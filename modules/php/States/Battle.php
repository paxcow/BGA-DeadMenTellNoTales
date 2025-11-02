<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\PossibleAction;
use Bga\Games\DeadMenPax\Game;

class Battle extends GameState
{
    /**
     * Constructor.
     *
     * @param Game $game The game instance.
     */
    function __construct(
        protected Game $game,
    ) {
        parent::__construct($game,
            id: 20,
            type: StateType::ACTIVE_PLAYER,
            name: "battle",
            description: clienttranslate('${actplayer} must battle enemies'),
            descriptionMyTurn: clienttranslate('${you} must battle ${enemy_type} (strength ${enemy_strength})'),
            transitions: [
                "continueActions" => TakeActions::class,
                "skelitsRevenge" => SkelitsRevenge::class,
                "gameEnd" => GameEnd::class
            ],
        );
    }

    /**
     * Gets the arguments for the game state.
     *
     * @param int $activePlayerId The active player ID.
     * @return array
     */
    public function getArgs(int $activePlayerId): array
    {
        $enemy = $this->game->getEnemyInRoom($activePlayerId);
        return [
            'player_id' => $activePlayerId,
            'enemy_type' => $enemy['type'],
            'enemy_strength' => $enemy['strength'],
            'player_battle_strength' => $this->game->getPlayerBattleStrength($activePlayerId),
        ];
    }

    /**
     * Called when entering the game state.
     *
     * @param int $activePlayerId The active player ID.
     * @return string
     */
    function onEnteringState(int $activePlayerId): string {
        // Automatically start battle if player has treasure (must drop it first)
        if ($this->game->playerHasTreasure($activePlayerId)) {
            $this->game->dropTreasure($activePlayerId);
        }
        
        return "";
    }

    /**
     * Performs the battle action.
     *
     * @param int $activePlayerId The active player ID.
     * @return string
     */
    #[Bga\Games\DeadMenPax\PossibleAction]
    public function actBattle(int $activePlayerId): string
    {
        $battleResult = $this->game->performBattle($activePlayerId);
        
        if ($battleResult['won']) {
            // Check if player died from fatigue after battle
            if ($this->game->isPlayerDead($activePlayerId)) {
                return GameEnd::class;
            }
            
            // Check if there are more enemies in the room
            if ($this->game->hasEnemiesInRoom($activePlayerId)) {
                return ""; // Stay in battle state for next enemy
            }
            
            // Check if player has more action tokens
            if ($this->game->getPlayerActionTokens($activePlayerId) > 0) {
                return TakeActions::class;
            } else {
                return SkelitsRevenge::class;
            }
        } else {
            // Lost battle - handle retreat or continue fighting
            if ($battleResult['retreat']) {
                if ($this->game->getPlayerActionTokens($activePlayerId) > 0) {
                    return TakeActions::class;
                } else {
                    return SkelitsRevenge::class;
                }
            } else {
                return ""; // Continue fighting same enemy
            }
        }
    }
}
