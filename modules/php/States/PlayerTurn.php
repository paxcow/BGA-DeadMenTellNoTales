<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\DeadMenPax\Game;

class PlayerTurn extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 2,
            type: StateType::GAME
        );
    }

    public function getArgs(): array
    {
        return [];
    }

    public function onEnteringState(): string
    {
        $this->game->activeNextPlayer();
        return SearchShip::class;
    }
}
