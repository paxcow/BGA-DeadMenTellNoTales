<?php

namespace Bga\Games\DeadMenPax\DB;

use Bga\Games\DeadMenPax\DB\Models\PlayerModel;
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
}
