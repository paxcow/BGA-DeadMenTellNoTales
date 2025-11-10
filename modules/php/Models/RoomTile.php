<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\Models;

use Bga\Games\DeadMenPax\DB\dbColumn;
use Bga\Games\DeadMenPax\DB\dbKey;

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
    public const COLOR_DINGHY = 'dinghy'; // Special color for dinghy tile

    // Orientation constants (in degrees)
    public const ORIENTATION_0 = 0;     // Original orientation
    public const ORIENTATION_90 = 90;   // 90 degrees clockwise
    public const ORIENTATION_180 = 180; // 180 degrees
    public const ORIENTATION_270 = 270; // 270 degrees clockwise (90 counter-clockwise)

    #[dbKey('tile_id')]
    private int $id;

    private int $originalDoors;  // Doors in original orientation

    #[dbColumn('doors')]
    private int $doors;          // Current doors after rotation

    #[dbColumn('color')]
    private string $color;

    #[dbColumn('pips')]
    private int $pips;

    #[dbColumn('has_powder_keg')]
    private bool $hasPowderKeg;

    #[dbColumn('has_trapdoor')]
    private bool $hasTrapdoor;

    #[dbColumn('x')]
    private ?int $x;

    #[dbColumn('y')]
    private ?int $y;

    #[dbColumn('fire_level')]
    private int $fireLevel;

    #[dbColumn('deckhand_count')]
    private int $deckhandCount;

    #[dbColumn('powder_keg_exploded')]
    private bool $powderKegExploded;

    #[dbColumn('is_starting_tile')]
    private bool $isStartingTile;

    #[dbColumn('orientation')]
    private int $orientation;    // Current orientation in degrees

    #[dbColumn('tile_type')]
    private string $tileType;

    #[dbColumn('is_exploded')]
    private bool $isExploded;

    #[dbColumn('tile_order')]
    private ?int $tileOrder;

    /**
     * Constructor.
     *
     * @param int $id The ID of the tile.
     * @param int $doors The doors mask.
     * @param string $color The color of the tile.
     * @param int $pips The pips on the tile.
     * @param bool $hasPowderKeg Whether the tile has a powder keg.
     * @param bool $hasTrapdoor Whether the tile has a trapdoor.
     * @param bool $isStartingTile Whether the tile is a starting tile.
     */
    public function __construct(
        int $id,
        int $doors,
        string $color,
        int $pips,
        bool $hasPowderKeg = false,
        bool $hasTrapdoor = false,
        bool $isStartingTile = false,
        int $deckhandCount = 0,
        string $tileType = 'room',
        bool $isExploded = false,
        ?int $tileOrder = null
    ) {
        $this->id = $id;
        $this->originalDoors = $doors;
        $this->doors = $doors;  // Start with original doors
        $this->color = $color;
        $this->pips = $pips;
        $this->hasPowderKeg = $hasPowderKeg;
        $this->hasTrapdoor = $hasTrapdoor;
        $this->isStartingTile = $isStartingTile;
        $this->x = null;
        $this->y = null;
        $this->fireLevel = 0;
        $this->deckhandCount = max(0, $deckhandCount);
        $this->powderKegExploded = false;
        $this->orientation = self::ORIENTATION_0;
        $this->tileType = $tileType;
        $this->isExploded = $isExploded;
        $this->tileOrder = $tileOrder;
    }

    /**
     * Gets the tile ID.
     *
     * @return int
     */
    public function getId(): int { return $this->id; }
    /**
     * Gets the current doors mask.
     *
     * @return int
     */
    public function getDoors(): int { return $this->doors; }
    /**
     * Gets the original doors mask.
     *
     * @return int
     */
    public function getOriginalDoors(): int { return $this->originalDoors; }
    /**
     * Gets the color.
     *
     * @return string
     */
    public function getColor(): string { return $this->color; }
    /**
     * Gets the pips.
     *
     * @return int
     */
    public function getPips(): int { return $this->pips; }
    /**
     * Checks if the tile has a powder keg.
     *
     * @return bool
     */
    public function hasPowderKeg(): bool { return $this->hasPowderKeg; }
    /**
     * Checks if the tile has a trapdoor.
     *
     * @return bool
     */
    public function hasTrapdoor(): bool { return $this->hasTrapdoor; }
    /**
     * Gets the x-coordinate.
     *
     * @return int|null
     */
    public function getX(): ?int { return $this->x; }
    /**
     * Gets the y-coordinate.
     *
     * @return int|null
     */
    public function getY(): ?int { return $this->y; }
    /**
     * Gets the fire level.
     *
     * @return int
     */
    public function getFireLevel(): int { return $this->fireLevel; }
    /**
     * Gets the number of deckhands in the room.
     *
     * @return int
     */
    public function getDeckhandCount(): int { return $this->deckhandCount; }
    /**
     * Checks if the powder keg has exploded.
     *
     * @return bool
     */
    public function isPowderKegExploded(): bool { return $this->powderKegExploded; }
    /**
     * Checks if the tile is a starting tile.
     *
     * @return bool
     */
    public function isStartingTile(): bool { return $this->isStartingTile; }
    /**
     * Gets the orientation.
     *
     * @return int
     */
    public function getOrientation(): int { return $this->orientation; }

    /**
     * Gets the tile type.
     */
    public function getTileType(): string
    {
        return $this->tileType;
    }

    /**
     * Checks if the tile is marked as exploded.
     */
    public function isExploded(): bool
    {
        return $this->isExploded;
    }

    /**
     * Gets the deck order value for this tile.
     */
    public function getTileOrder(): ?int
    {
        return $this->tileOrder;
    }

    /**
     * Sets the position.
     *
     * @param int $x The x-coordinate.
     * @param int $y The y-coordinate.
     */
    public function setPosition(?int $x, ?int $y): void {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Sets the fire level.
     *
     * @param int $level The fire level.
     */
    public function setFireLevel(int $level): void {
        $this->fireLevel = max(0, min(6, $level));
    }

    /**
     * Sets the deckhand count.
     *
     * @param int $value
     */
    public function setDeckhandCount(int $value): void
    {
        $this->deckhandCount = max(0, $value);
    }

    public function setTileType(string $tileType): void
    {
        $this->tileType = $tileType;
    }

    public function setExploded(bool $exploded): void
    {
        $this->isExploded = $exploded;
    }

    public function setTileOrder(?int $tileOrder): void
    {
        $this->tileOrder = $tileOrder;
    }

    /**
     * Increases the fire level.
     *
     * @param int $amount The amount to increase by.
     */
    public function increaseFireLevel(int $amount = 1): void {
        $this->setFireLevel($this->fireLevel + $amount);
    }

    /**
     * Decreases the fire level.
     *
     * @param int $amount The amount to decrease by.
     */
    public function decreaseFireLevel(int $amount = 1): void {
        $this->setFireLevel($this->fireLevel - $amount);
    }

    /**
     * Adds deckhands to the room tile.
     *
     * @param int $delta
     */
    public function addDeckhands(int $delta): void
    {
        if ($delta <= 0) {
            return;
        }

        $this->deckhandCount += $delta;
    }

    /**
     * Removes deckhands from the room tile.
     *
     * @param int $delta
     */
    public function removeDeckhands(int $delta): void
    {
        if ($delta <= 0) {
            return;
        }

        $this->deckhandCount = max(0, $this->deckhandCount - $delta);
    }

    /**
     * Explodes the powder keg.
     */
    public function explodePowderKeg(): void {
        $this->powderKegExploded = true;
    }

    /**
     * Checks if the tile has a door in a given direction.
     *
     * @param int $direction The direction to check.
     * @return bool
     */
    public function hasDoor(int $direction): bool {
        return ($this->doors & $direction) !== 0;
    }

    /**
     * Checks if the tile has a north door.
     *
     * @return bool
     */
    public function hasNorthDoor(): bool {
        return $this->hasDoor(self::DOOR_NORTH);
    }

    /**
     * Checks if the tile has an east door.
     *
     * @return bool
     */
    public function hasEastDoor(): bool {
        return $this->hasDoor(self::DOOR_EAST);
    }

    /**
     * Checks if the tile has a south door.
     *
     * @return bool
     */
    public function hasSouthDoor(): bool {
        return $this->hasDoor(self::DOOR_SOUTH);
    }

    /**
     * Checks if the tile has a west door.
     *
     * @return bool
     */
    public function hasWestDoor(): bool {
        return $this->hasDoor(self::DOOR_WEST);
    }

    /**
     * Gets the doors as an array of directions.
     *
     * @return array
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
     * Checks if this tile can connect to another tile in a given direction.
     *
     * @param RoomTile $otherTile The other tile.
     * @param int $direction The direction to check.
     * @return bool
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
     * Gets the opposite direction.
     *
     * @param int $direction The direction.
     * @return int
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
     * Checks if the tile will explode.
     *
     * @return bool
     */
    public function willExplode(): bool {
        return $this->fireLevel >= 6 || ($this->hasPowderKeg && !$this->powderKegExploded && $this->fireLevel > 0);
    }

    /**
     * Sets the tile orientation and updates door positions.
     *
     * @param int $orientation The new orientation.
     */
    public function setOrientation(int $orientation): void {
        // Normalize orientation to 0, 90, 180, or 270
        $orientation = $orientation % 360;
        if ($orientation < 0) $orientation += 360;
        
        $this->orientation = $orientation;
        $this->doors = $this->rotateDoorsToOrientation($this->originalDoors, $orientation);
    }

    /**
     * Rotates the tile clockwise by 90 degrees.
     */
    public function rotateClockwise(): void {
        $newOrientation = ($this->orientation + 90) % 360;
        $this->setOrientation($newOrientation);
    }

    /**
     * Rotates the tile counter-clockwise by 90 degrees.
     */
    public function rotateCounterClockwise(): void {
        $newOrientation = ($this->orientation - 90) % 360;
        if ($newOrientation < 0) $newOrientation += 360;
        $this->setOrientation($newOrientation);
    }

    /**
     * Resets the tile to its original orientation.
     */
    public function resetOrientation(): void {
        $this->setOrientation(self::ORIENTATION_0);
    }

    /**
     * Rotates doors based on orientation.
     *
     * @param int $doors The doors mask.
     * @param int $orientation The orientation.
     * @return int
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
     * Rotates doors 90 degrees clockwise.
     *
     * @param int $doors The doors mask.
     * @return int
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
     * Rotates doors 90 degrees counter-clockwise.
     *
     * @param int $doors The doors mask.
     * @return int
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
     * Gets all possible orientations for this tile.
     *
     * @return array
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
     * Creates a copy of this tile with a specific orientation.
     *
     * @param int $orientation The new orientation.
     * @return RoomTile
     */
    public function withOrientation(int $orientation): RoomTile {
        $copy = clone $this;
        $copy->setOrientation($orientation);
        return $copy;
    }

    /**
     * Gets door positions for a specific orientation without changing the tile.
     *
     * @param int $orientation The orientation.
     * @return int
     */
    public function getDoorsForOrientation(int $orientation): int {
        return $this->rotateDoorsToOrientation($this->originalDoors, $orientation);
    }

    /**
     * Gets the tile data as an array for database storage.
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'tile_id' => $this->id,
            'tile_type' => $this->tileType,
            'x' => $this->x,
            'y' => $this->y,
            'fire_level' => $this->fireLevel,
            'has_powder_keg' => $this->hasPowderKeg ? 1 : 0,
            'powder_keg_exploded' => $this->powderKegExploded ? 1 : 0,
            'doors' => $this->doors,
            'orientation' => $this->orientation,
            'color' => $this->color,
            'pips' => $this->pips,
            'has_trapdoor' => $this->hasTrapdoor ? 1 : 0,
            'is_starting_tile' => $this->isStartingTile ? 1 : 0,
            'deckhand_count' => $this->deckhandCount,
            'is_exploded' => $this->isExploded ? 1 : 0,
            'tile_order' => $this->tileOrder,
        ];
    }

    /**
     * Creates a RoomTile from a database array.
     *
     * @param array $data The data from the database.
     * @return RoomTile
     */
    public static function fromArray(array $data): RoomTile {
        $tile = new RoomTile(
            (int)$data['tile_id'],
            isset($data['original_doors']) ? (int)$data['original_doors'] : (int)$data['doors'],
            (string)($data['color'] ?? self::COLOR_RED),
            (int)($data['pips'] ?? 0),
            (bool)($data['has_powder_keg'] ?? false),
            (bool)($data['has_trapdoor'] ?? false),
            (bool)($data['is_starting_tile'] ?? false),
            isset($data['deckhand_count']) ? (int)$data['deckhand_count'] : 0,
            (string)($data['tile_type'] ?? 'room'),
            (bool)($data['is_exploded'] ?? false),
            array_key_exists('tile_order', $data) ? ($data['tile_order'] === null ? null : (int)$data['tile_order']) : null
        );

        $x = array_key_exists('x', $data) ? ($data['x'] === null ? null : (int)$data['x']) : null;
        $y = array_key_exists('y', $data) ? ($data['y'] === null ? null : (int)$data['y']) : null;
        $tile->setPosition($x, $y);
        $tile->setFireLevel((int)$data['fire_level']);
        if (isset($data['deckhand_count'])) {
            $tile->setDeckhandCount((int)$data['deckhand_count']);
        }
        if ((bool)$data['powder_keg_exploded']) {
            $tile->explodePowderKeg();
        }

        if (isset($data['is_exploded'])) {
            $tile->setExploded((bool)$data['is_exploded']);
        }

        // Set orientation if provided
        if (isset($data['orientation'])) {
            $tile->setOrientation((int)$data['orientation']);
        }
        
        return $tile;
    }
}
