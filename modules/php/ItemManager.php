<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\DB\DBManager;
use Bga\Games\DeadMenPax\DB\Models\ItemCardModel;
use Bga\Games\DeadMenPax\DB\PlayerDBManager;

/**
 * Manages Item cards with custom swapping logic
 * 7 item cards total - one per player initially, remainder on table for swapping
 * Uses separate item_card table, no DECK component needed
 */
class ItemManager
{
    private Table $game;
    private DBManager $itemDB;
    private PlayerDBManager $playerDBManager;
    
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
    }

    /**
     * Setup all 7 Item cards during game initialization
     */
    public function setupItemCards(): void
    {
        // Clear any existing item cards using DBManager
        $this->itemDB->clearAll();
        
        // Create all 7 unique item cards using DBManager
        for ($i = 1; $i <= 7; $i++) {
            $card = new ItemCardModel();
            $card->setCardId($i);
            $card->setCardType('item');
            $card->setCardTypeArg($i);
            $card->setCardLocation(self::LOCATION_TABLE);
            $card->setCardLocationArg(0);
            
            $this->itemDB->saveObjectToDB($card);
        }
    }

    /**
     * Deal starting item cards to all players (one per player)
     */
    public function dealStartingItemCards(): array
    {
        $players = $this->game->loadPlayersBasicInfos();
        $assignments = [];

        // Get available items from table using DBManager
        $allItems = $this->itemDB->getAllObjects();
        $availableItems = array_filter($allItems, function ($item) {
            return $item->getCardLocation() === self::LOCATION_TABLE;
        });
        $availableItems = array_values($availableItems); // Reindex array
        shuffle($availableItems);

        $itemIndex = 0;
        foreach ($players as $playerId => $player) {
            if (isset($availableItems[$itemIndex])) {
                $itemCard = $availableItems[$itemIndex];

                // Move item to player using DBManager
                $itemCard->setCardLocation(self::LOCATION_PLAYER);
                $itemCard->setCardLocationArg($playerId);
                $this->itemDB->saveObjectToDB($itemCard);

                // Prepare assignment for the Game mediator
                $assignments[$playerId] = $itemCard->getCardId();

                // Notify player about their item
                $this->game->notifyPlayer($playerId, 'newItemCard', '', [
                    'card' => $itemCard->toArray(),
                    'item_name' => $this->getItemName($itemCard->getCardTypeArg()),
                    'item_ability' => $this->getItemAbility($itemCard->getCardTypeArg())
                ]);

                $itemIndex++;
            }
        }
        return $assignments;
    }

    /**
     * Get player's current item card
     */
    public function getPlayerItemCard(int $playerId): ?array
    {
        $allItems = $this->itemDB->getAllObjects();
        foreach ($allItems as $item) {
            if ($item->getCardLocation() === self::LOCATION_PLAYER && $item->getCardLocationArg() === $playerId) {
                return $item->toArray();
            }
        }
        return null;
    }

    /**
     * Get all items available on the table (face up)
     */
    public function getAvailableItemsOnTable(): array
    {
        $allItems = $this->itemDB->getAllObjects();
        $tableItems = [];
        foreach ($allItems as $item) {
            if ($item->getCardLocation() === self::LOCATION_TABLE) {
                $tableItems[] = $item->toArray();
            }
        }
        return $tableItems;
    }

    /**
     * Get specific item card by ID
     */
    public function getItemCard(int $cardId): ?array
    {
        $item = $this->itemDB->createObjectFromDB($cardId);
        return $item ? $item->toArray() : null;
    }

    /**
     * Get item name by type_arg
     */
    public function getItemName(int $typeArg): string
    {
        switch ($typeArg) {
            case self::ITEM_BLANKET:
                return 'Blanket';
            case self::ITEM_BUCKET:
                return 'Bucket';
            case self::ITEM_COMPASS:
                return 'Compass';
            case self::ITEM_DAGGER:
                return 'Dagger';
            case self::ITEM_PISTOL:
                return 'Pistol';
            case self::ITEM_RUM:
                return 'Rum';
            case self::ITEM_SWORD:
                return 'Sword';
            default:
                return 'Unknown Item';
        }
    }

    /**
     * Get item ability by type_arg
     */
    public function getItemAbility(int $typeArg): string
    {
        switch ($typeArg) {
            case self::ITEM_BLANKET:
                return 'blanket'; // Lower Fire Die by 2 once per turn
            case self::ITEM_BUCKET:
                return 'bucket'; // Lower Fire Die in adjacent room once per turn
            case self::ITEM_COMPASS:
                return 'compass'; // One free Walk or Run Action per turn
            case self::ITEM_DAGGER:
                return 'dagger'; // One free Eliminate Deckhand Action per turn
            case self::ITEM_PISTOL:
                return 'pistol'; // Attack from adjacent room, no fatigue loss on fail
            case self::ITEM_RUM:
                return 'rum'; // One free Rest Action per turn
            case self::ITEM_SWORD:
                return 'sword'; // Add 1 to Strength in Battle
            default:
                return '';
        }
    }

    /**
     * Get item ability description
     */
    public function getItemAbilityDescription(int $typeArg): string
    {
        switch ($typeArg) {
            case self::ITEM_BLANKET:
                return 'May lower a Fire Die by 2 once per turn.';
            case self::ITEM_BUCKET:
                return 'May lower a Fire Die in an adjacent room once per turn.';
            case self::ITEM_COMPASS:
                return 'One free Walk or Run Action per turn.';
            case self::ITEM_DAGGER:
                return 'One free Eliminate Deckhand Action per turn.';
            case self::ITEM_PISTOL:
                return 'May attack from an adjacent room for one Action once per turn. No fatigue is lost for a failed attack.';
            case self::ITEM_RUM:
                return 'One free Rest Action per turn.';
            case self::ITEM_SWORD:
                return 'Add 1 to Strength in Battle.';
            default:
                return 'Unknown ability';
        }
    }

    /**
     * Swap item cards between two players
     */
    public function swapItemsBetweenPlayers(int $fromPlayerId, int $toPlayerId, int $itemId): ?array
    {
        // Validate the item belongs to the from player using DBManager
        $itemObject = $this->itemDB->createObjectFromDB($itemId);

        if (!$itemObject || $itemObject->getCardLocation() !== self::LOCATION_PLAYER || $itemObject->getCardLocationArg() !== $fromPlayerId) {
            return null;
        }

        // Get the target player's current item (if any)
        $targetItem = $this->getPlayerItemCard($toPlayerId);

        // Move the item to target player using DBManager
        $itemObject->setCardLocationArg($toPlayerId);
        $this->itemDB->saveObjectToDB($itemObject);

        $assignments = [
            $toPlayerId => $itemId,
            $fromPlayerId => null,
        ];

        if ($targetItem) {
            // Move target's item to from player using DBManager
            $targetItemObject = $this->itemDB->createObjectFromDB($targetItem['card_id']);
            $targetItemObject->setCardLocationArg($fromPlayerId);
            $this->itemDB->saveObjectToDB($targetItemObject);
            $assignments[$fromPlayerId] = $targetItem['card_id'];
        }

        // Notify players about the swap
        $this->game->notifyAllPlayers('itemsSwapped',
            clienttranslate('${from_player_name} and ${to_player_name} swapped items'),
            [
                'from_player_id' => $fromPlayerId,
                'to_player_id' => $toPlayerId,
                'from_player_name' => $this->game->getPlayerNameById($fromPlayerId),
                'to_player_name' => $this->game->getPlayerNameById($toPlayerId),
                'swapped_item_id' => $itemId,
                'target_item_id' => $targetItem ? $targetItem['card_id'] : null
            ]
        );

        return $assignments;
    }

    /**
     * Swap item between player and table
     */
    public function swapItemWithTable(int $playerId, int $tableItemId): ?array
    {
        // Validate the table item using DBManager
        $tableItemObject = $this->itemDB->createObjectFromDB($tableItemId);

        if (!$tableItemObject || $tableItemObject->getCardLocation() !== self::LOCATION_TABLE) {
            return null;
        }

        // Get player's current item
        $playerItem = $this->getPlayerItemCard($playerId);
        $assignments = [$playerId => $tableItemId];

        if (!$playerItem) {
            // Player has no item, just take the table item
            $tableItemObject->setCardLocation(self::LOCATION_PLAYER);
            $tableItemObject->setCardLocationArg($playerId);
            $this->itemDB->saveObjectToDB($tableItemObject);
        } else {
            // Perform the swap using DBManager
            $playerItemObject = $this->itemDB->createObjectFromDB($playerItem['card_id']);

            $tableItemObject->setCardLocation(self::LOCATION_PLAYER);
            $tableItemObject->setCardLocationArg($playerId);
            $this->itemDB->saveObjectToDB($tableItemObject);

            $playerItemObject->setCardLocation(self::LOCATION_TABLE);
            $playerItemObject->setCardLocationArg(0);
            $this->itemDB->saveObjectToDB($playerItemObject);
        }

        // Notify about the swap
        $this->game->notifyAllPlayers('itemSwappedWithTable',
            clienttranslate('${player_name} swapped an item with the table'),
            [
                'player_id' => $playerId,
                'player_name' => $this->game->getPlayerNameById($playerId),
                'table_item_id' => $tableItemId,
                'player_item_id' => $playerItem ? $playerItem['card_id'] : null
            ]
        );

        return $assignments;
    }

    /**
     * Check if player has specific item ability
     */
    public function hasItemAbility(int $playerId, string $ability): bool
    {
        $itemCard = $this->getPlayerItemCard($playerId);
        
        if (!$itemCard) {
            return false;
        }
        
        $itemAbility = $this->getItemAbility($itemCard['card_type_arg']);
        return $itemAbility === $ability;
    }

    /**
     * Get player's item ability
     */
    public function getPlayerItemAbility(int $playerId): string
    {
        $itemCard = $this->getPlayerItemCard($playerId);
        
        if (!$itemCard) {
            return '';
        }
        
        return $this->getItemAbility($itemCard['card_type_arg']);
    }

    /**
     * Destroy an item (used by explosion effects)
     */
    public function destroyItem(int $itemId): ?int
    {
        $itemObject = $this->itemDB->createObjectFromDB($itemId);
        if (!$itemObject) {
            return null;
        }

        $originalOwnerId = null;
        if ($itemObject->getCardLocation() === self::LOCATION_PLAYER) {
            $originalOwnerId = $itemObject->getCardLocationArg();
        }

        // Move item to discard pile using DBManager
        $itemObject->setCardLocation(self::LOCATION_DISCARD);
        $itemObject->setCardLocationArg(0);
        $this->itemDB->saveObjectToDB($itemObject);

        // Notify about item destruction
        $this->game->notifyAllPlayers('itemDestroyed',
            clienttranslate('An item has been destroyed!'),
            [
                'item_id' => $itemId
            ]
        );

        return $originalOwnerId;
    }

    /**
     * Get all player items data including item info
     */
    public function getPlayerItemData(int $playerId): array
    {
        $itemCard = $this->getPlayerItemCard($playerId);
        
        if (!$itemCard) {
            return [
                'item' => null,
                'item_name' => '',
                'item_ability' => '',
                'item_ability_description' => ''
            ];
        }
        
        return [
            'item' => $itemCard,
            'item_name' => $this->getItemName($itemCard['card_type_arg']),
            'item_ability' => $this->getItemAbility($itemCard['card_type_arg']),
            'item_ability_description' => $this->getItemAbilityDescription($itemCard['card_type_arg'])
        ];
    }

    /**
     * Get all items for game data display
     */
    public function getAllItemsData(): array
    {
        $allItems = $this->itemDB->getAllObjects();
        
        $playerItems = [];
        $discardedItems = [];
        
        foreach ($allItems as $item) {
            if ($item->getCardLocation() === self::LOCATION_PLAYER) {
                $playerItems[] = $item->toArray();
            } elseif ($item->getCardLocation() === self::LOCATION_DISCARD) {
                $discardedItems[] = $item->toArray();
            }
        }
        
        return [
            'player_items' => $playerItems,
            'table_items' => $this->getAvailableItemsOnTable(),
            'discarded_items' => $discardedItems
        ];
    }

    /**
     * Apply item effects (called when using items)
     */
    public function useItemAbility(int $playerId, string $ability, array $params = []): bool
    {
        if (!$this->hasItemAbility($playerId, $ability)) {
            return false;
        }
        
        switch ($ability) {
            case 'sword':
                // Applied automatically during battle calculations (+1 Strength)
                $this->game->notifyAllPlayers('itemUsed', 
                    clienttranslate('${player_name} uses Sword in battle'), 
                    [
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
                        'ability' => $ability
                    ]
                );
                return true;
                
            case 'rum':
                // One free Rest Action per turn - handled during action phase
                $this->game->notifyAllPlayers('itemUsed', 
                    clienttranslate('${player_name} uses Rum for a free Rest action'), 
                    [
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
                        'ability' => $ability
                    ]
                );
                return true;
                
            case 'compass':
                // One free Walk or Run Action per turn - handled during action phase
                $this->game->notifyAllPlayers('itemUsed', 
                    clienttranslate('${player_name} uses Compass for free movement'), 
                    [
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
                        'ability' => $ability
                    ]
                );
                return true;
                
            case 'dagger':
                // One free Eliminate Deckhand Action per turn - handled during action phase
                $this->game->notifyAllPlayers('itemUsed', 
                    clienttranslate('${player_name} uses Dagger to eliminate a deckhand'), 
                    [
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
                        'ability' => $ability
                    ]
                );
                return true;
                
            // Other abilities are applied contextually during gameplay
            case 'blanket':  // Lower Fire Die by 2 once per turn
            case 'bucket':   // Lower Fire Die in adjacent room once per turn  
            case 'pistol':   // Attack from adjacent room, no fatigue loss on fail
                $this->game->notifyAllPlayers('itemUsed', 
                    clienttranslate('${player_name} uses ${item_name}'), 
                    [
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
                        'ability' => $ability,
                        'item_name' => $this->getItemName($this->getPlayerItemCard($playerId)['card_type_arg'] ?? 0)
                    ]
                );
                return true;
                
            default:
                return false;
        }
    }

    /**
     * Get item counts by location
     */
    public function getItemCounts(): array
    {
        $allItems = $this->itemDB->getAllObjects();
        $counts = ['players' => 0, 'table' => 0, 'discard' => 0];
        
        foreach ($allItems as $item) {
            switch ($item->getCardLocation()) {
                case self::LOCATION_PLAYER:
                    $counts['players']++;
                    break;
                case self::LOCATION_TABLE:
                    $counts['table']++;
                    break;
                case self::LOCATION_DISCARD:
                    $counts['discard']++;
                    break;
            }
        }
        
        return $counts;
    }

    /**
     * Reset items for game restart (debugging/admin)
     */
    public function resetItems(): void
    {
        // Move all items back to table using DBManager
        $allItems = $this->itemDB->getAllObjects();
        foreach ($allItems as $item) {
            $item->setCardLocation(self::LOCATION_TABLE);
            $item->setCardLocationArg(0);
            $this->itemDB->saveObjectToDB($item);
        }
        
        // Clear player references
        $players = $this->playerDBManager->getAllObjects();
        foreach ($players as $player) {
            $player->itemCardId = null;
            $this->playerDBManager->saveObjectToDB($player);
        }
        
        $this->game->notifyAllPlayers('itemsReset', 
            clienttranslate('All items have been reset to the table'), []);
    }

    /**
     * Get items that can be swapped (for UI)
     */
    public function getSwappableItems(int $playerId): array
    {
        $playerItem = $this->getPlayerItemCard($playerId);
        $tableItems = $this->getAvailableItemsOnTable();
        
        // Get other players' items using DBManager
        $allItems = $this->itemDB->getAllObjects();
        $otherPlayerItems = [];
        
        foreach ($allItems as $item) {
            if ($item->getCardLocation() === self::LOCATION_PLAYER && $item->getCardLocationArg() !== $playerId) {
                $itemArray = $item->toArray();
                // Get player name for this item
                $playerName = $this->game->getPlayerNameById($item->getCardLocationArg());
                $itemArray['player_name'] = $playerName;
                $otherPlayerItems[] = $itemArray;
            }
        }
        
        return [
            'player_item' => $playerItem,
            'table_items' => $tableItems,
            'other_player_items' => $otherPlayerItems
        ];
    }
}
