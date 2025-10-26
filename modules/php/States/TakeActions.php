<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\DeadMenPax\Game;

class TakeActions extends GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct($game,
            id: 11,
            type: StateType::ACTIVE_PLAYER,
            name: "takeActions",
            description: clienttranslate('${actplayer} must take pirate actions'),
            descriptionMyTurn: clienttranslate('${you} must take pirate actions (${action_tokens} tokens remaining)'),
            transitions: [
                "skelitsRevenge" => SkelitsRevenge::class,
                "battle" => Battle::class,
                "gameEnd" => GameEnd::class
            ],
        );
    }

    public function getArgs(int $activePlayerId): array
    {
        return [
            'player_id' => $activePlayerId,
            'action_tokens' => $this->game->getPlayerActionTokens($activePlayerId),
            'available_actions' => $this->game->getAvailableActions($activePlayerId),
            'fatigue' => $this->game->getPlayerFatigue($activePlayerId),
            'battle_strength' => $this->game->getPlayerBattleStrength($activePlayerId),
        ];
    }

    function onEnteringState(int $activePlayerId): string {
        // Reset action tokens for the turn
        $this->game->resetActionTokens($activePlayerId);
        
        // Check if player encounters enemies immediately
        if ($this->game->hasEnemiesInRoom($activePlayerId)) {
            return Battle::class;
        }
        
        return "";
    }

    #[PossibleAction]
    public function actWalk(int $targetX, int $targetY, int $activePlayerId): string
    {
        $this->game->performWalk($activePlayerId, $targetX, $targetY);
        return $this->checkTurnEnd($activePlayerId);
    }

    #[PossibleAction]
    public function actRun(int $targetX, int $targetY, int $activePlayerId): string
    {
        $this->game->performRun($activePlayerId, $targetX, $targetY);
        return $this->checkTurnEnd($activePlayerId);
    }

    #[PossibleAction]
    public function actFightFire(int $activePlayerId): string
    {
        $this->game->performFightFire($activePlayerId);
        return $this->checkTurnEnd($activePlayerId);
    }

    #[PossibleAction]
    public function actEliminateDeckhand(int $targetX, int $targetY, int $activePlayerId): string
    {
        $this->game->performEliminateDeckhand($activePlayerId, $targetX, $targetY);
        return $this->checkTurnEnd($activePlayerId);
    }

    #[PossibleAction]
    public function actPickupToken(string $tokenId, int $activePlayerId): string
    {
        $this->game->performPickupToken($activePlayerId, $tokenId);
        return $this->checkTurnEnd($activePlayerId);
    }

    #[PossibleAction]
    public function actRest(int $activePlayerId): string
    {
        $this->game->performRest($activePlayerId);
        return $this->checkTurnEnd($activePlayerId);
    }

    #[PossibleAction]
    public function actIncreaseBattleStrength(int $activePlayerId): string
    {
        $this->game->performIncreaseBattleStrength($activePlayerId);
        return $this->checkTurnEnd($activePlayerId);
    }

    #[PossibleAction]
    public function actSwapItem(int $newItemId, int $activePlayerId): string
    {
        $this->game->performSwapItem($activePlayerId, $newItemId);
        return $this->checkTurnEnd($activePlayerId);
    }

    #[PossibleAction]
    public function actEndTurn(int $activePlayerId): string
    {
        return SkelitsRevenge::class;
    }

    private function checkTurnEnd(int $activePlayerId): string
    {
        // Check if player encounters enemies after action
        if ($this->game->hasEnemiesInRoom($activePlayerId)) {
            return Battle::class;
        }

        // Check if player has no more action tokens or chooses to end turn
        if ($this->game->getPlayerActionTokens($activePlayerId) <= 0) {
            return SkelitsRevenge::class;
        }

        return "";
    }
}
