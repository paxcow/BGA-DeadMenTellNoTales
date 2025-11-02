<?php

namespace Bga\Games\DeadMenPax\DB\Models;

use Bga\Games\DeadMenPax\DB\dbColumn;
use Bga\Games\DeadMenPax\DB\dbKey;

/**
 * Room tile model representing the room_tile database table
 */
class RoomTileModel
{
    #[dbKey('tile_id')]
    private int $tileId;

    #[dbColumn('tile_type')]
    private string $tileType;

    #[dbColumn('x')]
    private int $x;

    #[dbColumn('y')]
    private int $y;

    #[dbColumn('fire_level')]
    private int $fireLevel = 0;

    #[dbColumn('has_powder_keg')]
    private bool $hasPowderKeg = false;

    #[dbColumn('powder_keg_exploded')]
    private bool $powderKegExploded = false;

    #[dbColumn('is_exploded')]
    private bool $isExploded = false;

    #[dbColumn('doors')]
    private int $doors = 0;

    #[dbColumn('original_doors')]
    private int $originalDoors = 0;

    #[dbColumn('orientation')]
    private int $orientation = 0;

    #[dbColumn('color')]
    private string $color = 'red';

    #[dbColumn('pips')]
    private int $pips = 1;

    #[dbColumn('has_trapdoor')]
    private bool $hasTrapdoor = false;

    #[dbColumn('is_starting_tile')]
    private bool $isStartingTile = false;

    /**
     * Gets the tile ID.
     *
     * @return int
     */
    public function getTileId(): int
    {
        return $this->tileId;
    }

    /**
     * Gets the tile type.
     *
     * @return string
     */
    public function getTileType(): string
    {
        return $this->tileType;
    }

    /**
     * Gets the x-coordinate.
     *
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Gets the y-coordinate.
     *
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Gets the fire level.
     *
     * @return int
     */
    public function getFireLevel(): int
    {
        return $this->fireLevel;
    }

    /**
     * Checks if the tile has a powder keg.
     *
     * @return bool
     */
    public function hasPowderKeg(): bool
    {
        return $this->hasPowderKeg;
    }

    /**
     * Checks if the powder keg has exploded.
     *
     * @return bool
     */
    public function isPowderKegExploded(): bool
    {
        return $this->powderKegExploded;
    }

    /**
     * Checks if the tile has exploded.
     *
     * @return bool
     */
    public function isExploded(): bool
    {
        return $this->isExploded;
    }

    /**
     * Gets the doors mask.
     *
     * @return int
     */
    public function getDoors(): int
    {
        return $this->doors;
    }

    /**
     * Gets the original doors mask.
     *
     * @return int
     */
    public function getOriginalDoors(): int
    {
        return $this->originalDoors;
    }

    /**
     * Gets the orientation.
     *
     * @return int
     */
    public function getOrientation(): int
    {
        return $this->orientation;
    }

    /**
     * Gets the color.
     *
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Gets the pips.
     *
     * @return int
     */
    public function getPips(): int
    {
        return $this->pips;
    }

    /**
     * Checks if the tile has a trapdoor.
     *
     * @return bool
     */
    public function hasTrapdoor(): bool
    {
        return $this->hasTrapdoor;
    }

    /**
     * Checks if the tile is a starting tile.
     *
     * @return bool
     */
    public function isStartingTile(): bool
    {
        return $this->isStartingTile;
    }

    /**
     * Sets the tile ID.
     *
     * @param int $tileId
     */
    public function setTileId(int $tileId): void
    {
        $this->tileId = $tileId;
    }

    /**
     * Sets the tile type.
     *
     * @param string $tileType
     */
    public function setTileType(string $tileType): void
    {
        $this->tileType = $tileType;
    }

    /**
     * Sets the position.
     *
     * @param int $x
     * @param int $y
     */
    public function setPosition(int $x, int $y): void
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Sets the x-coordinate.
     *
     * @param int $x
     */
    public function setX(int $x): void
    {
        $this->x = $x;
    }

    /**
     * Sets the y-coordinate.
     *
     * @param int $y
     */
    public function setY(int $y): void
    {
        $this->y = $y;
    }

    /**
     * Sets the fire level.
     *
     * @param int $fireLevel
     */
    public function setFireLevel(int $fireLevel): void
    {
        $this->fireLevel = max(0, $fireLevel);
    }

    /**
     * Increases the fire level.
     *
     * @param int $amount
     */
    public function increaseFireLevel(int $amount = 1): void
    {
        $this->fireLevel += $amount;
    }

    /**
     * Decreases the fire level.
     *
     * @param int $amount
     */
    public function decreaseFireLevel(int $amount = 1): void
    {
        $this->fireLevel = max(0, $this->fireLevel - $amount);
    }

    /**
     * Sets whether the tile has a powder keg.
     *
     * @param bool $hasPowderKeg
     */
    public function setHasPowderKeg(bool $hasPowderKeg): void
    {
        $this->hasPowderKeg = $hasPowderKeg;
    }

    /**
     * Explodes the powder keg.
     */
    public function explodePowderKeg(): void
    {
        $this->powderKegExploded = true;
    }

    /**
     * Sets whether the powder keg has exploded.
     *
     * @param bool $exploded
     */
    public function setPowderKegExploded(bool $exploded): void
    {
        $this->powderKegExploded = $exploded;
    }

    /**
     * Sets whether the tile has exploded.
     *
     * @param bool $exploded
     */
    public function setExploded(bool $exploded): void
    {
        $this->isExploded = $exploded;
    }

    /**
     * Sets the doors mask.
     *
     * @param int $doors
     */
    public function setDoors(int $doors): void
    {
        $this->doors = $doors;
    }

    /**
     * Sets the original doors mask.
     *
     * @param int $originalDoors
     */
    public function setOriginalDoors(int $originalDoors): void
    {
        $this->originalDoors = $originalDoors;
    }

    /**
     * Sets the orientation.
     *
     * @param int $orientation
     */
    public function setOrientation(int $orientation): void
    {
        $this->orientation = $orientation;
    }

    /**
     * Sets the color.
     *
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * Sets the pips.
     *
     * @param int $pips
     */
    public function setPips(int $pips): void
    {
        $this->pips = $pips;
    }

    /**
     * Sets whether the tile has a trapdoor.
     *
     * @param bool $hasTrapdoor
     */
    public function setHasTrapdoor(bool $hasTrapdoor): void
    {
        $this->hasTrapdoor = $hasTrapdoor;
    }

    /**
     * Sets whether the tile is a starting tile.
     *
     * @param bool $isStartingTile
     */
    public function setIsStartingTile(bool $isStartingTile): void
    {
        $this->isStartingTile = $isStartingTile;
    }

    /**
     * Checks if the tile will explode.
     *
     * @return bool
     */
    public function willExplode(): bool
    {
        return $this->fireLevel >= 6 || ($this->hasPowderKeg && !$this->powderKegExploded && $this->fireLevel > 0);
    }

    /**
     * Checks if the tile has a door in a given direction.
     *
     * @param int $direction
     * @return bool
     */
    public function hasDoor(int $direction): bool
    {
        return ($this->doors & $direction) !== 0;
    }

    /**
     * Creates a RoomTileModel from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $model = new self();
        $model->tileId = (int) $data['tile_id'];
        $model->tileType = $data['tile_type'] ?? '';
        $model->x = (int) $data['x'];
        $model->y = (int) $data['y'];
        $model->fireLevel = (int) $data['fire_level'];
        $model->hasPowderKeg = (bool) $data['has_powder_keg'];
        $model->powderKegExploded = (bool) $data['powder_keg_exploded'];
        $model->isExploded = (bool) $data['is_exploded'];
        $model->doors = (int) $data['doors'];
        $model->originalDoors = (int) $data['original_doors'];
        $model->orientation = (int) $data['orientation'];
        $model->color = $data['color'] ?? 'red';
        $model->pips = (int) $data['pips'];
        $model->hasTrapdoor = (bool) $data['has_trapdoor'];
        $model->isStartingTile = (bool) $data['is_starting_tile'];
        
        return $model;
    }

    /**
     * Converts the RoomTileModel to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'tile_id' => $this->tileId,
            'tile_type' => $this->tileType,
            'x' => $this->x,
            'y' => $this->y,
            'fire_level' => $this->fireLevel,
            'has_powder_keg' => $this->hasPowderKeg ? 1 : 0,
            'powder_keg_exploded' => $this->powderKegExploded ? 1 : 0,
            'is_exploded' => $this->isExploded ? 1 : 0,
            'doors' => $this->doors,
            'original_doors' => $this->originalDoors,
            'orientation' => $this->orientation,
            'color' => $this->color,
            'pips' => $this->pips,
            'has_trapdoor' => $this->hasTrapdoor ? 1 : 0,
            'is_starting_tile' => $this->isStartingTile ? 1 : 0,
        ];
    }
}
