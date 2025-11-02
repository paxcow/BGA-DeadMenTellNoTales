<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\DeadMenPax\Game;

class SearchShip extends GameState
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
            id: 10,
            type: StateType::GAME,
            name: "searchShip",
            description: "Searching the ship...",
            action: "stSearchShip",
            transitions: [
                "takeActions" => TakeActions::class,
                "gameEnd" => GameEnd::class
            ],
        );
    }

    /**
     * Called when entering the game state.
     *
     * @param int $activePlayerId The active player ID.
     * @return string
     */
    function onEnteringState(int $activePlayerId): string {
        // Check if this is the first turn (place 2 tiles)
        $isFirstTurn = $this->game->isFirstTurn($activePlayerId);
        
        // Place room tile(s)
        if ($isFirstTurn) {
            $this->game->placeRoomTile($activePlayerId);
            $this->game->placeRoomTile($activePlayerId);
        } else {
            $this->game->placeRoomTile($activePlayerId);
        }
        
        // Check if we can still place tiles (game end condition)
        if (!$this->game->canPlaceMoreTiles()) {
            return GameEnd::class;
        }
        
        // Move to Take Actions phase
        return TakeActions::class;
    }
}
