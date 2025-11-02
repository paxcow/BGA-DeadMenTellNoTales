<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\DeadMenPax\Game;

class PlayerTurn extends GameState
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
            id: 2,
            type: StateType::ACTIVE_PLAYER,
            name: "playerTurn",
            description: clienttranslate('${actplayer} must take their turn'),
            descriptionMyTurn: clienttranslate('${you} must take your turn: Search the Ship, Take Actions, then Skelit\'s Revenge'),
            transitions: [
                "searchShip" => SearchShip::class,
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
        return [
            'player_id' => $activePlayerId,
            'fatigue' => $this->game->getPlayerFatigue($activePlayerId),
            'battle_strength' => $this->game->getPlayerBattleStrength($activePlayerId),
        ];
    }

    /**
     * Called when entering the game state.
     *
     * @param int $activePlayerId The active player ID.
     * @return string
     */
    function onEnteringState(int $activePlayerId): string {
        // Check if game should end
        if ($this->game->checkGameEnd()) {
            return GameEnd::class;
        }
        
        // Start the player's turn with Search the Ship phase
        return SearchShip::class;
    }
}
