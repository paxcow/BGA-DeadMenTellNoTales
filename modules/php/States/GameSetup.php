<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\DeadMenPax\Game;

class GameSetup extends GameState
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
            id: 1,
            type: StateType::MANAGER,
            name: "gameSetup",
            description: "Game setup",
            action: "stGameSetup",
            transitions: [
                "start" => PlayerTurn::class
            ],
        );
    }

    /**
     * Called when entering the game state.
     *
     * @return string
     */
    function onEnteringState(): string {
        // Initialize game setup
        $this->game->initializeGame();
        return PlayerTurn::class;
    }
}
