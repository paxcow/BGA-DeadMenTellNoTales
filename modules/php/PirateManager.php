<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\DB\PlayerDBManager;

/**
 * Manages pirate state, actions, and movement for Dead Men Pax
 */
class PirateManager
{
    private Table $game;
    private PlayerDBManager $playerDBManager;
    
    // In-memory storage for pirate data
    private array $pirates = [];       // [playerId] => pirate object
    private bool $initialized = false;

    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->playerDBManager = new PlayerDBManager($game);
        $this->initFromDatabase();
    }

    /**
     * Initialize pirate data from database into memory
     */
    public function initFromDatabase(): void
    {
        // Clear existing data
        $this->pirates = [];
        
        // Load all player/pirate data from database
        $allPlayers = $this->playerDBManager->getAllObjects();
        
        foreach ($allPlayers as $player) {
            $this->pirates[$player->id] = $player;
        }
        
        $this->initialized = true;
    }

    /**
     * Save a pirate's data to database
     */
    private function savePirate($pirate): void
    {
        $this->playerDBManager->saveObjectToDB($pirate);
        
        // Update in-memory copy
        $this->pirates[$pirate->id] = $pirate;
    }

    /**
     * Get pirate's current location
     */
    public function getPirateLocation(int $playerId): array
    {
        if (!isset($this->pirates[$playerId])) {
            return ['x' => -1, 'y' => -1, 'is_on_ship' => false];
        }
        
        $pirate = $this->pirates[$playerId];
        
        return [
            'x' => $pirate->roomX,
            'y' => $pirate->roomY, 
            'is_on_ship' => $pirate->isOnShip
        ];
    }

    /**
     * Move pirate to new location
     */
    public function movePirate(int $playerId, int $toX, int $toY): bool
    {
        if (!isset($this->pirates[$playerId])) {
            return false;
        }
        
        $pirate = $this->pirates[$playerId];
        $pirate->roomX = $toX;
        $pirate->roomY = $toY;
        $pirate->isOnShip = true;
        $this->savePirate($pirate);
        
        return true;
    }

    /**
     * Get remaining action tokens for player
     */
    public function getRemainingActions(int $playerId): int
    {
        if (!isset($this->pirates[$playerId])) {
            return 0;
        }
        
        return $this->pirates[$playerId]->actionsRemaining;
    }

    /**
     * Spend one action token
     */
    public function spendAction(int $playerId): bool
    {
        if (!isset($this->pirates[$playerId])) {
            return false;
        }
        
        $pirate = $this->pirates[$playerId];
        
        if ($pirate->actionsRemaining <= 0) {
            return false;
        }
        
        $pirate->actionsRemaining--;
        $this->savePirate($pirate);
        
        return true;
    }

    /**
     * Reset actions to maximum at start of turn
     */
    public function resetActions(int $playerId): void
    {
        if (!isset($this->pirates[$playerId])) {
            return;
        }
        
        $pirate = $this->pirates[$playerId];
        $pirate->actionsRemaining = $pirate->maxActions;
        $this->savePirate($pirate);
    }

    /**
     * Get maximum actions for player (5 normally, 6 for Lydia Lamore)
     */
    public function getMaxActions(int $playerId): int
    {
        if (!isset($this->pirates[$playerId])) {
            return 5; // Default value
        }
        
        return $this->pirates[$playerId]->maxActions;
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
        if (!isset($this->pirates[$playerId])) {
            return 0;
        }
        
        return $this->pirates[$playerId]->fatigue;
    }

    /**
     * Adjust player's fatigue level
     */
    public function adjustFatigue(int $playerId, int $change): void
    {
        if (!isset($this->pirates[$playerId])) {
            return;
        }
        
        $pirate = $this->pirates[$playerId];
        $pirate->fatigue = max(0, $pirate->fatigue + $change);
        $this->savePirate($pirate);
        
        // Notify fatigue change
        $this->game->notifyAllPlayers("fatigueChanged", clienttranslate('${player_name}\'s fatigue changes to ${fatigue}'), [
            "player_id" => $playerId,
            "player_name" => $this->game->getPlayerNameById($playerId),
            "fatigue" => $pirate->fatigue,
            "change" => $change
        ]);
        
        // Check if player died from too much fatigue
        if ($pirate->fatigue >= 12) {
            $this->killPirate($playerId, 'fatigue');
        }
    }

    /**
     * Get player's battle strength
     */
    public function getBattleStrength(int $playerId): int
    {
        if (!isset($this->pirates[$playerId])) {
            return 0;
        }
        
        return $this->pirates[$playerId]->battleStrength;
    }

    /**
     * Adjust player's battle strength
     */
    public function adjustBattleStrength(int $playerId, int $change): void
    {
        if (!isset($this->pirates[$playerId])) {
            return;
        }
        
        $pirate = $this->pirates[$playerId];
        $pirate->battleStrength = max(0, min(12, $pirate->battleStrength + $change));
        $this->savePirate($pirate);
        
        // Notify battle strength change
        $this->game->notifyAllPlayers("battleStrengthChanged", clienttranslate('${player_name}\'s battle strength changes to ${strength}'), [
            "player_id" => $playerId,
            "player_name" => $this->game->getPlayerNameById($playerId),
            "battle_strength" => $pirate->battleStrength,
            "change" => $change
        ]);
    }

    /**
     * Get all pirates at specific positions
     */
    public function getPiratesInPositions(array $positions): array
    {
        $result = [];
        
        foreach ($this->pirates as $playerId => $pirate) {
            if (!$pirate->isOnShip) {
                continue; // Skip dead pirates
            }
            
            foreach ($positions as $pos) {
                if ($pirate->roomX === $pos[0] && $pirate->roomY === $pos[1]) {
                    $result[] = $playerId;
                    break; // Found match for this pirate, no need to check other positions
                }
            }
        }
        
        return $result;
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
        if (!isset($this->pirates[$playerId])) {
            return;
        }
        
        $pirate = $this->pirates[$playerId];
        $pirate->isOnShip = false;
        $pirate->roomX = -1;
        $pirate->roomY = -1;
        $this->savePirate($pirate);
        
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
        if (!isset($this->pirates[$playerId])) {
            return false;
        }
        
        return $this->pirates[$playerId]->isOnShip;
    }

    /**
     * Get all alive pirates
     */
    public function getAlivePirates(): array
    {
        $alivePirates = [];
        
        foreach ($this->pirates as $playerId => $pirate) {
            if ($pirate->isOnShip) {
                $alivePirates[] = $playerId;
            }
        }
        
        return $alivePirates;
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
        if (!isset($this->pirates[$playerId])) {
            return;
        }
        
        $pirate = $this->pirates[$playerId];
        $pirate->maxActions = $maxActions;
        $this->savePirate($pirate);
    }

    /**
     * Assign an item to a player
     */
    public function assignItem(int $playerId, ?int $itemId): void
    {
        if (!isset($this->pirates[$playerId])) {
            return;
        }
        
        $pirate = $this->pirates[$playerId];
        $pirate->itemCardId = $itemId;
        $this->savePirate($pirate);
    }

    /**
     * Get player stats for client
     */
    public function getPlayerStats(int $playerId): array
    {
        if (!isset($this->pirates[$playerId])) {
            return [
                'fatigue' => 0,
                'battle_strength' => 0,
                'actions_remaining' => 0,
                'max_actions' => 5,
                'position' => ['x' => -1, 'y' => -1],
                'is_alive' => false
            ];
        }
        
        $pirate = $this->pirates[$playerId];
        
        return [
            'fatigue' => $pirate->fatigue,
            'battle_strength' => $pirate->battleStrength,
            'actions_remaining' => $pirate->actionsRemaining,
            'max_actions' => $pirate->maxActions,
            'position' => [
                'x' => $pirate->roomX,
                'y' => $pirate->roomY
            ],
            'is_alive' => $pirate->isOnShip
        ];
    }
}
