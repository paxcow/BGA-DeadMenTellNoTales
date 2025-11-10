<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\DeadMenPax\Game;

class ResolveBattles extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 7,
            type: StateType::GAME,
            description: clienttranslate('Routes based on number of enemies in room'),
            transitions: [
                'battle'      => Battle::class,
                'chooseEnemy' => ChooseEnemy::class,
                'takeActions' => TakeActions::class,
            ]
        );
    }

    public function getArgs(): array
    {
        $playerId = (int) $this->game->getActivePlayerId();
        $enemies = $this->game->getEnemiesInCurrentRoom($playerId);

        return [
            'playerId' => $playerId,
            'enemyCount' => count($enemies),
            'enemies' => $enemies,
        ];
    }

    public function onEnteringState(): string
    {
        $playerId = (int) $this->game->getActivePlayerId();
        $enemies = $this->game->getEnemiesInCurrentRoom($playerId);

        if (empty($enemies)) {
            $this->game->clearBattleContext($playerId);
            return TakeActions::class;
        }

        if (count($enemies) === 1) {
            $this->game->setBattleContext($playerId, [
                'enemyTokenId' => $enemies[0]['id'],
                'roomId' => $enemies[0]['room_id'],
            ]);
            return Battle::class;
        }

        $this->game->setBattleContext($playerId, [
            'roomId' => $enemies[0]['room_id'],
        ]);

        return ChooseEnemy::class;
    }
}
