<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\DeadMenPax\Game;
use BgaUserException;

class ChooseEnemy extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 8,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must select which enemy to fight when multiple are present'),
            descriptionMyTurn: clienttranslate('${you} must select which enemy to fight when multiple are present'),
            transitions: [
                'battle' => Battle::class
            ]
        );
    }

    public function getArgs(int $activePlayerId): array
    {
        $enemies = $this->game->getEnemiesInCurrentRoom($activePlayerId);
        $availableEnemies = array_map(static function (array $enemy) {
            return [
                'tokenId' => $enemy['id'],
                'tokenType' => $enemy['type'],
                'strength' => $enemy['strength'],
            ];
        }, $enemies);

        return [
            'playerId' => $activePlayerId,
            'availableEnemies' => $availableEnemies
        ];
    }

    public function onEnteringState(int $activePlayerId): void
    {
        // Get enemies in room and set up for selection
        $this->game->initializeEnemySelection($activePlayerId);
    }

    #[PossibleAction]
    public function selectEnemy(string $enemyId): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        
        // Validate enemy is in current room
        $enemies = $this->game->getEnemiesInCurrentRoom($playerId);
        $enemyValid = false;
        foreach ($enemies as $enemy) {
            if ($enemy['id'] === $enemyId) {
                $enemyValid = true;
                break;
            }
        }
        
        if (!$enemyValid) {
            throw new \BgaUserException("Invalid enemy selection");
        }
        
        // Set selected enemy as current battle target
        $this->game->setCurrentBattleEnemy($playerId, $enemyId);
        
        // Notify enemy selection
        $this->game->notify->all("enemySelected", clienttranslate('${player_name} selects enemy to fight'), [
            "playerId" => $playerId,
            "enemyId" => $enemyId
        ]);
        
        return "battle";
    }
}
