<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

/**
 * Represents a room tile on the ship board
 */
class RoomTile
{
    public const DOOR_NORTH = 1;
    public const DOOR_EAST = 2;
    public const DOOR_SOUTH = 4;
    public const DOOR_WEST = 8;

    public const COLOR_RED = 'red';
    public const COLOR_YELLOW = 'yellow';
    public const COLOR_GREEN = 'green';
    public const COLOR_BLUE = 'blue';

    // Orientation constants (in degrees)
    public const ORIENTATION_0 = 0;     // Original orientation
    public const ORIENTATION_90 = 90;   // 90 degrees clockwise
    public const ORIENTATION_180 = 180; // 180 degrees
    public const ORIENTATION_270 = 270; // 270 degrees clockwise (90 counter-clockwise)

    private int $id;
    private int $originalDoors;  // Doors in original orientation
    private int $doors;          // Current doors after rotation
    private string $color;
    private int $pips;
    private bool $hasPowderKeg;
    private bool $hasTrapdoor;
    private int $x;
    private int $y;
    private int $fireLevel;
    private bool $powderKegExploded;
    private bool $isStartingTile;
    private int $orientation;    // Current orientation in degrees

    public function __construct(
        int $id,
        int $doors,
        string $color,
        int $pips,
        bool $hasPowderKeg = false,
        bool $hasTrapdoor = false,
        bool $isStartingTile = false
    ) {
        $this->id = $id;
        $this->originalDoors = $doors;
        $this->doors = $doors;  // Start with original doors
        $this->color = $color;
        $this->pips = $pips;
        $this->hasPowderKeg = $hasPowderKeg;
        $this->hasTrapdoor = $hasTrapdoor;
        $this->isStartingTile = $isStartingTile;
        $this->x = 0;
        $this->y = 0;
        $this->fireLevel = 0;
        $this->powderKegExploded = false;
        $this->orientation = self::ORIENTATION_0;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getDoors(): int { return $this->doors; }
    public function getOriginalDoors(): int { return $this->originalDoors; }
    public function getColor(): string { return $this->color; }
    public function getPips(): int { return $this->pips; }
    public function hasPowderKeg(): bool { return $this->hasPowderKeg; }
    public function hasTrapdoor(): bool { return $this->hasTrapdoor; }
    public function getX(): int { return $this->x; }
    public function getY(): int { return $this->y; }
    public function getFireLevel(): int { return $this->fireLevel; }
    public function isPowderKegExploded(): bool { return $this->powderKegExploded; }
    public function isStartingTile(): bool { return $this->isStartingTile; }
    public function getOrientation(): int { return $this->orientation; }

    // Setters
    public function setPosition(int $x, int $y): void {
        $this->x = $x;
        $this->y = $y;
    }

    public function setFireLevel(int $level): void {
        $this->fireLevel = max(0, min(6, $level));
    }

    public function increaseFireLevel(int $amount = 1): void {
        $this->setFireLevel($this->fireLevel + $amount);
    }

    public function decreaseFireLevel(int $amount = 1): void {
        $this->setFireLevel($this->fireLevel - $amount);
    }

    public function explodePowderKeg(): void {
        $this->powderKegExploded = true;
    }

    // Door checking methods
    public function hasDoor(int $direction): bool {
        return ($this->doors & $direction) !== 0;
    }

    public function hasNorthDoor(): bool {
        return $this->hasDoor(self::DOOR_NORTH);
    }

    public function hasEastDoor(): bool {
        return $this->hasDoor(self::DOOR_EAST);
    }

    public function hasSouthDoor(): bool {
        return $this->hasDoor(self::DOOR_SOUTH);
    }

    public function hasWestDoor(): bool {
        return $this->hasDoor(self::DOOR_WEST);
    }

    /**
     * Get doors as an array of directions
     */
    public function getDoorsArray(): array {
        $doors = [];
        if ($this->hasNorthDoor()) $doors[] = self::DOOR_NORTH;
        if ($this->hasEastDoor()) $doors[] = self::DOOR_EAST;
        if ($this->hasSouthDoor()) $doors[] = self::DOOR_SOUTH;
        if ($this->hasWestDoor()) $doors[] = self::DOOR_WEST;
        return $doors;
    }

    /**
     * Check if this tile can connect to another tile in a given direction
     */
    public function canConnectTo(RoomTile $otherTile, int $direction): bool {
        // This tile must have a door in the given direction
        if (!$this->hasDoor($direction)) {
            return false;
        }

        // The other tile must have a door in the opposite direction
        $oppositeDirection = $this->getOppositeDirection($direction);
        return $otherTile->hasDoor($oppositeDirection);
    }

    /**
     * Get the opposite direction
     */
    private function getOppositeDirection(int $direction): int {
        switch ($direction) {
            case self::DOOR_NORTH: return self::DOOR_SOUTH;
            case self::DOOR_SOUTH: return self::DOOR_NORTH;
            case self::DOOR_EAST: return self::DOOR_WEST;
            case self::DOOR_WEST: return self::DOOR_EAST;
            default: return 0;
        }
    }

    /**
     * Check if the tile will explode (fire level 6 or powder keg explosion)
     */
    public function willExplode(): bool {
        return $this->fireLevel >= 6 || ($this->hasPowderKeg && !$this->powderKegExploded && $this->fireLevel > 0);
    }

    /**
     * Set the tile orientation and update door positions
     */
    public function setOrientation(int $orientation): void {
        // Normalize orientation to 0, 90, 180, or 270
        $orientation = $orientation % 360;
        if ($orientation < 0) $orientation += 360;
        
        $this->orientation = $orientation;
        $this->doors = $this->rotateDoorsToOrientation($this->originalDoors, $orientation);
    }

    /**
     * Rotate the tile clockwise by 90 degrees
     */
    public function rotateClockwise(): void {
        $newOrientation = ($this->orientation + 90) % 360;
        $this->setOrientation($newOrientation);
    }

    /**
     * Rotate the tile counter-clockwise by 90 degrees
     */
    public function rotateCounterClockwise(): void {
        $newOrientation = ($this->orientation - 90) % 360;
        if ($newOrientation < 0) $newOrientation += 360;
        $this->setOrientation($newOrientation);
    }

    /**
     * Reset tile to original orientation
     */
    public function resetOrientation(): void {
        $this->setOrientation(self::ORIENTATION_0);
    }

    /**
     * Rotate doors based on orientation
     */
    private function rotateDoorsToOrientation(int $doors, int $orientation): int {
        switch ($orientation) {
            case self::ORIENTATION_0:
                return $doors; // No rotation needed
            
            case self::ORIENTATION_90:
                return $this->rotateDoorsClockwise($doors);
            
            case self::ORIENTATION_180:
                return $this->rotateDoorsClockwise($this->rotateDoorsClockwise($doors));
            
            case self::ORIENTATION_270:
                return $this->rotateDoorsCounterClockwise($doors);
            
            default:
                return $doors;
        }
    }

    /**
     * Rotate doors 90 degrees clockwise
     */
    private function rotateDoorsClockwise(int $doors): int {
        $rotatedDoors = 0;
        
        // North -> East
        if ($doors & self::DOOR_NORTH) {
            $rotatedDoors |= self::DOOR_EAST;
        }
        
        // East -> South  
        if ($doors & self::DOOR_EAST) {
            $rotatedDoors |= self::DOOR_SOUTH;
        }
        
        // South -> West
        if ($doors & self::DOOR_SOUTH) {
            $rotatedDoors |= self::DOOR_WEST;
        }
        
        // West -> North
        if ($doors & self::DOOR_WEST) {
            $rotatedDoors |= self::DOOR_NORTH;
        }
        
        return $rotatedDoors;
    }

    /**
     * Rotate doors 90 degrees counter-clockwise
     */
    private function rotateDoorsCounterClockwise(int $doors): int {
        $rotatedDoors = 0;
        
        // North -> West
        if ($doors & self::DOOR_NORTH) {
            $rotatedDoors |= self::DOOR_WEST;
        }
        
        // East -> North
        if ($doors & self::DOOR_EAST) {
            $rotatedDoors |= self::DOOR_NORTH;
        }
        
        // South -> East
        if ($doors & self::DOOR_SOUTH) {
            $rotatedDoors |= self::DOOR_EAST;
        }
        
        // West -> South
        if ($doors & self::DOOR_WEST) {
            $rotatedDoors |= self::DOOR_SOUTH;
        }
        
        return $rotatedDoors;
    }

    /**
     * Get all possible orientations for this tile
     */
    public function getPossibleOrientations(): array {
        return [
            self::ORIENTATION_0,
            self::ORIENTATION_90,
            self::ORIENTATION_180,
            self::ORIENTATION_270
        ];
    }

    /**
     * Create a copy of this tile with a specific orientation
     */
    public function withOrientation(int $orientation): RoomTile {
        $copy = clone $this;
        $copy->setOrientation($orientation);
        return $copy;
    }

    /**
     * Get door positions for a specific orientation without changing the tile
     */
    public function getDoorsForOrientation(int $orientation): int {
        return $this->rotateDoorsToOrientation($this->originalDoors, $orientation);
    }

    /**
     * Get tile data as array for database storage
     */
    public function toArray(): array {
        return [
            'tile_id' => $this->id,
            'x' => $this->x,
            'y' => $this->y,
            'fire_level' => $this->fireLevel,
            'has_powder_keg' => $this->hasPowderKeg ? 1 : 0,
            'powder_keg_exploded' => $this->powderKegExploded ? 1 : 0,
            'doors' => $this->doors,
            'original_doors' => $this->originalDoors,
            'orientation' => $this->orientation,
            'color' => $this->color,
            'pips' => $this->pips,
            'has_trapdoor' => $this->hasTrapdoor ? 1 : 0,
            'is_starting_tile' => $this->isStartingTile ? 1 : 0,
        ];
    }

    /**
     * Create RoomTile from database array
     */
    public static function fromArray(array $data): RoomTile {
        $tile = new RoomTile(
            (int)$data['tile_id'],
            isset($data['original_doors']) ? (int)$data['original_doors'] : (int)$data['doors'],
            (string)$data['color'],
            (int)$data['pips'],
            (bool)$data['has_powder_keg'],
            (bool)$data['has_trapdoor'],
            (bool)$data['is_starting_tile']
        );
        
        $tile->setPosition((int)$data['x'], (int)$data['y']);
        $tile->setFireLevel((int)$data['fire_level']);
        if ((bool)$data['powder_keg_exploded']) {
            $tile->explodePowderKeg();
        }
        
        // Set orientation if provided
        if (isset($data['orientation'])) {
            $tile->setOrientation((int)$data['orientation']);
        }
        
        return $tile;
    }
}
