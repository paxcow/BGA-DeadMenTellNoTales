<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\DeadMenPax\Game;

class SkelitsRevenge extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 6,
            type: StateType::GAME,
            description: clienttranslate('${actplayer} resolves Skelit\'s Revenge card effects'),
            descriptionMyTurn: clienttranslate('${you} resolve Skelit\'s Revenge card effects'),
            transitions: [
                'next'           => PlayerTurn::class,
                'resolveBattles' => ResolveBattles::class,
                'gameEnd'        => GameEnd::class,
            ]
        );
    }

    public function getArgs(): array
    {
        return []; // TODO: provide card draw details
    }

    public function onEnteringState(): string
    {
        // TODO: implement Skelit's Revenge effects
        return PlayerTurn::class;
    }
}
