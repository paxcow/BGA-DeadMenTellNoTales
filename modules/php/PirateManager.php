<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;

/**
 * Manages pirate state, actions, and movement for Dead Men Pax
 */
class PirateManager
{
    private Table $game;

    public function __construct(Table $game)
    {
        $this->game = $game;
    }

    /**
     * Get pirate's current location
     */
    public function getPirateLocation(int $playerId): array
    {
        $player = $this->game->getObjectFromDB(
            "SELECT player_room_x, player_room_y, player_is_on_ship FROM player WHERE player_id = $playerId"
        );
        
        return [
            'x' => (int)$player['player_room_x'],
            'y' => (int)$player['player_room_y'], 
            'is_on_ship' => (bool)$player['player_is_on_ship']
        ];
    }

    /**
     * Move pirate to new location
     */
    public function movePirate(int $playerId, int $toX, int $toY): bool
    {
        // Update database
        $this->game->DbQuery("UPDATE player SET player_room_x = $toX, player_room_y = $toY, player_is_on_ship = 1 WHERE player_id = $playerId");
        
        return true;
    }

    /**
     * Get remaining action tokens for player
     */
    public function getRemainingActions(int $playerId): int
    {
        $player = $this->game->getObjectFromDB(
            "SELECT player_actions_remaining FROM player WHERE player_id = $playerId"
        );
        
        return (int)$player['player_actions_remaining'];
    }

    /**
     * Spend one action token
     */
    public function spendAction(int $playerId): bool
    {
        $remaining = $this->getRemainingActions($playerId);
        
        if ($remaining <= 0) {
            return false;
        }
        
        $this->game->DbQuery("UPDATE player SET player_actions_remaining = player_actions_remaining - 1 WHERE player_id = $playerId");
        
        return true;
    }

    /**
     * Reset actions to maximum at start of turn
     */
    public function resetActions(int $playerId): void
    {
        $maxActions = $this->getMaxActions($playerId);
        $this->game->DbQuery("UPDATE player SET player_actions_remaining = $maxActions WHERE player_id = $playerId");
    }

    /**
     * Get maximum actions for player (5 normally, 6 for Lydia Lamore)
     */
    public function getMaxActions(int $playerId): int
    {
        $player = $this->game->getObjectFromDB(
            "SELECT player_max_actions FROM player WHERE player_id = $playerId"
        );
        
        return (int)$player['player_max_actions'];
    }

    /**
     * Check if player can perform an action
     */
    public function canPerformAction(int $playerId, string $action): bool
    {
        $remaining = $this->getRemainingActions($playerId);
        
        if ($remaining <= 0) {
            return false;
        }
        
        // Additional action-specific checks could go here
        // For example, checking if player has required items, fatigue level, etc.
        
        return true;
    }

    /**
     * Get player's fatigue level
     */
    public function getFatigueLevel(int $playerId): int
    {
        $player = $this->game->getObjectFromDB(
            "SELECT player_fatigue FROM player WHERE player_id = $playerId"
        );
        
        return (int)$player['player_fatigue'];
    }

    /**
     * Adjust player's fatigue level
     */
    public function adjustFatigue(int $playerId, int $change): void
    {
        $this->game->DbQuery("UPDATE player SET player_fatigue = GREATEST(0, player_fatigue + $change) WHERE player_id = $playerId");
        
        $newFatigue = $this->getFatigueLevel($playerId);
        
        // Notify fatigue change
        $this->game->notifyAllPlayers("fatigueChanged", clienttranslate('${player_name}\'s fatigue changes to ${fatigue}'), [
            "player_id" => $playerId,
            "player_name" => $this->game->getPlayerNameById($playerId),
            "fatigue" => $newFatigue,
            "change" => $change
        ]);
        
        // Check if player died from too much fatigue
        if ($newFatigue >= 12) {
            $this->killPirate($playerId, 'fatigue');
        }
    }

    /**
     * Get player's battle strength
     */
    public function getBattleStrength(int $playerId): int
    {
        $player = $this->game->getObjectFromDB(
            "SELECT player_battle_strength FROM player WHERE player_id = $playerId"
        );
        
        return (int)$player['player_battle_strength'];
    }

    /**
     * Adjust player's battle strength
     */
    public function adjustBattleStrength(int $playerId, int $change): void
    {
        $this->game->DbQuery("UPDATE player SET player_battle_strength = GREATEST(0, LEAST(12, player_battle_strength + $change)) WHERE player_id = $playerId");
        
        $newStrength = $this->getBattleStrength($playerId);
        
        // Notify battle strength change
        $this->game->notifyAllPlayers("battleStrengthChanged", clienttranslate('${player_name}\'s battle strength changes to ${strength}'), [
            "player_id" => $playerId,
            "player_name" => $this->game->getPlayerNameById($playerId),
            "battle_strength" => $newStrength,
            "change" => $change
        ]);
    }

    /**
     * Get all pirates at specific positions
     */
    public function getPiratesInPositions(array $positions): array
    {
        if (empty($positions)) {
            return [];
        }
        
        $conditions = [];
        foreach ($positions as $pos) {
            $conditions[] = "(player_room_x = {$pos[0]} AND player_room_y = {$pos[1]})";
        }
        
        $whereClause = implode(' OR ', $conditions);
        
        $pirates = $this->game->getCollectionFromDb(
            "SELECT player_id FROM player WHERE player_is_on_ship = 1 AND ($whereClause)"
        );
        
        return array_keys($pirates);
    }

    /**
     * Get all pirates at a specific position
     */
    public function getPiratesAt(int $x, int $y): array
    {
        return $this->getPiratesInPositions([[$x, $y]]);
    }

    /**
     * Handle explosion damage to pirate
     */
    public function handleExplosionDamage(int $playerId, string $explosionType): void
    {
        switch ($explosionType) {
            case 'fire':
                $this->adjustFatigue($playerId, 3); // Fire explosion damage
                break;
                
            case 'powder_keg':
                $this->killPirate($playerId, 'explosion'); // Powder keg kills instantly
                break;
        }
    }

    /**
     * Kill a pirate
     */
    public function killPirate(int $playerId, string $cause = 'unknown'): void
    {
        // Move pirate off the ship
        $this->game->DbQuery("UPDATE player SET player_is_on_ship = 0, player_room_x = -1, player_room_y = -1 WHERE player_id = $playerId");
        
        // Notify player death
        $this->game->notifyAllPlayers("playerDied", clienttranslate('${player_name} has died from ${cause}!'), [
            "player_id" => $playerId,
            "player_name" => $this->game->getPlayerNameById($playerId),
            "cause" => $cause,
            "i18n" => ['cause']
        ]);
        
        // Check if game should end due to too many deaths
        $alivePirates = $this->getAlivePirates();
        if (count($alivePirates) <= 1) {
            // Game ends - not enough pirates to continue
            $this->game->gamestate->nextState("gameEnd");
        }
    }

    /**
     * Check if pirate is alive
     */
    public function isPirateAlive(int $playerId): bool
    {
        $player = $this->game->getObjectFromDB(
            "SELECT player_is_on_ship FROM player WHERE player_id = $playerId"
        );
        
        return (bool)$player['player_is_on_ship'];
    }

    /**
     * Get all alive pirates
     */
    public function getAlivePirates(): array
    {
        $pirates = $this->game->getCollectionFromDb(
            "SELECT player_id FROM player WHERE player_is_on_ship = 1"
        );
        
        return array_keys($pirates);
    }

    /**
     * Handle fire damage in room
     */
    public function applyFireDamage(int $playerId, int $fireLevel): void
    {
        if ($fireLevel > 0) {
            $this->adjustFatigue($playerId, $fireLevel);
        }
    }

    /**
     * Set character-specific maximum actions (for Lydia Lamore)
     */
    public function setMaxActions(int $playerId, int $maxActions): void
    {
        $this->game->DbQuery("UPDATE player SET player_max_actions = $maxActions WHERE player_id = $playerId");
    }

    /**
     * Get player stats for client
     */
    public function getPlayerStats(int $playerId): array
    {
        $player = $this->game->getObjectFromDB(
            "SELECT player_fatigue, player_battle_strength, player_actions_remaining, player_max_actions, 
                    player_room_x, player_room_y, player_is_on_ship 
             FROM player WHERE player_id = $playerId"
        );
        
        return [
            'fatigue' => (int)$player['player_fatigue'],
            'battle_strength' => (int)$player['player_battle_strength'],
            'actions_remaining' => (int)$player['player_actions_remaining'],
            'max_actions' => (int)$player['player_max_actions'],
            'position' => [
                'x' => (int)$player['player_room_x'],
                'y' => (int)$player['player_room_y']
            ],
            'is_alive' => (bool)$player['player_is_on_ship']
        ];
    }
}
