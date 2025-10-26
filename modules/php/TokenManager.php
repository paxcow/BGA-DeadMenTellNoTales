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
    }

    /**
     * Place a token in a specific room
     */
    public function placeTokenInRoom(string $tokenId, int $x, int $y): void
    {
        $locationKey = $this->getLocationKey($x, $y);
        
        $this->game->DbQuery("UPDATE token SET token_location = '" . self::LOCATION_ROOM . "', token_location_arg = '$locationKey' WHERE token_id = '$tokenId'");
    }

    /**
     * Get all tokens in a specific room
     */
    public function getTokensInRoom(int $x, int $y): array
    {
        $locationKey = $this->getLocationKey($x, $y);
        
        return $this->game->getCollectionFromDb(
            "SELECT * FROM token WHERE token_location = '" . self::LOCATION_ROOM . "' AND token_location_arg = '$locationKey'"
        );
    }

    /**
     * Get tokens in multiple room positions (for explosion effects)
     */
    public function getTokensInPositions(array $positions): array
    {
        if (empty($positions)) {
            return [];
        }
        
        $locationKeys = [];
        foreach ($positions as $pos) {
            $locationKeys[] = "'" . $this->getLocationKey($pos[0], $pos[1]) . "'";
        }
        
        $whereClause = implode(',', $locationKeys);
        
        return $this->game->getCollectionFromDb(
            "SELECT * FROM token WHERE token_location = '" . self::LOCATION_ROOM . "' AND token_location_arg IN ($whereClause)"
        );
    }

    /**
     * Move token to player inventory
     */
    public function pickupToken(int $playerId, string $tokenId): bool
    {
        // Check if token exists and is in a room
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId' AND token_location = '" . self::LOCATION_ROOM . "'"
        );
        
        if (!$token) {
            return false;
        }
        
        // Move token to player
        $this->game->DbQuery("UPDATE token SET token_location = '" . self::LOCATION_PLAYER . "', token_location_arg = $playerId WHERE token_id = '$tokenId'");
        
        return true;
    }

    /**
     * Get all tokens owned by a player
     */
    public function getPlayerTokens(int $playerId): array
    {
        return $this->game->getCollectionFromDb(
            "SELECT * FROM token WHERE token_location = '" . self::LOCATION_PLAYER . "' AND token_location_arg = $playerId"
        );
    }

    /**
     * Move token back to bag
     */
    public function moveTokenToBag(string $tokenId): void
    {
        $this->game->DbQuery("UPDATE token SET token_location = '" . self::LOCATION_BAG . "', token_location_arg = 0 WHERE token_id = '$tokenId'");
    }

    /**
     * Destroy/remove token from game
     */
    public function destroyToken(string $tokenId): void
    {
        $this->game->DbQuery("UPDATE token SET token_location = '" . self::LOCATION_REMOVED . "', token_location_arg = 0 WHERE token_id = '$tokenId'");
    }

    /**
     * Draw random token from bag
     */
    public function drawRandomTokenFromBag(): ?array
    {
        $token = $this->game->getObjectFromDB(
            "SELECT token_id FROM token WHERE token_location = '" . self::LOCATION_BAG . "' ORDER BY RAND() LIMIT 1"
        );
        
        return $token ?: null;
    }

    /**
     * Flip a double-sided token
     */
    public function flipToken(string $tokenId): bool
    {
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        if (!$token) {
            return false;
        }
        
        // Only certain tokens can be flipped
        if (!in_array($token['token_type'], [self::TOKEN_TYPE_TREASURE_GUARD, self::TOKEN_TYPE_SKELETON_CUTLASS, self::TOKEN_TYPE_SKELETON_GROG])) {
            return false;
        }
        
        $newState = $token['token_state'] === 0 ? 1 : 0;
        $this->game->DbQuery("UPDATE token SET token_state = $newState WHERE token_id = '$tokenId'");
        
        return true;
    }

    /**
     * Check if token is showing treasure side
     */
    public function isTokenShowingTreasure(string $tokenId): bool
    {
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        if (!$token) {
            return false;
        }
        
        return $token['token_type'] === self::TOKEN_TYPE_TREASURE_GUARD && $token['token_state'] === self::STATE_TREASURE;
    }

    /**
     * Check if token is showing guard side
     */
    public function isTokenShowingGuard(string $tokenId): bool
    {
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        if (!$token) {
            return false;
        }
        
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
    public function setupTokens(): void
    {
        // Initialize 20 double-sided tokens in the bag
        $this->createTokensInBag();
    }

    /**
     * Create initial tokens and place them in the bag
     */
    private function createTokensInBag(): void
    {
        // This would be expanded based on actual token definitions
        // For now, create sample tokens
        
        for ($i = 1; $i <= 10; $i++) {
            // Create treasure/guard tokens
            $this->game->DbQuery("INSERT INTO token (token_id, token_type, token_location, token_location_arg, token_state) 
                                VALUES ('treasure_guard_$i', '" . self::TOKEN_TYPE_TREASURE_GUARD . "', '" . self::LOCATION_BAG . "', 0, " . self::STATE_TREASURE . ")");
        }
        
        for ($i = 1; $i <= 5; $i++) {
            // Create skeleton crew cutlass tokens
            $this->game->DbQuery("INSERT INTO token (token_id, token_type, token_location, token_location_arg, token_state) 
                                VALUES ('skeleton_cutlass_$i', '" . self::TOKEN_TYPE_SKELETON_CUTLASS . "', '" . self::LOCATION_BAG . "', 0, " . self::STATE_CUTLASS . ")");
        }
        
        for ($i = 1; $i <= 5; $i++) {
            // Create skeleton crew grog tokens
            $this->game->DbQuery("INSERT INTO token (token_id, token_type, token_location, token_location_arg, token_state) 
                                VALUES ('skeleton_grog_$i', '" . self::TOKEN_TYPE_SKELETON_GROG . "', '" . self::LOCATION_BAG . "', 0, " . self::STATE_GROG . ")");
        }
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
        $token = $this->game->getObjectFromDB(
            "SELECT * FROM token WHERE token_id = '$tokenId'"
        );
        
        if (!$token || !in_array($token['token_type'], [self::TOKEN_TYPE_SKELETON_CUTLASS, self::TOKEN_TYPE_SKELETON_GROG])) {
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
        return $this->game->getCollectionFromDb(
            "SELECT * FROM token WHERE token_type IN ('" . self::TOKEN_TYPE_SKELETON_CUTLASS . "', '" . self::TOKEN_TYPE_SKELETON_GROG . "') 
             AND token_location = '" . self::LOCATION_ROOM . "'"
        );
    }

    /**
     * Check if room has any skeleton crew
     */
    public function hasSkeletonCrewInRoom(int $x, int $y): bool
    {
        $locationKey = $this->getLocationKey($x, $y);
        
        $count = $this->game->getUniqueValueFromDB(
            "SELECT COUNT(*) FROM token WHERE token_type IN ('" . self::TOKEN_TYPE_SKELETON_CUTLASS . "', '" . self::TOKEN_TYPE_SKELETON_GROG . "') 
             AND token_location = '" . self::LOCATION_ROOM . "' AND token_location_arg = '$locationKey'"
        );
        
        return (int)$count > 0;
    }

    /**
     * Get all tokens for client display
     */
    public function getAllTokensState(): array
    {
        $tokens = $this->game->getCollectionFromDb(
            "SELECT * FROM token WHERE token_location != '" . self::LOCATION_REMOVED . "'"
        );
        
        $result = [
            'in_rooms' => [],
            'with_players' => [],
            'in_bag_count' => 0
        ];
        
        foreach ($tokens as $token) {
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
}
