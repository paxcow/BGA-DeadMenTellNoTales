<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\DeadMenPax\Game;
use BgaUserException;

class PlayerRetreat extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 9,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must select adjacent room to retreat to after losing battle'),
            descriptionMyTurn: clienttranslate('${you} must select adjacent room to retreat to after losing battle'),
            transitions: [
                'next'           => TakeActions::class,
                'skelitsRevenge' => SkelitsRevenge::class,
            ]
        );
    }

    public function getArgs(int $activePlayerId): array
    {
        $possibleRetreats = $this->game->getPossibleRetreatRooms($activePlayerId);
        
        return [
            'playerId' => $activePlayerId,
            'possibleRetreats' => $possibleRetreats
        ];
    }

    public function onEnteringState(int $activePlayerId): void
    {
        // Calculate possible retreat rooms
        $this->game->calculateRetreatOptions($activePlayerId);
    }

    #[PossibleAction]
    public function chooseRetreatRoom(int $roomId): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        
        // Validate room is in possible retreats
        $possibleRetreats = $this->game->getPossibleRetreatRooms($playerId);
        $validRetreat = false;
        
        foreach ($possibleRetreats as $retreat) {
            if ($retreat['roomId'] == $roomId) {
                $validRetreat = true;
                break;
            }
        }
        
        if (!$validRetreat) {
            throw new \BgaUserException("Cannot retreat to this room");
        }
        
        // Move player to selected room
        $this->game->getPirateManager()->movePirateToRoom($playerId, $roomId);
        
        // Apply fatigue from movement
        $this->game->getPirateManager()->adjustFatigue($playerId, 1, 'retreat');
        
        // Notify movement
        $this->game->notify->all("move", clienttranslate('${player_name} retreats'), [
            "playerId" => $playerId,
            "fromRoomId" => $this->game->getLastRoom($playerId),
            "toRoomId" => $roomId
        ]);
        
        // Check if player has action tokens remaining
        if ($this->game->getPirateManager()->getRemainingActions($playerId) > 0) {
            return 'next'; // Back to TakeActions
        } else {
            return 'skelitsRevenge'; // No tokens left
        }
    }
}
