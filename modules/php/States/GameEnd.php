<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\DeadMenPax\Game;

class GameEnd extends GameState
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
            id: 99,
            type: StateType::MANAGER,
            name: "gameEnd",
            description: "End of game",
            action: "stGameEnd",
            transitions: [],
        );
    }

    /**
     * Called when entering the game state.
     *
     * @return string
     */
    function onEnteringState(): string {
        // Calculate final scores and determine win/loss
        $this->game->calculateFinalScores();
        
        // End the game
        $this->game->endGame();
        
        return "";
    }
}
