<?php

namespace Bga\Games\DeadMenPax\Managers;

use Bga\Games\DeadMenPax\Models\PlayerModel;
use Bga\GameFramework\Table;

class PlayerDBManager extends DBManager
{
    /**
     * Constructor.
     *
     * @param Table $game The game instance.
     */
    public function __construct(Table $game)
    {
        parent::__construct('player', PlayerModel::class, $game);
    }

    /**
     * Gets all pirates in the given positions.
     *
     * @param array $positions The positions to check.
     * @return array A list of pirate IDs.
     */
    public function getPiratesInPositions(array $positions): array
    {
        if (empty($positions)) {
            return [];
        }
        
        $result = [];
        $allPlayers = $this->getAllObjects();
        
        foreach ($allPlayers as $player) {
            if (!$player->isOnShip) {
                continue; // Skip dead pirates
            }
            
            foreach ($positions as $pos) {
                if ($player->roomX === $pos[0] && $player->roomY === $pos[1]) {
                    $result[] = $player->id;
                    break; // Found match for this pirate, no need to check other positions
                }
            }
        }
        
        return $result;
    }

    /**
     * Gets all alive pirates.
     *
     * @return array A list of pirate IDs.
     */
    public function getAlivePirates(): array
    {
        $result = [];
        $allPlayers = $this->getAllObjects();
        
        foreach ($allPlayers as $player) {
            if ($player->isOnShip) {
                $result[] = $player->id;
            }
        }
        
        return $result;
    }

    /**
     * Creates player records in the database.
     *
     * @param array $players The players to create.
     * @param array $default_colors The default colors for the players.
     */
    public function createPlayers(array $players, array $default_colors): void
    {
        foreach ($players as $player_id => $player) {
            $playerModel = new PlayerModel();
            $playerModel->id = (int)$player_id;
            $playerModel->color = array_shift($default_colors) ?? '000000';
            $playerModel->canal = $player['player_canal'];
            $playerModel->name = $player['player_name'];
            $playerModel->avatar = $player['player_avatar'];
            $playerModel->playerNo = isset($player['player_no']) ? (int)$player['player_no'] : count($players);
            $playerModel->score = 0;
            $playerModel->fatigue = 0;
            $playerModel->battleStrength = 0;
            $playerModel->roomX = -1;
            $playerModel->roomY = -1;
            $playerModel->isOnShip = false;
            $playerModel->actionsRemaining = 5; // Default
            $playerModel->maxActions = 5; // Default
            $playerModel->extraActions = 0;
            $playerModel->characterCardId = null;
            $playerModel->itemCardId = null;
            $playerModel->currentEnemyTokenId = null;
            $playerModel->currentBattleRoomId = null;
            $playerModel->battleState = null;

            $this->saveObjectToDB($playerModel);
        }
    }
}
