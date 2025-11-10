<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\DeadMenPax\Game;

class GameEnd extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 99,
            type: StateType::GAME,
            description: clienttranslate('Terminal state - game over')
        );
    }

    public function getArgs(): array
    {
        return []; // TODO: provide end-of-game summary
    }
}
