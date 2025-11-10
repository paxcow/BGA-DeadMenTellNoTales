<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\DeadMenPax\Game;
use BgaUserException;

class TakeActions extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 4,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must spend action tokens'),
            descriptionMyTurn: clienttranslate('${you} must spend action tokens'),
            transitions: [
                'next'            => SkelitsRevenge::class,
                'resolveBattles'  => ResolveBattles::class,
                'itemSwapChoice'  => ItemSwapChoice::class,
                'gameEnd'         => GameEnd::class,
            ]
        );
    }

    public function getArgs(int $activePlayerId): array
    {
        $player = $this->game->getPlayerById($activePlayerId);
        $currentRoom = $this->game->getPlayerCurrentRoom($activePlayerId);
        $availableActions = $this->game->getAvailableActions($activePlayerId);
        
        return [
            'playerId' => $activePlayerId,
            'actionTokensRemaining' => $player['action_tokens'],
            'currentRoomId' => $currentRoom,
            'possibleActions' => array_keys($availableActions),
            'walkTargets' => $this->game->getWalkTargets($activePlayerId),
            'runTargets' => $this->game->getRunTargets($activePlayerId),
            'pickupableTokens' => $this->game->getPickupableTokens($activePlayerId),
            'swappableItems' => $this->game->getSwappableItems($activePlayerId),
            'carryingTreasure' => $this->game->isCarryingTreasure($activePlayerId),
            'canExitShip' => $this->game->canExitShip($activePlayerId)
        ];
    }

    public function onEnteringState(int $activePlayerId): void
    {
        // Initialize player's action tokens for the turn
        $this->game->initializePlayerTurn($activePlayerId);
    }

    #[PossibleAction]
    public function walk(int $roomId): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $this->assertActionAvailable($playerId);
        
        // Validate room is in walk targets
        $walkTargets = $this->game->getWalkTargets($playerId);
        if (!in_array($roomId, $walkTargets)) {
            throw new \BgaUserException("Cannot walk to this room");
        }
        
        // Move player to room
        $this->game->getPirateManager()->movePirateToRoom($playerId, $roomId);
        
        // Apply fatigue from fire
        $room = $this->game->getBoardManager()->getRoomById($roomId);
        if ($room && $room->getFireLevel() > 0) {
            $this->game->getPirateManager()->adjustFatigue($playerId, $room->getFireLevel(), 'entering a burning room');
        }

        $this->consumeAction($playerId);
        
        // Check for enemies in room
        $enemies = $this->game->getTokenManager()->getEnemiesInRoom($roomId);
        if (!empty($enemies)) {
            return 'resolveBattles';
        }
        
        return 'next';
    }

    #[PossibleAction]
    public function run(int $roomId): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $this->assertActionAvailable($playerId);
        
        // Validate room is in run targets
        $runTargets = $this->game->getRunTargets($playerId);
        if (!in_array($roomId, $runTargets)) {
            throw new \BgaUserException("Cannot run to this room");
        }
        
        // Move player through path (2 rooms)
        $path = $this->game->getRunPath($playerId, $roomId);
        foreach ($path as $intermediateRoomId) {
            $this->game->getPirateManager()->movePirateToRoom($playerId, $intermediateRoomId);
            $this->game->getPirateManager()->adjustFatigue($playerId, 1, 'running');
        }
        
        // Apply +2 run fatigue
        $this->game->getPirateManager()->adjustFatigue($playerId, 2, 'running');

        $this->consumeAction($playerId);
        
        // Check for enemies in final room
        $enemies = $this->game->getTokenManager()->getEnemiesInRoom($roomId);
        if (!empty($enemies)) {
            return 'resolveBattles';
        }
        
        return 'next';
    }

    #[PossibleAction]
    public function fightFire(): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $this->assertActionAvailable($playerId);
        
        // Get current room
        $currentRoom = $this->game->getPirateManager()->getPirateRoom($playerId);
        $room = $this->game->getBoardManager()->getRoomById($currentRoom);
        
        if (!$room || $room->getFireLevel() <= 0) {
            throw new \BgaUserException("No fire to fight in current room");
        }
        
        // Decrease fire level
        $oldLevel = $room->getFireLevel();
        $room->decreaseFireLevel(1);
        $this->game->getBoardManager()->saveToDatabase($room);
        
        $this->consumeAction($playerId);
        
        // Notify fire level change
        $this->game->notify->all("fireLevelChange", clienttranslate('${player_name} fights fire'), [
            "playerId" => $playerId,
            "changes" => [[
                "roomId" => $currentRoom,
                "oldLevel" => $oldLevel,
                "newLevel" => $room->getFireLevel()
            ]]
        ]);
        
        return 'next';
    }

    #[PossibleAction]
    public function eliminateDeckhand(int $roomId): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $this->assertActionAvailable($playerId);
        
        // Validate room is current or adjacent
        $currentRoom = $this->game->getPirateManager()->getPirateRoom($playerId);
        $adjacentRooms = $this->game->getBoardManager()->getAdjacentRooms($currentRoom);
        $validRooms = array_merge([$currentRoom], $adjacentRooms);
        
        if (!in_array($roomId, $validRooms)) {
            throw new \BgaUserException("Cannot eliminate deckhand in this room");
        }
        
        // Remove one deckhand from room
        $deckhandCount = $this->game->getTokenManager()->getDeckhandCount($roomId);
        if ($deckhandCount <= 0) {
            throw new \BgaUserException("No deckhands in this room");
        }
        
        $this->game->getTokenManager()->removeDeckhand($roomId, 1);
        
        $this->consumeAction($playerId);
        
        // Notify deckhand change
        $this->game->notify->all("deckhandChange", clienttranslate('${player_name} eliminates a deckhand'), [
            "playerId" => $playerId,
            "changes" => [[
                "roomId" => $roomId,
                "oldCount" => $deckhandCount,
                "newCount" => $deckhandCount - 1
            ]]
        ]);
        
        return 'next';
    }

    #[PossibleAction]
    public function pickupToken(int $tokenId): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $this->assertActionAvailable($playerId);
        
        // Validate token is pickupable
        $pickupableTokens = $this->game->getPickupableTokens($playerId);
        $tokenAvailable = false;
        foreach ($pickupableTokens as $token) {
            if ($token['tokenId'] == $tokenId) {
                $tokenAvailable = true;
                break;
            }
        }
        
        if (!$tokenAvailable) {
            throw new \BgaUserException("Cannot pick up this token");
        }
        
        // Move token to player inventory
        $this->game->getTokenManager()->moveTokenToPlayer($tokenId, $playerId);
        
        $this->consumeAction($playerId);
        
        // Notify token change
        $this->game->notify->all("tokenChange", clienttranslate('${player_name} picks up a token'), [
            "playerId" => $playerId,
            "tokenId" => $tokenId,
            "fromLocation" => "room",
            "toLocation" => "player"
        ]);
        
        return 'next';
    }

    #[PossibleAction]
    public function dropToken(int $tokenId): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        
        // Validate player has the token
        if (!$this->game->getTokenManager()->doesPlayerHaveToken($playerId, $tokenId)) {
            throw new \BgaUserException("You don't have this token");
        }
        
        // Move token to current room
        $currentRoom = $this->game->getPirateManager()->getPirateRoom($playerId);
        $this->game->getTokenManager()->moveTokenToRoom($tokenId, $currentRoom);
        
        // Notify token change
        $this->game->notify->all("tokenChange", clienttranslate('${player_name} drops a token'), [
            "playerId" => $playerId,
            "tokenId" => $tokenId,
            "fromLocation" => "player",
            "toLocation" => "room"
        ]);
        
        return 'next';
    }

    #[PossibleAction]
    public function rest(): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $this->assertActionAvailable($playerId);
        
        // Reduce fatigue by 2
        $player = $this->game->requirePirate($playerId);
        $oldFatigue = $player->fatigue;
        $player->fatigue = max(0, $player->fatigue - 2);
        $this->game->getPirateManager()->persistPirate($player);

        $this->consumeAction($playerId);
        
        // Notify fatigue change
        $this->game->notify->all("fatigueChange", clienttranslate('${player_name} rests'), [
            "playerId" => $playerId,
            "oldFatigue" => $oldFatigue,
            "newFatigue" => $player->fatigue,
            "reason" => "rest"
        ]);
        
        return 'next';
    }

    #[PossibleAction]
    public function increaseBattleStrength(): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $this->assertActionAvailable($playerId);
        
        $player = $this->game->requirePirate($playerId);
        $oldPosition = $player->battleStrength;
        if ($oldPosition >= 4) {
            throw new \BgaUserException("Battle track already at maximum");
        }

        $this->game->getPirateManager()->adjustBattleStrength($playerId, 1);
        $this->consumeAction($playerId);
        
        // Notify battle track change
        $this->game->notify->all("battleTrackChange", clienttranslate('${player_name} increases battle strength'), [
            "playerId" => $playerId,
            "oldPosition" => $oldPosition,
            "newPosition" => $oldPosition + 1
        ]);
        
        return 'next';
    }

    #[PossibleAction]
    public function swapItem(int $itemId, int $sourcePlayerId = 0): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $this->assertActionAvailable($playerId);
        
        // Validate item is available
        if (!$this->game->getItemManager()->isItemAvailable($itemId, $sourcePlayerId)) {
            throw new \BgaUserException("Item not available");
        }
        
        // Get player's current item
        $currentItemId = $this->game->requirePirate($playerId)->itemCardId;
        
        // Perform the swap
        $this->game->getItemManager()->swapItem($playerId, $itemId, $currentItemId, $sourcePlayerId);
        
        $this->consumeAction($playerId);
        
        // Notify item swap
        $this->game->notify->all("itemSwap", clienttranslate('${player_name} swaps item'), [
            "playerId" => $playerId,
            "oldItemId" => $currentItemId,
            "newItemId" => $itemId,
            "itemPlacedOn" => $sourcePlayerId > 0 ? "player" : "table"
        ]);
        
        // If taken from another player, go to item swap choice
        return $sourcePlayerId > 0 ? 'itemSwapChoice' : 'next';
    }

    #[PossibleAction]
    public function exitShip(): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        
        // Validate player can exit ship
        if (!$this->game->canExitShip($playerId)) {
            throw new \BgaUserException("Cannot exit ship from current position");
        }
        
        // Handle treasure if carrying
        $treasureLooted = false;
        $treasureId = null;
        if ($this->game->isCarryingTreasure($playerId)) {
            $treasure = $this->game->getTokenManager()->getPlayerTreasure($playerId);
            $treasureLooted = true;
            $treasureId = $treasure['token_id'];
            $this->game->placeTreasureOnDinghy($treasureId);
        }
        
        // Remove pirate from board
        $this->game->getPirateManager()->removePirateFromBoard($playerId);
        
        // Reduce fatigue by half
        $player = $this->game->requirePirate($playerId);
        $oldFatigue = $player->fatigue;
        $player->fatigue = (int) ceil($player->fatigue / 2);
        $this->game->getPirateManager()->persistPirate($player);
        
        // Check win condition
        if ($this->game->checkWinCondition()) {
            return 'gameEnd';
        }
        
        // Notify exit
        $this->game->notify->all("exitShip", clienttranslate('${player_name} exits the ship'), [
            "playerId" => $playerId,
            "treasureLooted" => $treasureLooted,
            "treasureId" => $treasureId,
            "totalTreasuresLooted" => $this->game->getTreasuresLooted(),
            "fatigueReduced" => $oldFatigue - $player->fatigue
        ]);
        
        return 'next';
    }

    #[PossibleAction]
    public function pass(): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        
        // Store unused base action tokens only
        $pirate = $this->game->getPirateManager()->getPirate($playerId);
        $unusedTokens = $pirate ? max(0, $pirate->actionsRemaining) : 0;
        $nextPlayerId = (int)$this->game->getPlayerAfter($playerId);

        if ($unusedTokens > 0) {
            $this->game->storePassedTokens($nextPlayerId, $unusedTokens);
        } else {
            $this->game->clearPassedTokens($nextPlayerId);
        }

        // Reset any extra actions held by the departing player
        $this->game->clearPassedTokens($playerId);
        
        return 'next';
    }

    private function assertActionAvailable(int $playerId): void
    {
        if ($this->game->getPirateManager()->getRemainingActions($playerId) <= 0) {
            throw new \BgaUserException("No action tokens remaining");
        }
    }

    private function consumeAction(int $playerId): void
    {
        if (!$this->game->getPirateManager()->spendAction($playerId)) {
            throw new \BgaUserException("No action tokens remaining");
        }
    }
}
