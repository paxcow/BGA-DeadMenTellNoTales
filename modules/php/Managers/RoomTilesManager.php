<?php

namespace Bga\Games\DeadMenPax\Managers;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\Models\RoomTile;

class RoomTilesManager extends DBManager
{
    /** @var string[] */
    private const STATIC_FIELDS = [
        'tile_type',
        'color',
        'pips',
        'has_powder_keg',
        'has_trapdoor',
        'is_starting_tile',
    ];

    /** @var array<int,RoomTile> */
    private array $tiles = [];

    /** @var array<int,array<string,mixed>> */
    private array $tileRows = [];

    /** @var array<int,array<string,mixed>> */
    private array $tileDefinitions = [];

    /**
     * Creates the manager that operates on the `room_tile` table.
     *
     * Example:
     * ```php
     * $roomTilesManager = new RoomTilesManager($game, $definitions);
     * ```
     */
    public function __construct(Table $game, array $tileDefinitions)
    {
        parent::__construct('room_tile', RoomTile::class, $game);
        $this->setTileDefinitions($tileDefinitions);
        $this->reloadTiles();
    }

    /**
     * Seeds the database with every tile definition and randomizes draw order.
     *
     * Example:
     * ```php
     * $manager->initializeTiles($game->getAllRoomTileDefinitions());
     * ```
     *
     * @param array<int,array<string,mixed>> $tileDefinitions
     */
    public function initializeTiles(?array $tileDefinitions = null): void
    {
        if ($tileDefinitions !== null) {
            $this->setTileDefinitions($tileDefinitions);
        }

        if (empty($this->tileDefinitions)) {
            return;
        }

        $indexedDefinitions = $this->tileDefinitions;
        $drawableIds = array_keys(array_filter(
            $indexedDefinitions,
            static fn(array $definition): bool => empty($definition['is_starting_tile'])
        ));

        $shuffledIds = $this->shuffleIds($drawableIds);
        $orderMap = [];
        foreach ($shuffledIds as $position => $tileId) {
            $orderMap[$tileId] = $position + 1;
        }

        $this->clearAll();

        foreach ($indexedDefinitions as $tileId => $definition) {
            $tileData = [
                'tile_id' => $tileId,
                'tile_type' => (string) ($definition['tile_type'] ?? 'room'),
                'x' => null,
                'y' => null,
                'fire_level' => 0,
                'deckhand_count' => 0,
                'has_powder_keg' => (bool) ($definition['has_powder_keg'] ?? false),
                'powder_keg_exploded' => 0,
                'is_exploded' => 0,
                'doors' => (int) ($definition['doors'] ?? 0),
                'original_doors' => (int) ($definition['doors'] ?? 0),
                'orientation' => RoomTile::ORIENTATION_0,
                'color' => (string) ($definition['color'] ?? RoomTile::COLOR_RED),
                'pips' => (int) ($definition['pips'] ?? 0),
                'has_trapdoor' => (bool) ($definition['has_trapdoor'] ?? false),
                'is_starting_tile' => (bool) ($definition['is_starting_tile'] ?? false),
                'tile_order' => $orderMap[$tileId] ?? null,
            ];

            $tile = RoomTile::fromArray($tileData);
            $this->saveObjectToDB($tile);
        }

        $this->reloadTiles();
    }

    /**
     * Reloads the in-memory cache of RoomTile objects from the database.
     */
    public function reloadTiles(): void
    {
        $this->tileRows = [];
        $this->tiles = [];

        $rows = $this->getAllRows();
        foreach ($rows as $row) {
            $tileId = (int) $row['tile_id'];
            $this->tileRows[$tileId] = $row;

            if (!isset($this->tileDefinitions[$tileId])) {
                continue;
            }

            $hydrated = $this->hydrateTileRow($row, $this->tileDefinitions[$tileId]);
            $this->tiles[$tileId] = RoomTile::fromArray($hydrated);
        }
    }

    /**
     * Returns every cached RoomTile, including those off-board.
     */
    public function getTiles(): array
    {
        return $this->tiles;
    }

    /**
     * Returns only the tiles that currently have board coordinates.
     */
    public function getPlacedTiles(): array
    {
        return array_filter(
            $this->tiles,
            static fn(RoomTile $tile): bool => $tile->getX() !== null && $tile->getY() !== null
        );
    }

    public function getTile(int $tileId): ?RoomTile
    {
        return $this->tiles[$tileId] ?? null;
    }

    /**
     * Merges a database row with its static definition.
     *
     * Example:
     * ```php
     * $hydrated = $manager->hydrateTileRow($row, $definition);
     * ```
     *
     * @param array<string,mixed> $row
     * @param array<string,mixed> $definition
     * @return array<string,mixed>
     */
    public function hydrateTileRow(array $row, array $definition): array
    {
        $hydrated = $row;
        foreach (self::STATIC_FIELDS as $field) {
            if (array_key_exists($field, $definition)) {
                $hydrated[$field] = $definition[$field];
            }
        }

        $hydrated['original_doors'] = (int) ($definition['doors'] ?? $row['doors'] ?? 0);
        $hydrated['tile_id'] = (int) $row['tile_id'];

        return $hydrated;
    }

    /**
     * Persists the provided RoomTile back to the database while preserving draw order.
     *
     * Example:
     * ```php
     * $manager->saveTile($roomTile);
     * ```
     */
    public function saveTile(RoomTile $tile): void
    {
        $this->saveObjectToDB($tile);

        $this->tileRows[$tile->getId()] = $tile->toArray();
        $this->tiles[$tile->getId()] = $tile;
    }

    /**
     * Draws the next available tile ID based on the persisted shuffle order.
     *
     * Example:
     * ```php
     * $nextTileId = $manager->drawNextTileId();
     * ```
     */
    public function drawNextTileId(): int
    {
        $candidate = null;
        $candidateOrder = null;

        foreach ($this->tileRows as $row) {
            $isDrawable = ($row['x'] ?? null) === null
                && ($row['y'] ?? null) === null
                && (int) ($row['is_starting_tile'] ?? 0) === 0;

            if (!$isDrawable) {
                continue;
            }

            $order = isset($row['tile_order']) ? (int) $row['tile_order'] : PHP_INT_MAX;

            if ($candidateOrder === null
                || $order < $candidateOrder
                || ($order === $candidateOrder && (int) $row['tile_id'] < (int) ($candidate['tile_id'] ?? PHP_INT_MAX))) {
                $candidate = $row;
                $candidateOrder = $order;
            }
        }

        if ($candidate === null) {
            throw new \BgaSystemException('No remaining room tiles to draw.');
        }

        return (int) $candidate['tile_id'];
    }

    /**
     * Indexes raw material definitions by their tile id.
     *
     * Example:
     * ```php
     * $indexed = $manager->indexDefinitions($definitions);
     * ```
     *
     * @param array<int,array<string,mixed>> $definitions
     * @return array<int,array<string,mixed>>
     */
    private function indexDefinitions(array $definitions): array
    {
        $indexed = [];
        foreach ($definitions as $key => $definition) {
            $tileId = (int) ($definition['id'] ?? $key);
            if ($tileId === 0) {
                throw new \BgaSystemException('Room tile definition missing id.');
            }

            $definition['id'] = $tileId;
            $indexed[$tileId] = $definition;
        }

        ksort($indexed);
        return $indexed;
    }

    /**
     * Shuffles a list of tile ids using the BGA RNG so draws are reproducible.
     *
     * @param array<int,int> $ids
     * @return array<int,int>
     */
    private function shuffleIds(array $ids): array
    {
        for ($i = count($ids) - 1; $i > 0; $i--) {
            $j = $this->game->bga_rand(0, $i);
            if ($i !== $j) {
                [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            }
        }

        return $ids;
    }

    /**
     * Sets the cached tile definitions.
     */
    private function setTileDefinitions(array $definitions): void
    {
        $this->tileDefinitions = $this->indexDefinitions($definitions);
    }
}
