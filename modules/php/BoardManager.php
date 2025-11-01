<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\DB\RoomTileDBManager;

/**
 * Manages the board state and tile placement for Dead Men Pax
 */
class BoardManager
{
    private Table $game;
    private array $tiles = [];           // [x][y] => RoomTile
    private array $tileById = [];        // [id] => RoomTile
    private array $tilesByPosition = []; // [y][x] => RoomTile
    private RoomTileDBManager $roomTileDBManager;

    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->roomTileDBManager = new RoomTileDBManager($game);
        $this->loadFromDatabase();
    }

    /**
     * Load existing tiles from database
     */
    private function loadFromDatabase(): void
    {
        $tiles = $this->roomTileDBManager->getAllObjects();

        foreach ($tiles as $tileData) {
            $tile = RoomTile::fromArray($tileData->toArray());
            $this->tiles[$tile->getId()] = $tile;
            $this->tilesByPosition[$tile->getY()][$tile->getX()] = $tile;
        }
    }

    /**
     * Place a tile on the board at specified coordinates with optional orientation
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
        $this->tiles[$tile->getTileId()] = $tile;
        $this->tilesByPosition[$y][$x] = $tile;

        // Update database
        $this->saveToDatabase($tile);

        return true;
    }

    /**
     * Check if the tile has valid door connections at the given position
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
     * Get opposite direction
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
     * Get tile at specific coordinates
     */
    public function getTileAt(int $x, int $y): ?RoomTile
    {
        return $this->tilesByPosition[$y][$x] ?? null;
    }

    /**
     * Get tile by ID
     */
    public function getTileById(int $tileId): ?RoomTile
    {
        return $this->tiles[$tileId] ?? null;
    }

    /**
     * Get all tiles on the board
     */
    public function getAllTiles(): array
    {
        return $this->tiles;
    }

    /**
     * Get valid placement positions for a tile (considering all possible orientations)
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
     * Find path between two tiles using A* algorithm
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
     * Get tiles connected to the given tile through doors
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
     * Check if two tiles can have movement between them
     */
    public function canMoveBetween(RoomTile $fromTile, RoomTile $toTile): bool
    {
        $connectedTiles = $this->getConnectedTiles($fromTile);
        return $this->tileInArray($toTile, $connectedTiles);
    }

    /**
     * Handle chain explosions starting from a tile
     */
    public function handleChainExplosions(RoomTile $startTile): array
    {
        $explosionResult = ['exploded_tiles' => []];
        $toProcess = [$startTile];
        $processed = [];

        while (!empty($toProcess)) {
            $currentTile = array_shift($toProcess);

            if (in_array($currentTile->getTileId(), $processed)) {
                continue;
            }

            $processed[] = $currentTile->getTileId();

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
                    'tile_id' => $currentTile->getTileId(),
                ];

                // Add fire to adjacent tiles
                $adjacentTiles = $this->getAdjacentTiles($currentTile);
                foreach ($adjacentTiles as $adjacentTile) {
                    $adjacentTile->increaseFireLevel(1);
                    $this->saveToDatabase($adjacentTile); // Save adjacent tile changes

                    if (!in_array($adjacentTile->getTileId(), $processed)) {
                        $toProcess[] = $adjacentTile;
                    }
                }

                // Save tile state to database
                $this->saveToDatabase($currentTile);

                // Notify explosion
                $this->game->notifyAllPlayers("explosion", clienttranslate('An explosion occurs in room!'), [
                    "tile_id" => $currentTile->getTileId(),
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
     * Get tiles adjacent to the given tile (regardless of door connections)
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
     * Check if ship is critically damaged
     */
    public function isCriticallyDamaged(int $maxExplosions = 4): bool
    {
        return count($this->getExplodedTiles()) >= $maxExplosions;
    }

    /**
     * Get all exploded tiles
     */
    public function getExplodedTiles(): array
    {
        return array_filter($this->tiles, function($tile) {
            return $tile->getFireLevel() >= 6 || $tile->isPowderKegExploded();
        });
    }

    /**
     * Get ship bounds
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
     * Save tile to database
     */
    public function saveToDatabase(RoomTile $tile): void
    {
        $model = RoomTileModel::fromArray($tile->toArray());
        $this->roomTileDBManager->saveObjectToDB($model);
    }

    // Helper methods for pathfinding
    private function tileInArray(RoomTile $tile, array $array): bool
    {
        foreach ($array as $item) {
            if ($item->getId() === $tile->getId()) {
                return true;
            }
        }
        return false;
    }

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

    private function reconstructPath(array $cameFrom, RoomTile $current): array
    {
        $path = [$current];
        
        while (isset($cameFrom[$current->getId()])) {
            $current = $cameFrom[$current->getId()];
            array_unshift($path, $current);
        }

        return $path;
    }

    private function manhattanDistance(RoomTile $tile1, RoomTile $tile2): int
    {
        return abs($tile1->getX() - $tile2->getX()) + abs($tile1->getY() - $tile2->getY());
    }
}
