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

    // Getters
    public function getTileId(): int
    {
        return $this->tileId;
    }

    public function getTileType(): string
    {
        return $this->tileType;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getFireLevel(): int
    {
        return $this->fireLevel;
    }

    public function hasPowderKeg(): bool
    {
        return $this->hasPowderKeg;
    }

    public function isPowderKegExploded(): bool
    {
        return $this->powderKegExploded;
    }

    public function isExploded(): bool
    {
        return $this->isExploded;
    }

    public function getDoors(): int
    {
        return $this->doors;
    }

    public function getOriginalDoors(): int
    {
        return $this->originalDoors;
    }

    public function getOrientation(): int
    {
        return $this->orientation;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getPips(): int
    {
        return $this->pips;
    }

    public function hasTrapdoor(): bool
    {
        return $this->hasTrapdoor;
    }

    public function isStartingTile(): bool
    {
        return $this->isStartingTile;
    }

    // Setters
    public function setTileId(int $tileId): void
    {
        $this->tileId = $tileId;
    }

    public function setTileType(string $tileType): void
    {
        $this->tileType = $tileType;
    }

    public function setPosition(int $x, int $y): void
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function setX(int $x): void
    {
        $this->x = $x;
    }

    public function setY(int $y): void
    {
        $this->y = $y;
    }

    public function setFireLevel(int $fireLevel): void
    {
        $this->fireLevel = max(0, $fireLevel);
    }

    public function increaseFireLevel(int $amount = 1): void
    {
        $this->fireLevel += $amount;
    }

    public function decreaseFireLevel(int $amount = 1): void
    {
        $this->fireLevel = max(0, $this->fireLevel - $amount);
    }

    public function setHasPowderKeg(bool $hasPowderKeg): void
    {
        $this->hasPowderKeg = $hasPowderKeg;
    }

    public function explodePowderKeg(): void
    {
        $this->powderKegExploded = true;
    }

    public function setPowderKegExploded(bool $exploded): void
    {
        $this->powderKegExploded = $exploded;
    }

    public function setExploded(bool $exploded): void
    {
        $this->isExploded = $exploded;
    }

    public function setDoors(int $doors): void
    {
        $this->doors = $doors;
    }

    public function setOriginalDoors(int $originalDoors): void
    {
        $this->originalDoors = $originalDoors;
    }

    public function setOrientation(int $orientation): void
    {
        $this->orientation = $orientation;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function setPips(int $pips): void
    {
        $this->pips = $pips;
    }

    public function setHasTrapdoor(bool $hasTrapdoor): void
    {
        $this->hasTrapdoor = $hasTrapdoor;
    }

    public function setIsStartingTile(bool $isStartingTile): void
    {
        $this->isStartingTile = $isStartingTile;
    }

    // Business logic methods
    public function willExplode(): bool
    {
        return $this->fireLevel >= 6 || ($this->hasPowderKeg && !$this->powderKegExploded && $this->fireLevel > 0);
    }

    public function hasDoor(int $direction): bool
    {
        return ($this->doors & $direction) !== 0;
    }

    // Create from array (for compatibility with existing RoomTile class)
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

    // Convert to array (for compatibility with existing code)
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
