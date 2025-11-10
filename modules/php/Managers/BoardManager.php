<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\Managers;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\DB\RoomTilesManager;

/**
 * Manages the board state and tile placement for Dead Men Pax
 */
class BoardManager
{
    private Table $game;
    private array $tiles = [];           // legacy storage keyed by tile id
    private array $tileById = [];        // [id] => RoomTile
    private array $tilesByPosition = []; // [y][x] => RoomTile
    private RoomTilesManager $roomTilesManager;

    /**
     * Constructor.
     *
     * @param Table $game The game instance.
     */
    public function __construct(Table $game, RoomTilesManager $roomTilesManager)
    {
        $this->game = $game;
        $this->roomTilesManager = $roomTilesManager;
        $this->loadFromDatabase();
    }

    /**
     * Loads existing tiles from the database.
     */
    private function loadFromDatabase(): void
    {
        $this->tiles = [];
        $this->tileById = [];
        $this->tilesByPosition = [];

        foreach ($this->roomTilesManager->getPlacedTiles() as $tile) {
            $this->indexTile($tile);
        }
    }

    /**
     * Places a tile on the board at specified coordinates with optional orientation.
     *
     * @param RoomTile $tile The tile to place.
     * @param int $x The x-coordinate.
     * @param int $y The y-coordinate.
     * @param int $orientation The orientation of the tile.
     * @return bool True if the tile was placed successfully, false otherwise.
     */
    public function placeTile(RoomTile $tile, int $x, int $y, int $orientation = RoomTile::ORIENTATION_0): bool
    {
        // Check if position is already occupied
        if ($this->getTileAt($x, $y) !== null) {
            return false;
        }

        // Set the tile's orientation before checking connections
        $tile->setOrientation($orientation);

        // For non-starting tiles, validate connections
        if (!$tile->isStartingTile()) {
            if (!$this->hasValidConnections($tile, $x, $y)) {
                return false;
            }
        }

// Place the tile
        $tile->setPosition($x, $y);
        $this->indexTile($tile);

        // Update database
        $this->saveToDatabase($tile);

        return true;
    }

    /**
     * Checks if the tile has valid door connections at the given position.
     *
     * @param RoomTile $tile The tile to check.
     * @param int $x The x-coordinate.
     * @param int $y The y-coordinate.
     * @return bool True if the connections are valid, false otherwise.
     */
    private function hasValidConnections(RoomTile $tile, int $x, int $y): bool
    {
        $adjacentPositions = [
            RoomTile::DOOR_NORTH => [$x, $y - 1],
            RoomTile::DOOR_EAST => [$x + 1, $y],
            RoomTile::DOOR_SOUTH => [$x, $y + 1],
            RoomTile::DOOR_WEST => [$x - 1, $y]
        ];

        $hasConnection = false;

        foreach ($adjacentPositions as $direction => $pos) {
            $adjacentTile = $this->getTileAt($pos[0], $pos[1]);

            if ($adjacentTile !== null) {
                // There's an adjacent tile - check door compatibility
                if ($tile->hasDoor($direction) && $adjacentTile->hasDoor($this->getOppositeDirection($direction))) {
                    $hasConnection = true;
                } elseif ($tile->hasDoor($direction) !== $adjacentTile->hasDoor($this->getOppositeDirection($direction))) {
                    // Doors don't match - invalid placement
                    return false;
                }
            }
        }

        return $hasConnection;
    }

    /**
     * Gets the opposite direction.
     *
     * @param int $direction The direction.
     * @return int The opposite direction.
     */
    private function getOppositeDirection(int $direction): int
    {
        switch ($direction) {
            case RoomTile::DOOR_NORTH: return RoomTile::DOOR_SOUTH;
            case RoomTile::DOOR_SOUTH: return RoomTile::DOOR_NORTH;
            case RoomTile::DOOR_EAST: return RoomTile::DOOR_WEST;
            case RoomTile::DOOR_WEST: return RoomTile::DOOR_EAST;
            default: return 0;
        }
    }

    /**
     * Gets the tile at specific coordinates.
     *
     * @param int $x The x-coordinate.
     * @param int $y The y-coordinate.
     * @return RoomTile|null The tile at the given coordinates, or null if no tile is present.
     */
    public function getTileAt(int $x, int $y): ?RoomTile
    {
        return $this->tilesByPosition[$y][$x] ?? null;
    }

    /**
     * Gets the tile by ID.
     *
     * @param int $tileId The ID of the tile.
     * @return RoomTile|null The tile with the given ID, or null if no tile is found.
     */
    public function getTileById(int $tileId): ?RoomTile
    {
        return $this->tileById[$tileId] ?? null;
    }

    /**
     * Gets the coordinates for a tile id.
     *
     * @param int $tileId
     * @return array{x:int,y:int}|null
     */
    public function getTileCoords(int $tileId): ?array
    {
        $tile = $this->getTileById($tileId);
        return $tile ? ['x' => $tile->getX(), 'y' => $tile->getY()] : null;
    }

    /**
     * Resolves a tile id from board coordinates.
     *
     * @param int $x
     * @param int $y
     * @return int|null
     */
    public function getTileIdFromCoords(int $x, int $y): ?int
    {
        $tile = $this->getTileAt($x, $y);
        return $tile ? $tile->getId() : null;
    }

    /**
     * Gets all tiles on the board.
     *
     * @return array An array of all tiles on the board.
     */
    public function getAllTiles(): array
    {
        return array_values($this->tileById);
    }

    /**
     * Gets valid placement positions for a tile.
     *
     * @param RoomTile $tile The tile to place.
     * @return array An array of valid placement positions.
     */
    public function getValidPlacementPositions(RoomTile $tile): array
    {
        $validPositions = [];
        $checkedPositions = [];
        
        // Check all positions adjacent to existing tiles
        foreach ($this->getAllTiles() as $existingTile) {
            $adjacentPositions = [
                [$existingTile->getX(), $existingTile->getY() - 1], // North
                [$existingTile->getX() + 1, $existingTile->getY()], // East
                [$existingTile->getX(), $existingTile->getY() + 1], // South
                [$existingTile->getX() - 1, $existingTile->getY()]  // West
            ];
            
            foreach ($adjacentPositions as $pos) {
                $x = $pos[0];
                $y = $pos[1];
                $posKey = "$x,$y";
                
                // Skip if position is occupied or already checked
                if ($this->getTileAt($x, $y) !== null || isset($checkedPositions[$posKey])) {
                    continue;
                }
                
                $checkedPositions[$posKey] = true;
                
                // Check all possible orientations
                foreach ($tile->getPossibleOrientations() as $orientation) {
                    $testTile = $tile->withOrientation($orientation);
                    if ($this->hasValidConnections($testTile, $x, $y)) {
                        $validPositions[] = [
                            'x' => $x,
                            'y' => $y,
                            'orientation' => $orientation
                        ];
                        break; // Found valid orientation for this position
                    }
                }
            }
        }
        
        return $validPositions;
    }

    /**
     * Finds a path between two tiles using the A* algorithm.
     *
     * @param RoomTile $startTile The starting tile.
     * @param RoomTile $endTile The ending tile.
     * @return array An array of tiles representing the path, or an empty array if no path is found.
     */
    public function findPath(RoomTile $startTile, RoomTile $endTile): array
    {
        if ($startTile->getId() === $endTile->getId()) {
            return [$startTile];
        }

        $openSet = [$startTile];
        $closedSet = [];
        $cameFrom = [];
        $gScore = [$startTile->getId() => 0];
        $fScore = [$startTile->getId() => $this->manhattanDistance($startTile, $endTile)];

        while (!empty($openSet)) {
            // Find node with lowest fScore
            $current = $this->getLowestFScoreTile($openSet, $fScore);
            $currentIndex = array_search($current, $openSet);
            unset($openSet[$currentIndex]);
            $openSet = array_values($openSet);

            if ($current->getId() === $endTile->getId()) {
                return $this->reconstructPath($cameFrom, $current);
            }

            $closedSet[] = $current;
            $neighbors = $this->getConnectedTiles($current);

            foreach ($neighbors as $neighbor) {
                if ($this->tileInArray($neighbor, $closedSet)) {
                    continue;
                }

                $tentativeGScore = ($gScore[$current->getId()] ?? PHP_INT_MAX) + 1;

                if (!$this->tileInArray($neighbor, $openSet)) {
                    $openSet[] = $neighbor;
                } elseif ($tentativeGScore >= ($gScore[$neighbor->getId()] ?? PHP_INT_MAX)) {
                    continue;
                }

                $cameFrom[$neighbor->getId()] = $current;
                $gScore[$neighbor->getId()] = $tentativeGScore;
                $fScore[$neighbor->getId()] = $tentativeGScore + $this->manhattanDistance($neighbor, $endTile);
            }
        }

        return []; // No path found
    }

    /**
     * Gets tiles connected to the given tile through doors.
     *
     * @param RoomTile $tile The tile.
     * @return array An array of connected tiles.
     */
    public function getConnectedTiles(RoomTile $tile): array
    {
        $connected = [];
        $adjacentPositions = [
            RoomTile::DOOR_NORTH => [$tile->getX(), $tile->getY() - 1],
            RoomTile::DOOR_EAST => [$tile->getX() + 1, $tile->getY()],
            RoomTile::DOOR_SOUTH => [$tile->getX(), $tile->getY() + 1],
            RoomTile::DOOR_WEST => [$tile->getX() - 1, $tile->getY()]
        ];

        foreach ($adjacentPositions as $direction => $pos) {
            if ($tile->hasDoor($direction)) {
                $adjacentTile = $this->getTileAt($pos[0], $pos[1]);
                if ($adjacentTile && $adjacentTile->hasDoor($this->getOppositeDirection($direction))) {
                    $connected[] = $adjacentTile;
                }
            }
        }

        return $connected;
    }

    /**
     * Checks if two tiles can have movement between them.
     *
     * @param RoomTile $fromTile The starting tile.
     * @param RoomTile $toTile The destination tile.
     * @return bool True if movement is possible, false otherwise.
     */
    public function canMoveBetween(RoomTile $fromTile, RoomTile $toTile): bool
    {
        $connectedTiles = $this->getConnectedTiles($fromTile);
        return $this->tileInArray($toTile, $connectedTiles);
    }

    /**
     * Handles chain explosions starting from a tile.
     *
     * @param RoomTile $startTile The tile where the explosion starts.
     * @return array An array containing the exploded tiles.
     */
    public function handleChainExplosions(RoomTile $startTile): array
    {
        $explosionResult = ['exploded_tiles' => []];
        $toProcess = [$startTile];
        $processed = [];

        while (!empty($toProcess)) {
            $currentTile = array_shift($toProcess);

if (in_array($currentTile->getId(), $processed)) {
                continue;
            }

$processed[] = $currentTile->getId();

            if ($currentTile->willExplode()) {
                // Determine explosion type
                $explosionType = $currentTile->hasPowderKeg() && !$currentTile->isPowderKegExploded() ? 'powder_keg' : 'fire';

                // Handle powder keg explosion
                if ($explosionType === 'powder_keg') {
                    $currentTile->explodePowderKeg();
                }

                // Set fire level to max (exploded)
                $currentTile->setFireLevel(6);

                // Add to the result to be processed by the Game mediator
                $explosionResult['exploded_tiles'][] = [
                    'x' => $currentTile->getX(),
                    'y' => $currentTile->getY(),
                    'type' => $explosionType,
'tile_id' => $currentTile->getId(),
                ];

                // Add fire to adjacent tiles
                $adjacentTiles = $this->getAdjacentTiles($currentTile);
                foreach ($adjacentTiles as $adjacentTile) {
                    $adjacentTile->increaseFireLevel(1);
                    $this->saveToDatabase($adjacentTile); // Save adjacent tile changes

if (!in_array($adjacentTile->getId(), $processed)) {
                        $toProcess[] = $adjacentTile;
                    }
                }

                // Save tile state to database
                $this->saveToDatabase($currentTile);

                // Notify explosion
                $this->game->notifyAllPlayers("explosion", clienttranslate('An explosion occurs in room!'), [
                    "tile_id" => $currentTile->getId(),
                    "x" => $currentTile->getX(),
                    "y" => $currentTile->getY(),
                    "type" => $explosionType,
                    "fire_level" => $currentTile->getFireLevel()
                ]);
            }
        }

        return $explosionResult;
    }

    /**
     * Gets tiles adjacent to the given tile.
     *
     * @param RoomTile $tile The tile.
     * @return array An array of adjacent tiles.
     */
    private function getAdjacentTiles(RoomTile $tile): array
    {
        $adjacent = [];
        $positions = [
            [$tile->getX(), $tile->getY() - 1], // North
            [$tile->getX() + 1, $tile->getY()], // East
            [$tile->getX(), $tile->getY() + 1], // South
            [$tile->getX() - 1, $tile->getY()]  // West
        ];

        foreach ($positions as $pos) {
            $adjacentTile = $this->getTileAt($pos[0], $pos[1]);
            if ($adjacentTile !== null) {
                $adjacent[] = $adjacentTile;
            }
        }

        return $adjacent;
    }

    /**
     * Checks if the ship is critically damaged.
     *
     * @param int $maxExplosions The maximum number of explosions before the ship is critically damaged.
     * @return bool True if the ship is critically damaged, false otherwise.
     */
    public function isCriticallyDamaged(int $maxExplosions = 4): bool
    {
        return count($this->getExplodedTiles()) >= $maxExplosions;
    }

    /**
     * Gets all exploded tiles.
     *
     * @return array An array of exploded tiles.
     */
    public function getExplodedTiles(): array
    {
        return array_filter($this->tiles, function($tile) {
            return $tile->getFireLevel() >= 6 || $tile->isPowderKegExploded();
        });
    }

    /**
     * Gets the ship bounds.
     *
     * @return array An array containing the min and max x and y coordinates of the ship.
     */
    public function getShipBounds(): array
    {
        if (empty($this->tiles)) {
            return ['minX' => 0, 'maxX' => 0, 'minY' => 0, 'maxY' => 0];
        }

        $minX = $maxX = $minY = $maxY = null;
        
        foreach ($this->tiles as $tile) {
            if ($minX === null || $tile->getX() < $minX) $minX = $tile->getX();
            if ($maxX === null || $tile->getX() > $maxX) $maxX = $tile->getX();
            if ($minY === null || $tile->getY() < $minY) $minY = $tile->getY();
            if ($maxY === null || $tile->getY() > $maxY) $maxY = $tile->getY();
        }

        return ['minX' => $minX, 'maxX' => $maxX, 'minY' => $minY, 'maxY' => $maxY];
    }

    /**
     * Saves a tile to the database.
     *
     * @param RoomTile $tile The tile to save.
     */
    public function saveToDatabase(RoomTile $tile): void
    {
        $this->roomTilesManager->saveTile($tile);
    }

    /**
     * Checks if a tile is in an array.
     *
     * @param RoomTile $tile The tile to check.
     * @param array $array The array to check.
     * @return bool True if the tile is in the array, false otherwise.
     */
    private function tileInArray(RoomTile $tile, array $array): bool
    {
        foreach ($array as $item) {
            if ($item->getId() === $tile->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds or updates a tile in lookup structures.
     *
     * @param RoomTile $tile
     */
    private function indexTile(RoomTile $tile): void
    {
        $this->tiles[$tile->getId()] = $tile;
        $this->tileById[$tile->getId()] = $tile;
        $this->tilesByPosition[$tile->getY()][$tile->getX()] = $tile;
    }

    /**
     * Gets the tile with the lowest f-score.
     *
     * @param array $tiles The array of tiles.
     * @param array $fScore The array of f-scores.
     * @return RoomTile The tile with the lowest f-score.
     */
    private function getLowestFScoreTile(array $tiles, array $fScore): RoomTile
    {
        $lowest = $tiles[0];
        $lowestScore = $fScore[$lowest->getId()] ?? PHP_INT_MAX;

        foreach ($tiles as $tile) {
            $score = $fScore[$tile->getId()] ?? PHP_INT_MAX;
            if ($score < $lowestScore) {
                $lowest = $tile;
                $lowestScore = $score;
            }
        }

        return $lowest;
    }

    /**
     * Reconstructs the path from the `cameFrom` array.
     *
     * @param array $cameFrom The `cameFrom` array.
     * @param RoomTile $current The current tile.
     * @return array The reconstructed path.
     */
    private function reconstructPath(array $cameFrom, RoomTile $current): array
    {
        $path = [$current];
        
        while (isset($cameFrom[$current->getId()])) {
            $current = $cameFrom[$current->getId()];
            array_unshift($path, $current);
        }

        return $path;
    }

    /**
     * Calculates the Manhattan distance between two tiles.
     *
     * @param RoomTile $tile1 The first tile.
     * @param RoomTile $tile2 The second tile.
     * @return int The Manhattan distance.
     */
    private function manhattanDistance(RoomTile $tile1, RoomTile $tile2): int
    {
        return abs($tile1->getX() - $tile2->getX()) + abs($tile1->getY() - $tile2->getY());
    }
}
