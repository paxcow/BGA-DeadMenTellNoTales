<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;

/**
 * Manages tokens (treasure, skeleton crew, etc.) for Dead Men Pax
 */
class TokenManager
{
    private Table $game;
    
    // In-memory storage
    private array $tokens = [];              // [tokenId] => token data
    private array $tokensByLocation = [];    // [location][locationArg] => [tokenIds]
    private bool $initialized = false;
    
    // Token types
    public const TOKEN_TYPE_TREASURE_GUARD = 'treasure_guard';
    public const TOKEN_TYPE_SKELETON_CUTLASS = 'skeleton_cutlass';
    public const TOKEN_TYPE_SKELETON_GROG = 'skeleton_grog';
    public const TOKEN_TYPE_TRAPDOOR = 'trapdoor';
    public const TOKEN_TYPE_CAPTAIN_FROMM = 'captain_fromm';
    
    // Token locations
    public const LOCATION_BAG = 'bag';
    public const LOCATION_ROOM = 'room';
    public const LOCATION_PLAYER = 'player';
    public const LOCATION_REMOVED = 'removed';
    
    // Token states (for double-sided tokens)
    public const STATE_TREASURE = 0;
    public const STATE_GUARD = 1;
    public const STATE_CUTLASS = 0;
    public const STATE_GROG = 1;

    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->initFromDatabase();
    }

    /**
     * Initialize in-memory storage from database
     */
    public function initFromDatabase(): void
    {
        // Clear existing in-memory data
        $this->tokens = [];
        $this->tokensByLocation = [];
        
        // Load all tokens from database
        $allTokens = $this->game->getCollectionFromDb(
            "SELECT * FROM token WHERE token_location != '" . self::LOCATION_REMOVED . "'"
        );
        
        // Populate in-memory structures
        foreach ($allTokens as $token) {
            $tokenId = $token['token_id'];
            $this->tokens[$tokenId] = $token;
            
            // Index by location
            $location = $token['token_location'];
            $locationArg = $token['token_location_arg'];
            
            if (!isset($this->tokensByLocation[$location])) {
                $this->tokensByLocation[$location] = [];
            }
            if (!isset($this->tokensByLocation[$location][$locationArg])) {
                $this->tokensByLocation[$location][$locationArg] = [];
            }
            $this->tokensByLocation[$location][$locationArg][] = $tokenId;
        }
        
        $this->initialized = true;
    }

    /**
     * Save a token to the database
     */
    private function saveToken(array $token): void
    {
        $tokenId = $token['token_id'];
        $tokenType = $token['token_type'];
        $location = $token['token_location'];
        $locationArg = $token['token_location_arg'];
        $state = $token['token_state'];
        
        $this->game->DbQuery("UPDATE token SET 
            token_type = '$tokenType',
            token_location = '$location', 
            token_location_arg = '$locationArg', 
            token_state = $state 
            WHERE token_id = '$tokenId'");
    }

    /**
     * Update token location in memory and database
     */
    private function updateTokenLocation(string $tokenId, string $newLocation, $newLocationArg, ?int $newState = null): void
    {
        if (!isset($this->tokens[$tokenId])) {
            return;
        }
        
        $token = $this->tokens[$tokenId];
        
        // Remove from old location index
        $oldLocation = $token['token_location'];
        $oldLocationArg = $token['token_location_arg'];
        if (isset($this->tokensByLocation[$oldLocation][$oldLocationArg])) {
            $key = array_search($tokenId, $this->tokensByLocation[$oldLocation][$oldLocationArg]);
            if ($key !== false) {
                unset($this->tokensByLocation[$oldLocation][$oldLocationArg][$key]);
                $this->tokensByLocation[$oldLocation][$oldLocationArg] = array_values($this->tokensByLocation[$oldLocation][$oldLocationArg]);
            }
        }
        
        // Update token data
        $token['token_location'] = $newLocation;
        $token['token_location_arg'] = $newLocationArg;
        if ($newState !== null) {
            $token['token_state'] = $newState;
        }
        $this->tokens[$tokenId] = $token;
        
        // Add to new location index
        if (!isset($this->tokensByLocation[$newLocation])) {
            $this->tokensByLocation[$newLocation] = [];
        }
        if (!isset($this->tokensByLocation[$newLocation][$newLocationArg])) {
            $this->tokensByLocation[$newLocation][$newLocationArg] = [];
        }
        $this->tokensByLocation[$newLocation][$newLocationArg][] = $tokenId;
        
        // Save to database
        $this->saveToken($token);
    }

    /**
     * Place a token in a specific room
     */
    public function placeTokenInRoom(string $tokenId, int $x, int $y): void
    {
        $locationKey = $this->getLocationKey($x, $y);
        $this->updateTokenLocation($tokenId, self::LOCATION_ROOM, $locationKey);
    }

    /**
     * Get all tokens in a specific room
     */
    public function getTokensInRoom(int $x, int $y): array
    {
        $locationKey = $this->getLocationKey($x, $y);
        
        if (!isset($this->tokensByLocation[self::LOCATION_ROOM][$locationKey])) {
            return [];
        }
        
        $result = [];
        foreach ($this->tokensByLocation[self::LOCATION_ROOM][$locationKey] as $tokenId) {
            if (isset($this->tokens[$tokenId])) {
                $result[$tokenId] = $this->tokens[$tokenId];
            }
        }
        
        return $result;
    }

    /**
     * Get tokens in multiple room positions (for explosion effects)
     */
    public function getTokensInPositions(array $positions): array
    {
        if (empty($positions)) {
            return [];
        }
        
        $result = [];
        foreach ($positions as $pos) {
            $roomTokens = $this->getTokensInRoom($pos[0], $pos[1]);
            $result = array_merge($result, $roomTokens);
        }
        
        return $result;
    }

    /**
     * Move token to player inventory
     */
    public function pickupToken(int $playerId, string $tokenId): bool
    {
        // Check if token exists and is in a room
        if (!isset($this->tokens[$tokenId])) {
            return false;
        }
        
        $token = $this->tokens[$tokenId];
        if ($token['token_location'] !== self::LOCATION_ROOM) {
            return false;
        }
        
        // Move token to player
        $this->updateTokenLocation($tokenId, self::LOCATION_PLAYER, (string)$playerId);
        
        return true;
    }

    /**
     * Get all tokens owned by a player
     */
    public function getPlayerTokens(int $playerId): array
    {
        $playerKey = (string)$playerId;
        
        if (!isset($this->tokensByLocation[self::LOCATION_PLAYER][$playerKey])) {
            return [];
        }
        
        $result = [];
        foreach ($this->tokensByLocation[self::LOCATION_PLAYER][$playerKey] as $tokenId) {
            if (isset($this->tokens[$tokenId])) {
                $result[$tokenId] = $this->tokens[$tokenId];
            }
        }
        
        return $result;
    }

    /**
     * Move token back to bag
     */
    public function moveTokenToBag(string $tokenId): void
    {
        $this->updateTokenLocation($tokenId, self::LOCATION_BAG, '0');
    }

    /**
     * Destroy/remove token from game
     */
    public function destroyToken(string $tokenId): void
    {
        $this->updateTokenLocation($tokenId, self::LOCATION_REMOVED, '0');
    }

    /**
     * Draw random token from bag
     */
    public function drawRandomTokenFromBag(): ?array
    {
        if (!isset($this->tokensByLocation[self::LOCATION_BAG]['0']) || 
            empty($this->tokensByLocation[self::LOCATION_BAG]['0'])) {
            return null;
        }
        
        $bagTokenIds = $this->tokensByLocation[self::LOCATION_BAG]['0'];
        $randomIndex = array_rand($bagTokenIds);
        $randomTokenId = $bagTokenIds[$randomIndex];
        
        if (isset($this->tokens[$randomTokenId])) {
            return $this->tokens[$randomTokenId];
        }
        
        return null;
    }

    /**
     * Flip a double-sided token
     */
    public function flipToken(string $tokenId): bool
    {
        if (!isset($this->tokens[$tokenId])) {
            return false;
        }
        
        $token = $this->tokens[$tokenId];
        
        // Only certain tokens can be flipped
        if (!in_array($token['token_type'], [self::TOKEN_TYPE_TREASURE_GUARD, self::TOKEN_TYPE_SKELETON_CUTLASS, self::TOKEN_TYPE_SKELETON_GROG])) {
            return false;
        }
        
        $newState = $token['token_state'] === 0 ? 1 : 0;
        
        // Update in memory
        $token['token_state'] = $newState;
        $this->tokens[$tokenId] = $token;
        
        // Save to database
        $this->saveToken($token);
        
        return true;
    }

    /**
     * Check if token is showing treasure side
     */
    public function isTokenShowingTreasure(string $tokenId): bool
    {
        if (!isset($this->tokens[$tokenId])) {
            return false;
        }
        
        $token = $this->tokens[$tokenId];
        return $token['token_type'] === self::TOKEN_TYPE_TREASURE_GUARD && $token['token_state'] === self::STATE_TREASURE;
    }

    /**
     * Check if token is showing guard side
     */
    public function isTokenShowingGuard(string $tokenId): bool
    {
        if (!isset($this->tokens[$tokenId])) {
            return false;
        }
        
        $token = $this->tokens[$tokenId];
        return $token['token_type'] === self::TOKEN_TYPE_TREASURE_GUARD && $token['token_state'] === self::STATE_GUARD;
    }

    /**
     * Get treasure tokens owned by player
     */
    public function getPlayerTreasureCount(int $playerId): int
    {
        $tokens = $this->getPlayerTokens($playerId);
        $treasureCount = 0;
        
        foreach ($tokens as $token) {
            if ($token['token_type'] === self::TOKEN_TYPE_TREASURE_GUARD && $token['token_state'] === self::STATE_TREASURE) {
                $treasureCount++;
            }
        }
        
        return $treasureCount;
    }

    /**
     * Setup tokens for game start
     */
    public function setupTokens(array $tokenDefinitions): void
    {
        // Initialize tokens from material definitions in the bag
        $this->createTokensInBag($tokenDefinitions);
        
        // Reload in-memory data to include new tokens
        $this->initFromDatabase();
    }

    /**
     * Create initial tokens and place them in the bag based on material definitions
     */
    private function createTokensInBag(array $materialTokens): void
    {
        foreach ($materialTokens as $tokenDef) {
            for ($i = 1; $i <= $tokenDef['quantity']; $i++) {
                $tokenId = $this->generateTokenId($tokenDef, $i);
                $tokenType = $tokenDef['front_type'] . '_' . $tokenDef['back_type'];
                
                $this->game->DbQuery("INSERT INTO token (token_id, token_type, token_location, token_location_arg, token_state) 
                                    VALUES ('$tokenId', '$tokenType', '" . self::LOCATION_BAG . "', 0, 0)");
            }
        }
    }

    /**
     * Generate a unique token ID based on token definition and instance number
     */
    private function generateTokenId(array $tokenDef, int $instance): string
    {
        $frontType = $tokenDef['front_type'];
        $backType = $tokenDef['back_type'] ?? 'none';
        
        return "{$frontType}_{$backType}_{$instance}";
    }

    /**
     * Place a random token in a room when a new room is revealed
     */
    public function placeRandomTokenInRoom(int $x, int $y): ?string
    {
        $randomToken = $this->drawRandomTokenFromBag();
        
        if (!$randomToken) {
            return null; // No tokens left in bag
        }
        
        $tokenId = $randomToken['token_id'];
        $this->placeTokenInRoom($tokenId, $x, $y);
        
        return $tokenId;
    }

    /**
     * Handle skeleton crew movement during Skelit's Revenge
     */
    public function moveSkeletonCrew(string $tokenId, int $fromX, int $fromY, int $toX, int $toY): bool
    {
        if (!isset($this->tokens[$tokenId])) {
            return false;
        }
        
        $token = $this->tokens[$tokenId];
        
        if (!in_array($token['token_type'], [self::TOKEN_TYPE_SKELETON_CUTLASS, self::TOKEN_TYPE_SKELETON_GROG])) {
            return false;
        }
        
        // Check if token is in the from room
        $fromLocationKey = $this->getLocationKey($fromX, $fromY);
        if ($token['token_location'] !== self::LOCATION_ROOM || $token['token_location_arg'] !== $fromLocationKey) {
            return false;
        }
        
        // Move token to new room
        $this->placeTokenInRoom($tokenId, $toX, $toY);
        
        return true;
    }

    /**
     * Get location key for room coordinates
     */
    private function getLocationKey(int $x, int $y): string
    {
        return "{$x}_{$y}";
    }

    /**
     * Parse location key back to coordinates
     */
    private function parseLocationKey(string $locationKey): array
    {
        $parts = explode('_', $locationKey);
        return [(int)$parts[0], (int)$parts[1]];
    }

    /**
     * Get all skeleton crew tokens in play
     */
    public function getSkeletonCrewTokens(): array
    {
        $result = [];
        
        if (!isset($this->tokensByLocation[self::LOCATION_ROOM])) {
            return $result;
        }
        
        foreach ($this->tokensByLocation[self::LOCATION_ROOM] as $locationArg => $tokenIds) {
            foreach ($tokenIds as $tokenId) {
                if (isset($this->tokens[$tokenId])) {
                    $token = $this->tokens[$tokenId];
                    if (in_array($token['token_type'], [self::TOKEN_TYPE_SKELETON_CUTLASS, self::TOKEN_TYPE_SKELETON_GROG])) {
                        $result[$tokenId] = $token;
                    }
                }
            }
        }
        
        return $result;
    }

    /**
     * Check if room has any skeleton crew
     */
    public function hasSkeletonCrewInRoom(int $x, int $y): bool
    {
        $locationKey = $this->getLocationKey($x, $y);
        
        if (!isset($this->tokensByLocation[self::LOCATION_ROOM][$locationKey])) {
            return false;
        }
        
        foreach ($this->tokensByLocation[self::LOCATION_ROOM][$locationKey] as $tokenId) {
            if (isset($this->tokens[$tokenId])) {
                $token = $this->tokens[$tokenId];
                if (in_array($token['token_type'], [self::TOKEN_TYPE_SKELETON_CUTLASS, self::TOKEN_TYPE_SKELETON_GROG])) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Get all tokens for client display
     */
    public function getAllTokensState(): array
    {
        $result = [
            'in_rooms' => [],
            'with_players' => [],
            'in_bag_count' => 0
        ];
        
        foreach ($this->tokens as $tokenId => $token) {
            if ($token['token_location'] === self::LOCATION_ROOM) {
                $coords = $this->parseLocationKey($token['token_location_arg']);
                $result['in_rooms'][] = [
                    'token_id' => $token['token_id'],
                    'token_type' => $token['token_type'],
                    'token_state' => $token['token_state'],
                    'x' => $coords[0],
                    'y' => $coords[1]
                ];
            } elseif ($token['token_location'] === self::LOCATION_PLAYER) {
                $playerId = (int)$token['token_location_arg'];
                if (!isset($result['with_players'][$playerId])) {
                    $result['with_players'][$playerId] = [];
                }
                $result['with_players'][$playerId][] = [
                    'token_id' => $token['token_id'],
                    'token_type' => $token['token_type'],
                    'token_state' => $token['token_state']
                ];
            } elseif ($token['token_location'] === self::LOCATION_BAG) {
                $result['in_bag_count']++;
            }
        }
        
        return $result;
    }

    // ========== Enhanced Token Workflow Methods ==========

    /**
     * Defeat an enemy token - flip from enemy (state=0) to object (state=1)
     */
    public function defeatEnemy(string $tokenId): bool
    {
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        if (!$token) {
            return false;
        }
        
        // Can only defeat enemies (state = 0)
        if ($token['token_state'] !== 0) {
            return false;
        }
        
        // Flip to object side (state = 1)
        $this->game->DbQuery("UPDATE token SET token_state = 1 WHERE token_id = '$tokenId'");
        
        return true;
    }

    /**
     * Spawn a random enemy token in a room (when room is revealed)
     */
    public function spawnRandomEnemyInRoom(int $x, int $y): ?string
    {
        $randomToken = $this->drawRandomTokenFromBag();
        
        if (!$randomToken) {
            return null; // No tokens left in bag
        }
        
        $tokenId = $randomToken['token_id'];
        
        // Ensure token starts as enemy (state = 0)
        $this->game->DbQuery("UPDATE token SET token_state = 0 WHERE token_id = '$tokenId'");
        
        // Place token in room
        $this->placeTokenInRoom($tokenId, $x, $y);
        
        return $tokenId;
    }

    /**
     * Check if token can be picked up (must be object side and in room)
     */
    public function canPickupObject(string $tokenId): bool
    {
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        if (!$token) {
            return false;
        }
        
        // Must be showing object side (state = 1) and be in a room
        return $token['token_state'] === 1 && $token['token_location'] === self::LOCATION_ROOM;
    }

    /**
     * Get only enemy tokens in a specific room (state = 0)
     */
    public function getEnemyTokensInRoom(int $x, int $y): array
    {
        $locationKey = $this->getLocationKey($x, $y);
        
        return $this->game->getCollectionFromDb(
            "SELECT * FROM token WHERE token_location = '" . self::LOCATION_ROOM . "' 
             AND token_location_arg = '$locationKey' AND token_state = 0"
        );
    }

    /**
     * Get only object tokens in a specific room (state = 1)
     */
    public function getObjectTokensInRoom(int $x, int $y): array
    {
        $locationKey = $this->getLocationKey($x, $y);
        
        return $this->game->getCollectionFromDb(
            "SELECT * FROM token WHERE token_location = '" . self::LOCATION_ROOM . "' 
             AND token_location_arg = '$locationKey' AND token_state = 1"
        );
    }

    /**
     * Check if token is showing enemy side
     */
    public function isTokenEnemy(string $tokenId): bool
    {
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        return $token && $token['token_state'] === 0;
    }

    /**
     * Check if token is showing object side
     */
    public function isTokenObject(string $tokenId): bool
    {
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        return $token && $token['token_state'] === 1;
    }

    /**
     * Get token definition from token ID (parse front/back types)
     */
    public function getTokenDefinition(string $tokenId): ?array
    {
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        if (!$token) {
            return null;
        }
        
        // Parse token type back to front/back types
        $parts = explode('_', $token['token_type']);
        if (count($parts) >= 2) {
            return [
                'front_type' => $parts[0],
                'back_type' => $parts[1],
                'current_state' => $token['token_state'],
                'location' => $token['token_location']
            ];
        }
        
        return null;
    }

    /**
     * Enhanced pickup method that checks object state
     */
    public function pickupObject(int $playerId, string $tokenId): bool
    {
        if (!$this->canPickupObject($tokenId)) {
            return false;
        }
        
        // Use existing pickup logic
        return $this->pickupToken($playerId, $tokenId);
    }

    /**
     * Get count of tokens in bag (for UI display)
     */
    public function getBagTokenCount(): int
    {
        return (int)$this->game->getUniqueValueFromDB(
            "SELECT COUNT(*) FROM token WHERE token_location = '" . self::LOCATION_BAG . "'"
        );
    }

    /**
     * Check if room has any enemies (for battle determination)
     */
    public function hasEnemiesInRoom(int $x, int $y): bool
    {
        $enemies = $this->getEnemyTokensInRoom($x, $y);
        return count($enemies) > 0;
    }

    /**
     * Check if room has any objects (for pickup opportunities)
     */
    public function hasObjectsInRoom(int $x, int $y): bool
    {
        $objects = $this->getObjectTokensInRoom($x, $y);
        return count($objects) > 0;
    }
}
