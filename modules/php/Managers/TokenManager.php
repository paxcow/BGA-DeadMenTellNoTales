<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\Managers;

use Bga\Games\DeadMenPax\DB\DBManager;
use Bga\Games\DeadMenPax\Models\TokenModel;

/**
 * Manages tokens (treasure, skeleton crew, etc.) for Dead Men Pax
 */
class TokenManager
{
    /** @var string[] */
    private const TOKEN_STATIC_FIELDS = [
        'token_type',
        'front_type',
        'front_value',
        'back_type',
        'back_value',
    ];

    /** @var string[] */
    private const TOKEN_MUTABLE_FIELDS = [
        'token_id',
        'token_location',
        'token_location_arg',
        'token_state',
        'token_order',
    ];

    private Game $game;
    private DBManager $tokenDbManager;

    /** @var array<string, array<string, mixed>> */
    private array $tokenDefinitionMap = [];
    
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

    /**
     * Constructor.
     *
     * @param Game $game The game instance.
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->tokenDbManager = new DBManager('token', TokenModel::class, $game);
        $definitions = $this->game->getTokenDefinitions();
        $this->tokenDefinitionMap = $this->buildTokenDefinitionMap($definitions);
        $this->initFromDatabase();
    }

    /**
     * Initializes in-memory storage from the database.
     */
    public function initFromDatabase(): void
    {
        // Clear existing in-memory data
        $this->tokens = [];
        $this->tokensByLocation = [];

        // Load all tokens from database using the abstraction layer
        $allTokens = $this->tokenDbManager->getAllRows();

        // Populate in-memory structures
        foreach ($allTokens as $token) {
            if (($token['token_location'] ?? null) === self::LOCATION_REMOVED) {
                continue;
            }
            $token = $this->hydrateTokenRow($token);
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
     * Reloads all token data from the database.
     */
    public function reload(): void
    {
        $this->initFromDatabase();
    }

    /**
     * Saves a token to the database.
     *
     * @param array $token The token data.
     */
    private function saveToken(array $token): void
    {
        $tokenId = $token['token_id'];
        $tokenType = $token['token_type'];
        $location = $token['token_location'];
        $locationArg = $token['token_location_arg'];
        $state = $token['token_state'];

        if (!isset($token['token_order'])) {
            $token['token_order'] = 0;
        }

        $this->tokenDbManager->saveObjectToDB(TokenModel::fromArray($token));
    }

    /**
     * Updates a token's location in memory and the database.
     *
     * @param string $tokenId The ID of the token.
     * @param string $newLocation The new location.
     * @param mixed $newLocationArg The new location argument.
     * @param int|null $newState The new state.
     */
    private function updateTokenLocation(string $tokenId, string $newLocation, $newLocationArg, ?int $newState = null, bool $requeueBag = false): void
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
        if ($requeueBag && $newLocation === self::LOCATION_BAG) {
            $token['token_order'] = $this->generateBagOrderValue();
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
     * Places a token in a specific room.
     *
     * @param string $tokenId The ID of the token.
     * @param int $x The x-coordinate of the room.
     * @param int $y The y-coordinate of the room.
     */
    public function placeTokenInRoom(string $tokenId, int $x, int $y): void
    {
        $locationKey = $this->getLocationKey($x, $y);
        $this->updateTokenLocation($tokenId, self::LOCATION_ROOM, $locationKey);
    }

    /**
     * Gets all tokens in a specific room.
     *
     * @param int $x The x-coordinate of the room.
     * @param int $y The y-coordinate of the room.
     * @return array An array of tokens.
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
     * Gets tokens in multiple room positions.
     *
     * @param array $positions The positions to check.
     * @return array An array of tokens.
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
     * Moves a token to a player's inventory.
     *
     * @param int $playerId The ID of the player.
     * @param string $tokenId The ID of the token.
     * @return bool True on success, false on failure.
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
     * Gets all tokens owned by a player.
     *
     * @param int $playerId The ID of the player.
     * @return array An array of tokens.
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
     * Moves a token back to the bag.
     *
     * @param string $tokenId The ID of the token.
     */
    public function moveTokenToBag(string $tokenId): void
    {
        $this->updateTokenLocation($tokenId, self::LOCATION_BAG, '0', null, true);
    }

    /**
     * Destroys/removes a token from the game.
     *
     * @param string $tokenId The ID of the token.
     */
    public function destroyToken(string $tokenId): void
    {
        $this->updateTokenLocation($tokenId, self::LOCATION_REMOVED, '0');
        // If a treasure token was destroyed, increment the counter
        if ($this->isTokenShowingTreasure($tokenId)) {
            $this->game->statsManager->incTreasuresDestroyed();
        }
    }

    /**
     * Draws a random token from the bag.
     *
     * @return array|null The drawn token, or null if the bag is empty.
     */
    public function drawNextTokenFromBag(): ?array
    {
        $bagTokens = $this->tokensByLocation[self::LOCATION_BAG]['0'] ?? [];
        if (empty($bagTokens)) {
            return null;
        }

        $candidateId = null;
        $candidateOrder = null;

        foreach ($bagTokens as $tokenId) {
            $token = $this->tokens[$tokenId] ?? null;
            if ($token === null) {
                continue;
            }

            $order = (int) ($token['token_order'] ?? PHP_INT_MAX);
            if ($candidateOrder === null
                || $order < $candidateOrder
                || ($order === $candidateOrder && strcmp($tokenId, (string) $candidateId) < 0)) {
                $candidateId = $tokenId;
                $candidateOrder = $order;
            }
        }

        return $candidateId !== null ? $this->tokens[$candidateId] : null;
    }

    /**
     * Flips a double-sided token.
     *
     * @param string $tokenId The ID of the token.
     * @return bool True on success, false on failure.
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
     * Gets a token by ID from the in-memory cache.
     */
    public function getTokenById(string $tokenId): ?array
    {
        return $this->tokens[$tokenId] ?? null;
    }

    /**
     * Builds a metadata description for an enemy token.
     */
    public function describeEnemyToken(string $tokenId): ?array
    {
        $token = $this->getTokenById($tokenId);
        if ($token === null) {
            return null;
        }

        $enemyType = $this->mapFrontTypeToEnemyType($token['front_type'] ?? '');
        if ($enemyType === null) {
            return null;
        }

        $roomId = null;
        if ($token['token_location'] === self::LOCATION_ROOM) {
            $coords = $this->parseLocationKey($token['token_location_arg']);
            $tile = $this->game->getBoardManager()->getTileAt($coords[0], $coords[1]);
            $roomId = $tile ? $tile->getId() : null;
        }

        return [
            'id' => $token['token_id'],
            'type' => $enemyType,
            'strength' => (int)($token['front_value'] ?? 0),
            'room_id' => $roomId,
            'state' => $token['token_state'],
        ];
    }

    /**
     * Convenience wrapper for flipping an enemy token.
     */
    public function flipEnemyToken(string $tokenId): bool
    {
        return $this->flipToken($tokenId);
    }

    /**
     * Checks if a token is showing the treasure side.
     *
     * @param string $tokenId The ID of the token.
     * @return bool True if the token is showing the treasure side, false otherwise.
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
     * Checks if a token is showing the guard side.
     *
     * @param string $tokenId The ID of the token.
     * @return bool True if the token is showing the guard side, false otherwise.
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
     * Gets the number of treasure tokens owned by a player.
     *
     * @param int $playerId The ID of the player.
     * @return int The number of treasure tokens.
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
     * Sets up tokens for the game start.
     *
     * @param array $tokenDefinitions The token definitions.
     */
    public function setupTokens(array $tokenDefinitions): void
    {
        $this->tokenDefinitionMap = $this->buildTokenDefinitionMap($tokenDefinitions);
        // Initialize tokens from material definitions in the bag
        $this->createTokensInBag($tokenDefinitions);
        
        // Reload in-memory data to include new tokens
        $this->initFromDatabase();
    }

    /**
     * Creates initial tokens and places them in the bag.
     *
     * @param array $materialTokens The token definitions.
     */
    private function createTokensInBag(array $materialTokens): void
    {
        $this->tokenDbManager->clearAll();

        $tokens = [];
        foreach ($materialTokens as $tokenDef) {
            $quantity = (int) ($tokenDef['quantity'] ?? 1);
            for ($i = 1; $i <= $quantity; $i++) {
                $tokenId = $this->generateTokenId($tokenDef, $i);
                $tokens[] = [
                    'token_id' => $tokenId,
                    'token_type' => $this->buildTokenType($tokenDef),
                    'token_location' => self::LOCATION_BAG,
                    'token_location_arg' => '0',
                    'token_state' => 0,
                    'front_type' => $tokenDef['front_type'],
                    'front_value' => $tokenDef['front_value'] ?? null,
                    'back_type' => $tokenDef['back_type'] ?? null,
                    'back_value' => $tokenDef['back_value'] ?? null,
                ];
            }
        }

        $this->shuffleArrayWithBga($tokens);

        foreach ($tokens as $index => $token) {
            $token['token_order'] = $index + 1;
            $this->tokenDbManager->saveObjectToDB(TokenModel::fromArray($token));
        }
    }

    /**
     * Generates a unique token ID.
     *
     * @param array $tokenDef The token definition.
     * @param int $instance The instance number.
     * @return string The generated token ID.
     */
    private function generateTokenId(array $tokenDef, int $instance): string
    {
        $frontType = $tokenDef['front_type'];
        $backType = $tokenDef['back_type'] ?? 'none';
        
        return "{$frontType}_{$backType}_{$instance}";
    }

    /**
     * @param array<int,array<string,mixed>> $tokenDefinitions
     * @return array<string,array<string,mixed>>
     */
    private function buildTokenDefinitionMap(array $tokenDefinitions): array
    {
        $map = [];
        foreach ($tokenDefinitions as $definition) {
            $quantity = (int) ($definition['quantity'] ?? 1);
            for ($i = 1; $i <= $quantity; $i++) {
                $tokenId = $this->generateTokenId($definition, $i);
                $map[$tokenId] = [
                    'token_type' => $this->buildTokenType($definition),
                    'front_type' => $definition['front_type'],
                    'front_value' => $definition['front_value'] ?? null,
                    'back_type' => $definition['back_type'] ?? null,
                    'back_value' => $definition['back_value'] ?? null,
                ];
            }
        }

        return $map;
    }

    private function buildTokenType(array $tokenDef): string
    {
        $backType = $tokenDef['back_type'] ?? 'none';
        return $tokenDef['front_type'] . '_' . ($backType ?? 'none');
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function hydrateTokenRow(array $row): array
    {
        $tokenId = $row['token_id'];
        $definition = $this->tokenDefinitionMap[$tokenId] ?? null;

        if ($definition !== null) {
            foreach (self::TOKEN_STATIC_FIELDS as $field) {
                if (array_key_exists($field, $definition)) {
                    $row[$field] = $definition[$field];
                }
            }
        }

        foreach (self::TOKEN_MUTABLE_FIELDS as $field) {
            switch ($field) {
                case 'token_id':
                case 'token_location':
                    $row[$field] = (string) ($row[$field] ?? '');
                    break;
                case 'token_location_arg':
                    $row[$field] = (string) ($row[$field] ?? '0');
                    break;
                case 'token_state':
                case 'token_order':
                    $row[$field] = isset($row[$field]) ? (int) $row[$field] : 0;
                    break;
            }
        }

        return $row;
    }

    /**
     * @param array<int,array<string,mixed>> $items
     */
    private function shuffleArrayWithBga(array &$items): void
    {
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = $this->game->bga_rand(0, $i);
            if ($i !== $j) {
                [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
            }
        }
    }

    private function generateBagOrderValue(): int
    {
        return $this->game->bga_rand(1, 1_000_000);
    }

    /**
     * Places a random token in a room when a new room is revealed.
     *
     * @param int $tileOrX Tile ID or x-coordinate of the room.
     * @param int|null $y The y-coordinate if using coordinates.
     * @return string|null The ID of the placed token, or null if no token was placed.
     */
    public function placeRandomTokenInRoom(int $tileOrX, ?int $y = null): ?string
    {
        if ($y === null) {
            $coords = $this->game->getBoardManager()->getTileCoords($tileOrX);
            if ($coords === null) {
                throw new \BgaSystemException("Cannot place token in unknown tile {$tileOrX}.");
            }
            $x = $coords['x'];
            $y = $coords['y'];
        } else {
            $x = $tileOrX;
        }

        $randomToken = $this->drawNextTokenFromBag();
        
        if (!$randomToken) {
            return null; // No tokens left in bag
        }
        
        $tokenId = $randomToken['token_id'];
        $this->placeTokenInRoom($tokenId, $x, $y);
        
        return $tokenId;
    }

    /**
     * Handles skeleton crew movement.
     *
     * @param string $tokenId The ID of the token.
     * @param int $fromX The starting x-coordinate.
     * @param int $fromY The starting y-coordinate.
     * @param int $toX The target x-coordinate.
     * @param int $toY The target y-coordinate.
     * @return bool True on success, false on failure.
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
     * Gets a location key for room coordinates.
     *
     * @param int $x The x-coordinate.
     * @param int $y The y-coordinate.
     * @return string The location key.
     */
    private function getLocationKey(int $x, int $y): string
    {
        return "{$x}_{$y}";
    }

    /**
     * Parses a location key back to coordinates.
     *
     * @param string $locationKey The location key.
     * @return array The coordinates.
     */
    private function parseLocationKey(string $locationKey): array
    {
        $parts = explode('_', $locationKey);
        return [(int)$parts[0], (int)$parts[1]];
    }

    /**
     * Retrieves enemies present in the specified tile by ID.
     *
     * @param int $tileId
     * @return array<int, array<string, mixed>>
     */
    public function getEnemiesInRoomById(int $tileId): array
    {
        $coords = $this->game->getBoardManager()->getTileCoords($tileId);
        if ($coords === null) {
            return [];
        }

        $tokens = $this->getTokensInRoom($coords['x'], $coords['y']);
        $enemies = [];
        foreach ($tokens as $token) {
            $enemyType = $this->mapFrontTypeToEnemyType($token['front_type'] ?? '');
            if ($enemyType === null) {
                continue;
            }

            $enemies[] = [
                'id' => $token['token_id'],
                'type' => $enemyType,
                'strength' => (int)($token['front_value'] ?? 0),
                'room_id' => $tileId,
                'state' => $token['token_state'],
            ];
        }

        return $enemies;
    }

    /**
     * Gets the stored strength for a given token.
     */
    public function getTokenStrength(string $tokenId): int
    {
        if (!isset($this->tokens[$tokenId])) {
            return 0;
        }

        return (int)($this->tokens[$tokenId]['front_value'] ?? 0);
    }

    /**
     * Maps token front types to battle enemy categories.
     */
    private function mapFrontTypeToEnemyType(string $frontType): ?string
    {
        return match ($frontType) {
            'guardian' => 'guard',
            'crew' => 'skeleton',
            default => null,
        };
    }

    /**
     * Gets all skeleton crew tokens in play.
     *
     * @return array An array of skeleton crew tokens.
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
     * Checks if a room has any skeleton crew.
     *
     * @param int $x The x-coordinate of the room.
     * @param int $y The y-coordinate of the room.
     * @return bool True if the room has skeleton crew, false otherwise.
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
     * Gets all tokens for client display.
     *
     * @return array An array of all tokens.
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

    /**
     * Defeats an enemy token.
     *
     * @param string $tokenId The ID of the token.
     * @return bool True on success, false on failure.
     */
    public function defeatEnemy(string $tokenId): bool
    {
        $token = $this->tokens[$tokenId] ?? null;

        if (!$token || (int)$token['token_state'] !== 0) {
            return false;
        }

        $token['token_state'] = 1;
        $this->tokens[$tokenId] = $token;
        $this->saveToken($token);

        return true;
    }

    /**
     * Spawns a random enemy token in a room.
     *
     * @param int $x The x-coordinate of the room.
     * @param int $y The y-coordinate of the room.
     * @return string|null The ID of the spawned token, or null if no token was spawned.
     */
    public function spawnRandomEnemyInRoom(int $x, int $y): ?string
    {
        $randomToken = $this->drawNextTokenFromBag();
        
        if (!$randomToken) {
            return null; // No tokens left in bag
        }
        
        $tokenId = $randomToken['token_id'];
        
        // Ensure token starts as enemy (state = 0)
        $this->updateTokenLocation($tokenId, self::LOCATION_ROOM, $this->getLocationKey($x, $y), 0);
        
        return $tokenId;
    }

    /**
     * Checks if a token can be picked up.
     *
     * @param string $tokenId The ID of the token.
     * @return bool True if the token can be picked up, false otherwise.
     */
    public function canPickupObject(string $tokenId): bool
    {
        $token = $this->tokens[$tokenId] ?? null;
        if (!$token) {
            return false;
        }

        return (int)$token['token_state'] === 1 && $token['token_location'] === self::LOCATION_ROOM;
    }

    /**
     * Gets only enemy tokens in a specific room.
     *
     * @param int $x The x-coordinate of the room.
     * @param int $y The y-coordinate of the room.
     * @return array An array of enemy tokens.
     */
    public function getEnemyTokensInRoom(int $x, int $y): array
    {
        $locationKey = $this->getLocationKey($x, $y);
        $tokens = $this->getTokensInRoom($x, $y);

        return array_filter(
            $tokens,
            static fn(array $token): bool => (int)$token['token_state'] === 0 && $token['token_location_arg'] === $locationKey
        );
    }

    /**
     * Gets only object tokens in a specific room.
     *
     * @param int $x The x-coordinate of the room.
     * @param int $y The y-coordinate of the room.
     * @return array An array of object tokens.
     */
    public function getObjectTokensInRoom(int $x, int $y): array
    {
        $locationKey = $this->getLocationKey($x, $y);
        $tokens = $this->getTokensInRoom($x, $y);

        return array_filter(
            $tokens,
            static fn(array $token): bool => (int)$token['token_state'] === 1 && $token['token_location_arg'] === $locationKey
        );
    }

    /**
     * Checks if a token is showing the enemy side.
     *
     * @param string $tokenId The ID of the token.
     * @return bool True if the token is an enemy, false otherwise.
     */
    public function isTokenEnemy(string $tokenId): bool
    {
        $token = $this->tokens[$tokenId] ?? null;
        return $token !== null && (int)$token['token_state'] === 0;
    }

    /**
     * Checks if a token is showing the object side.
     *
     * @param string $tokenId The ID of the token.
     * @return bool True if the token is an object, false otherwise.
     */
    public function isTokenObject(string $tokenId): bool
    {
        $token = $this->tokens[$tokenId] ?? null;
        return $token !== null && (int)$token['token_state'] === 1;
    }

    /**
     * Gets a token definition from a token ID.
     *
     * @param string $tokenId The ID of the token.
     * @return array|null The token definition, or null if not found.
     */
    public function getTokenDefinition(string $tokenId): ?array
    {
        $token = $this->tokens[$tokenId] ?? null;
        if (!$token) {
            return null;
        }

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
     * Enhanced pickup method that checks the object state.
     *
     * @param int $playerId The ID of the player.
     * @param string $tokenId The ID of the token.
     * @return bool True on success, false on failure.
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
     * Gets the count of tokens in the bag.
     *
     * @return int The number of tokens in the bag.
     */
    public function getBagTokenCount(): int
    {
        if (!isset($this->tokensByLocation[self::LOCATION_BAG])) {
            return 0;
        }

        $count = 0;
        foreach ($this->tokensByLocation[self::LOCATION_BAG] as $tokenIds) {
            $count += count($tokenIds);
        }

        return $count;
    }

    /**
     * Checks if a room has any enemies.
     *
     * @param int $x The x-coordinate of the room.
     * @param int $y The y-coordinate of the room.
     * @return bool True if the room has enemies, false otherwise.
     */
    public function hasEnemiesInRoom(int $x, int $y): bool
    {
        $enemies = $this->getEnemyTokensInRoom($x, $y);
        return count($enemies) > 0;
    }

    /**
     * Checks if a room has any objects.
     *
     * @param int $x The x-coordinate of the room.
     * @param int $y The y-coordinate of the room.
     * @return bool True if the room has objects, false otherwise.
     */
    public function hasObjectsInRoom(int $x, int $y): bool
    {
        $objects = $this->getObjectTokensInRoom($x, $y);
        return count($objects) > 0;
    }
}
