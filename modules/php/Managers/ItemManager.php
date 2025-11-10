<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\Managers;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\DB\DBManager;
use Bga\Games\DeadMenPax\Models\ItemCardModel;
use Bga\Games\DeadMenPax\DB\PlayerDBManager;

/**
 * Manages the full lifecycle of item cards. All rows are loaded into memory once per request
 * and mutations keep the cache and database in sync.
 */
class ItemManager
{
    private Table $game;
    private DBManager $itemDB;
    private PlayerDBManager $playerDBManager;

    /** @var array<int, ItemCardModel> */
    private array $items = [];
    /** @var array<int, int> playerId => cardId */
    private array $itemsByPlayer = [];
    /** @var array<int, int> cardId => cardId */
    private array $tableItems = [];
    /** @var array<int, int> cardId => cardId */
    private array $discardedItems = [];
    private bool $initialized = false;

    // Card locations
    public const LOCATION_PLAYER = 'player';
    public const LOCATION_TABLE = 'table';
    public const LOCATION_DISCARD = 'discard';

    // Item types (matching material.inc.php)
    public const ITEM_BLANKET = 1;        // Lower Fire Die by 2 once per turn
    public const ITEM_BUCKET = 2;         // Lower Fire Die in adjacent room once per turn
    public const ITEM_COMPASS = 3;        // One free Walk or Run Action per turn
    public const ITEM_DAGGER = 4;         // One free Eliminate Deckhand Action per turn
    public const ITEM_PISTOL = 5;         // Attack from adjacent room, no fatigue loss on fail
    public const ITEM_RUM = 6;            // One free Rest Action per turn
    public const ITEM_SWORD = 7;          // Add 1 to Strength in Battle

    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->itemDB = new DBManager('item_card', ItemCardModel::class, $game);
        $this->playerDBManager = new PlayerDBManager($game);
        $this->reload();
    }

    /**
     * Rebuilds the in-memory cache from the database.
     */
    public function reload(): void
    {
        $this->initialized = false;
        $this->initFromDatabase();
    }

    /**
     * Ensures the cache is ready before any read.
     */
    private function ensureInitialized(): void
    {
        if (!$this->initialized) {
            $this->initFromDatabase();
        }
    }

    /**
     * Loads all item rows into memory and builds quick lookup indexes.
     */
    private function initFromDatabase(): void
    {
        $this->items = [];
        $this->itemsByPlayer = [];
        $this->tableItems = [];
        $this->discardedItems = [];

        /** @var ItemCardModel[] $allItems */
        $allItems = $this->itemDB->getAllObjects();
        foreach ($allItems as $item) {
            $this->items[$item->getCardId()] = $item;
            $this->indexItemLocation($item);
        }

        $this->initialized = true;
    }

    /**
     * Updates secondary indexes for the provided card.
     */
    private function indexItemLocation(ItemCardModel $item): void
    {
        $cardId = $item->getCardId();

        foreach ($this->itemsByPlayer as $playerId => $ownedCardId) {
            if ($ownedCardId === $cardId) {
                unset($this->itemsByPlayer[$playerId]);
                break;
            }
        }

        unset($this->tableItems[$cardId], $this->discardedItems[$cardId]);

        switch ($item->getCardLocation()) {
            case self::LOCATION_PLAYER:
                $this->itemsByPlayer[$item->getCardLocationArg()] = $cardId;
                break;

            case self::LOCATION_TABLE:
                $this->tableItems[$cardId] = $cardId;
                break;

            case self::LOCATION_DISCARD:
                $this->discardedItems[$cardId] = $cardId;
                break;
        }
    }

    /**
     * Writes an item to the database and keeps the cache consistent.
     */
    private function persistItem(ItemCardModel $item): void
    {
        $this->itemDB->saveObjectToDB($item);
        $this->items[$item->getCardId()] = $item;
        $this->indexItemLocation($item);
    }

    /**
     * Convenience accessor used by most public methods.
     */
    private function getItemObject(int $cardId): ?ItemCardModel
    {
        $this->ensureInitialized();
        return $this->items[$cardId] ?? null;
    }

    /**
     * Sets up all item cards during game initialization.
     */
    public function setupItemCards(): void
    {
        $this->itemDB->clearAll();

        $this->items = [];
        $this->itemsByPlayer = [];
        $this->tableItems = [];
        $this->discardedItems = [];
        $this->initialized = true;

        for ($i = 1; $i <= 7; $i++) {
            $card = new ItemCardModel();
            $card->setCardId($i);
            $card->setCardType('item');
            $card->setCardTypeArg($i);
            $card->setCardLocation(self::LOCATION_TABLE);
            $card->setCardLocationArg(0);
            $this->persistItem($card);
        }
    }

    /**
     * Deals one random table item to each player during setup.
     *
     * @return array<int,int> playerId => cardId
     */
    public function dealStartingItemCards(): array
    {
        $this->ensureInitialized();
        $players = $this->game->loadPlayersBasicInfos();
        $assignments = [];

        $tableItemIds = array_keys($this->tableItems);
        shuffle($tableItemIds);

        foreach ($players as $playerId => $_player) {
            $cardId = array_shift($tableItemIds);
            if ($cardId === null) {
                break;
            }

            $itemCard = $this->items[$cardId];
            $itemCard->moveToPlayer((int)$playerId);
            $this->persistItem($itemCard);
            $assignments[(int)$playerId] = $cardId;

            $this->game->notifyPlayer((int)$playerId, 'newItemCard', '', [
                'card' => $itemCard->toArray(),
                'item_name' => $this->getItemName($itemCard->getCardTypeArg()),
                'item_ability' => $this->getItemAbility($itemCard->getCardTypeArg()),
            ]);
        }

        return $assignments;
    }

    /**
     * Returns the current item for a player as an array for UI payloads.
     */
    public function getPlayerItemCard(int $playerId): ?array
    {
        $this->ensureInitialized();

        if (!isset($this->itemsByPlayer[$playerId])) {
            return null;
        }

        return $this->items[$this->itemsByPlayer[$playerId]]->toArray();
    }

    /**
     * Lists table items for selection UIs.
     *
     * @return array<int,array<string,mixed>>
     */
    public function getAvailableItemsOnTable(): array
    {
        $this->ensureInitialized();

        $items = [];
        foreach (array_keys($this->tableItems) as $cardId) {
            $items[] = $this->items[$cardId]->toArray();
        }

        return $items;
    }

    /**
     * Finds the raw item card details by id.
     */
    public function getItemCard(int $cardId): ?array
    {
        $item = $this->getItemObject($cardId);
        return $item ? $item->toArray() : null;
    }

    /**
     * Checks whether an item is currently takeable by the active player.
     */
    public function isItemAvailable(int $itemId, int $sourcePlayerId = 0): bool
    {
        $item = $this->getItemObject($itemId);
        if ($item === null) {
            return false;
        }

        if ($sourcePlayerId > 0) {
            return $item->getCardLocation() === self::LOCATION_PLAYER
                && $item->getCardLocationArg() === $sourcePlayerId;
        }

        return $item->getCardLocation() === self::LOCATION_TABLE;
    }

    /**
     * Convenience wrapper used by TakeActions::swapItem(). Routes to the correct helper.
     */
    public function swapItem(int $playerId, int $itemId, ?int $currentItemId, int $sourcePlayerId): ?array
    {
        if ($sourcePlayerId > 0) {
            return $this->swapItemsBetweenPlayers($sourcePlayerId, $playerId, $itemId);
        }

        return $this->swapItemWithTable($playerId, $itemId, $currentItemId);
    }

    /**
     * Human-readable item name.
     */
    public function getItemName(int $typeArg): string
    {
        return match ($typeArg) {
            self::ITEM_BLANKET => 'Blanket',
            self::ITEM_BUCKET => 'Bucket',
            self::ITEM_COMPASS => 'Compass',
            self::ITEM_DAGGER => 'Dagger',
            self::ITEM_PISTOL => 'Pistol',
            self::ITEM_RUM => 'Rum',
            self::ITEM_SWORD => 'Sword',
            default => 'Unknown Item',
        };
    }

    /**
     * Symbolic ability short-code.
     */
    public function getItemAbility(int $typeArg): string
    {
        return match ($typeArg) {
            self::ITEM_BLANKET => 'blanket',
            self::ITEM_BUCKET => 'bucket',
            self::ITEM_COMPASS => 'compass',
            self::ITEM_DAGGER => 'dagger',
            self::ITEM_PISTOL => 'pistol',
            self::ITEM_RUM => 'rum',
            self::ITEM_SWORD => 'sword',
            default => '',
        };
    }

    /**
     * Full ability description for client side tooltips.
     */
    public function getItemAbilityDescription(int $typeArg): string
    {
        return match ($typeArg) {
            self::ITEM_BLANKET =>
                'May lower a Fire Die by 2 once per turn.',
            self::ITEM_BUCKET =>
                'May lower a Fire Die in an adjacent room once per turn.',
            self::ITEM_COMPASS =>
                'One free Walk or Run Action per turn.',
            self::ITEM_DAGGER =>
                'One free Eliminate Deckhand Action per turn.',
            self::ITEM_PISTOL =>
                'May attack from an adjacent room for one Action once per turn. No fatigue is lost for a failed attack.',
            self::ITEM_RUM =>
                'One free Rest Action per turn.',
            self::ITEM_SWORD =>
                'Add 1 to Strength in Battle.',
            default =>
                'Unknown ability',
        };
    }

    /**
     * Swaps an item between two players and returns the reassignment map.
     *
     * @return array<int,int>|null playerId => cardId
     */
    public function swapItemsBetweenPlayers(int $fromPlayerId, int $toPlayerId, int $itemId): ?array
    {
        $item = $this->getItemObject($itemId);

        if ($item === null
            || $item->getCardLocation() !== self::LOCATION_PLAYER
            || $item->getCardLocationArg() !== $fromPlayerId) {
            return null;
        }
        $previousTargetId = $this->itemsByPlayer[$toPlayerId] ?? null;

        $assignments = [$toPlayerId => $itemId, $fromPlayerId => $previousTargetId];

        $item->moveToPlayer($toPlayerId);
        $this->persistItem($item);

        if ($previousTargetId !== null && $previousTargetId !== $itemId) {
            $targetItem = $this->items[$previousTargetId];
            $targetItem->moveToPlayer($fromPlayerId);
            $this->persistItem($targetItem);
        } else {
            $assignments[$fromPlayerId] = null;
        }

        $this->game->notifyAllPlayers(
            'itemsSwapped',
            clienttranslate('${from_player_name} and ${to_player_name} swapped items'),
            [
                'from_player_id' => $fromPlayerId,
                'to_player_id' => $toPlayerId,
                'from_player_name' => $this->game->getPlayerNameById($fromPlayerId),
                'to_player_name' => $this->game->getPlayerNameById($toPlayerId),
                'swapped_item_id' => $itemId,
                'target_item_id' => $assignments[$fromPlayerId],
            ]
        );

        return $assignments;
    }

    /**
     * Swaps (or simply assigns) an item between a player and the table.
     */
    public function swapItemWithTable(int $playerId, int $tableItemId, ?int $currentItemId = null): ?array
    {
        $tableItem = $this->getItemObject($tableItemId);
        if ($tableItem === null || $tableItem->getCardLocation() !== self::LOCATION_TABLE) {
            return null;
        }

        if ($currentItemId === null) {
            $currentItemId = $this->itemsByPlayer[$playerId] ?? null;
        }

        $assignments = [$playerId => $tableItemId];

        $tableItem->moveToPlayer($playerId);
        $this->persistItem($tableItem);

        if ($currentItemId !== null) {
            $playerItem = $this->getItemObject($currentItemId);
            if ($playerItem !== null && $playerItem->getCardLocation() === self::LOCATION_PLAYER
                && $playerItem->getCardLocationArg() === $playerId) {
                $playerItem->moveToTable();
                $this->persistItem($playerItem);
            }
        }

        $this->game->notifyAllPlayers(
            'itemSwappedWithTable',
            clienttranslate('${player_name} swapped an item with the table'),
            [
                'player_id' => $playerId,
                'player_name' => $this->game->getPlayerNameById($playerId),
                'table_item_id' => $tableItemId,
                'player_item_id' => $currentItemId,
            ]
        );

        return $assignments;
    }

    public function hasItemAbility(int $playerId, string $ability): bool
    {
        $itemCard = $this->getPlayerItemCard($playerId);
        if (!$itemCard) {
            return false;
        }

        return $this->getItemAbility((int)$itemCard['card_type_arg']) === $ability;
    }

    public function getPlayerItemAbility(int $playerId): string
    {
        $itemCard = $this->getPlayerItemCard($playerId);
        return $itemCard ? $this->getItemAbility((int)$itemCard['card_type_arg']) : '';
    }

    /**
     * Returns the combat modifier granted by the player's equipped item.
     */
    public function getItemBattleModifier(int $playerId): int
    {
        $itemCard = $this->getPlayerItemCard($playerId);
        if (!$itemCard) {
            return 0;
        }

        return (int)$itemCard['card_type_arg'] === self::ITEM_SWORD ? 1 : 0;
    }

    /**
     * Destroys an item (moves it to discard) and returns its previous owner id if any.
     */
    public function destroyItem(int $itemId): ?int
    {
        $item = $this->getItemObject($itemId);
        if ($item === null) {
            return null;
        }

        $previousOwner = null;
        if ($item->getCardLocation() === self::LOCATION_PLAYER) {
            $previousOwner = $item->getCardLocationArg();
        }

        $item->discard();
        $this->persistItem($item);

        $this->game->notifyAllPlayers(
            'itemDestroyed',
            clienttranslate('An item has been destroyed!'),
            ['item_id' => $itemId]
        );

        return $previousOwner;
    }

    public function getPlayerItemData(int $playerId): array
    {
        $itemCard = $this->getPlayerItemCard($playerId);

        if (!$itemCard) {
            return [
                'item' => null,
                'item_name' => '',
                'item_ability' => '',
                'item_ability_description' => '',
            ];
        }

        $typeArg = (int)$itemCard['card_type_arg'];
        return [
            'item' => $itemCard,
            'item_name' => $this->getItemName($typeArg),
            'item_ability' => $this->getItemAbility($typeArg),
            'item_ability_description' => $this->getItemAbilityDescription($typeArg),
        ];
    }

    public function getAllItemsData(): array
    {
        $this->ensureInitialized();

        $playerItems = [];
        foreach ($this->itemsByPlayer as $playerId => $cardId) {
            $item = $this->items[$cardId]->toArray();
            $item['player_name'] = $this->game->getPlayerNameById($playerId);
            $playerItems[] = $item;
        }

        $discarded = [];
        foreach (array_keys($this->discardedItems) as $cardId) {
            $discarded[] = $this->items[$cardId]->toArray();
        }

        return [
            'player_items' => $playerItems,
            'table_items' => $this->getAvailableItemsOnTable(),
            'discarded_items' => $discarded,
        ];
    }

    public function useItemAbility(int $playerId, string $ability, array $params = []): bool
    {
        if (!$this->hasItemAbility($playerId, $ability)) {
            return false;
        }

        $this->game->notifyAllPlayers(
            'itemUsed',
            clienttranslate('${player_name} uses ${ability}'),
            [
                'player_id' => $playerId,
                'player_name' => $this->game->getPlayerNameById($playerId),
                'ability' => $ability,
                'params' => $params,
            ]
        );

        return true;
    }

    /**
     * Returns counts of items in each location bucket.
     */
    public function getItemCounts(): array
    {
        $this->ensureInitialized();

        return [
            'players' => count($this->itemsByPlayer),
            'table' => count($this->tableItems),
            'discard' => count($this->discardedItems),
        ];
    }

    /**
     * Moves every card back to the table (used for debugging resets).
     */
    public function resetItems(): void
    {
        $this->ensureInitialized();

        foreach ($this->items as $item) {
            $item->moveToTable();
            $this->persistItem($item);
        }

        $players = $this->playerDBManager->getAllObjects();
        foreach ($players as $player) {
            $player->itemCardId = null;
            $this->playerDBManager->saveObjectToDB($player);
        }

        $this->game->notifyAllPlayers('itemsReset', clienttranslate('All items have been reset to the table'), []);
    }

    /**
     * Provides details for swap dialogs (table items + other player holdings).
     */
    public function getSwappableItems(int $playerId): array
    {
        $this->ensureInitialized();

        $otherPlayers = [];
        foreach ($this->itemsByPlayer as $ownerId => $cardId) {
            if ($ownerId === $playerId) {
                continue;
            }

            $itemArray = $this->items[$cardId]->toArray();
            $itemArray['player_name'] = $this->game->getPlayerNameById($ownerId);
            $otherPlayers[] = $itemArray;
        }

        return [
            'player_item' => $this->getPlayerItemCard($playerId),
            'table_items' => $this->getAvailableItemsOnTable(),
            'other_player_items' => $otherPlayers,
        ];
    }
}
