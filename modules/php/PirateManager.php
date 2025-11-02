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

    /**
     * Constructor.
     *
     * @param Table $game The game instance.
     */
    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->playerDBManager = new PlayerDBManager($game);
        $this->initFromDatabase();
    }

    /**
     * Initializes pirate data from the database into memory.
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
     * Saves a pirate's data to the database.
     *
     * @param mixed $pirate The pirate object to save.
     */
    private function savePirate($pirate): void
    {
        $this->playerDBManager->saveObjectToDB($pirate);
        
        // Update in-memory copy
        $this->pirates[$pirate->id] = $pirate;
    }

    /**
     * Gets a pirate's current location.
     *
     * @param int $playerId The ID of the player.
     * @return array The pirate's location.
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
     * Moves a pirate to a new location.
     *
     * @param int $playerId The ID of the player.
     * @param int $toX The target x-coordinate.
     * @param int $toY The target y-coordinate.
     * @return bool True on success, false on failure.
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
     * Gets the remaining action tokens for a player.
     *
     * @param int $playerId The ID of the player.
     * @return int The number of remaining actions.
     */
    public function getRemainingActions(int $playerId): int
    {
        if (!isset($this->pirates[$playerId])) {
            return 0;
        }
        
        return $this->pirates[$playerId]->actionsRemaining;
    }

    /**
     * Spends one action token.
     *
     * @param int $playerId The ID of the player.
     * @return bool True on success, false on failure.
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
     * Resets actions to the maximum at the start of a turn.
     *
     * @param int $playerId The ID of the player.
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
     * Gets the maximum actions for a player.
     *
     * @param int $playerId The ID of the player.
     * @return int The maximum number of actions.
     */
    public function getMaxActions(int $playerId): int
    {
        if (!isset($this->pirates[$playerId])) {
            return 5; // Default value
        }
        
        return $this->pirates[$playerId]->maxActions;
    }

    /**
     * Checks if a player can perform an action.
     *
     * @param int $playerId The ID of the player.
     * @param string $action The action to check.
     * @return bool True if the action can be performed, false otherwise.
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
     * Gets a player's fatigue level.
     *
     * @param int $playerId The ID of the player.
     * @return int The fatigue level.
     */
    public function getFatigueLevel(int $playerId): int
    {
        if (!isset($this->pirates[$playerId])) {
            return 0;
        }
        
        return $this->pirates[$playerId]->fatigue;
    }

    /**
     * Adjusts a player's fatigue level.
     *
     * @param int $playerId The ID of the player.
     * @param int $change The amount to change the fatigue level by.
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
     * Gets a player's battle strength.
     *
     * @param int $playerId The ID of the player.
     * @return int The battle strength.
     */
    public function getBattleStrength(int $playerId): int
    {
        if (!isset($this->pirates[$playerId])) {
            return 0;
        }
        
        return $this->pirates[$playerId]->battleStrength;
    }

    /**
     * Adjusts a player's battle strength.
     *
     * @param int $playerId The ID of the player.
     * @param int $change The amount to change the battle strength by.
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
     * Gets all pirates at specific positions.
     *
     * @param array $positions The positions to check.
     * @return array An array of pirate IDs.
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
     * Gets all pirates at a specific position.
     *
     * @param int $x The x-coordinate.
     * @param int $y The y-coordinate.
     * @return array An array of pirate IDs.
     */
    public function getPiratesAt(int $x, int $y): array
    {
        return $this->getPiratesInPositions([[$x, $y]]);
    }

    /**
     * Handles explosion damage to a pirate.
     *
     * @param int $playerId The ID of the player.
     * @param string $explosionType The type of explosion.
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
     * Kills a pirate.
     *
     * @param int $playerId The ID of the player.
     * @param string $cause The cause of death.
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
     * Checks if a pirate is alive.
     *
     * @param int $playerId The ID of the player.
     * @return bool True if the pirate is alive, false otherwise.
     */
    public function isPirateAlive(int $playerId): bool
    {
        if (!isset($this->pirates[$playerId])) {
            return false;
        }
        
        return $this->pirates[$playerId]->isOnShip;
    }

    /**
     * Gets all alive pirates.
     *
     * @return array An array of alive pirate IDs.
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
     * Handles fire damage in a room.
     *
     * @param int $playerId The ID of the player.
     * @param int $fireLevel The fire level of the room.
     */
    public function applyFireDamage(int $playerId, int $fireLevel): void
    {
        if ($fireLevel > 0) {
            $this->adjustFatigue($playerId, $fireLevel);
        }
    }

    /**
     * Sets character-specific maximum actions.
     *
     * @param int $playerId The ID of the player.
     * @param int $maxActions The maximum number of actions.
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
     * Assigns an item to a player.
     *
     * @param int $playerId The ID of the player.
     * @param int|null $itemId The ID of the item.
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
     * Gets player stats for the client.
     *
     * @param int $playerId The ID of the player.
     * @return array An array of player stats.
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
