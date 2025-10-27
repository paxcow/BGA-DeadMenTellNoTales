<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\DB\DBManager;
use Bga\Games\DeadMenPax\DB\Models\ItemCardModel;

/**
 * Manages Item cards with custom swapping logic
 * 7 item cards total - one per player initially, remainder on table for swapping
 * Uses separate item_card table, no DECK component needed
 */
class ItemManager
{
    private Table $game;
    private DBManager $itemDB;
    
    // Card locations
    public const LOCATION_PLAYER = 'player';
    public const LOCATION_TABLE = 'table';
    public const LOCATION_DISCARD = 'discard';
    
    // Item types (different item abilities)
    public const ITEM_CUTLASS = 1;        // +2 battle strength
    public const ITEM_GROG = 2;           // Reduce fatigue
    public const ITEM_TREASURE_MAP = 3;   // Extra treasure
    public const ITEM_ROPE = 4;           // Move between rooms without doors
    public const ITEM_LANTERN = 5;        // See hidden passages
    public const ITEM_POWDER_KEG = 6;     // Explosive damage
    public const ITEM_FIRST_AID = 7;      // Heal wounds

    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->itemDB = new DBManager('item_card', ItemCardModel::class, $game);
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
    public function dealStartingItemCards(): void
    {
        $players = $this->game->loadPlayersBasicInfos();
        $playerCount = count($players);
        
        // Get random item cards for each player
        $availableItems = $this->game->getCollectionFromDb(
            "SELECT card_id FROM item_card WHERE card_location = '" . self::LOCATION_TABLE . "' ORDER BY RAND() LIMIT $playerCount"
        );
        
        $itemIndex = 0;
        foreach ($players as $playerId => $player) {
            $itemCards = array_keys($availableItems);
            
            if (isset($itemCards[$itemIndex])) {
                $cardId = $itemCards[$itemIndex];
                
                // Move item to player
                $this->game->DbQuery("UPDATE item_card SET card_location = '" . self::LOCATION_PLAYER . "', card_location_arg = $playerId WHERE card_id = $cardId");
                
                // Update player table with item card reference
                $this->game->DbQuery("UPDATE player SET player_item_card_id = $cardId WHERE player_id = $playerId");
                
                // Notify player about their item
                $itemCard = $this->getItemCard($cardId);
                if ($itemCard) {
                    $this->game->notifyPlayer($playerId, 'newItemCard', '', [
                        'card' => $itemCard,
                        'item_name' => $this->getItemName($itemCard['card_type_arg']),
                        'item_ability' => $this->getItemAbility($itemCard['card_type_arg'])
                    ]);
                }
                
                $itemIndex++;
            }
        }
    }

    /**
     * Get player's current item card
     */
    public function getPlayerItemCard(int $playerId): ?array
    {
        return $this->game->getObjectFromDB(
            "SELECT * FROM item_card WHERE card_location = '" . self::LOCATION_PLAYER . "' AND card_location_arg = $playerId"
        );
    }

    /**
     * Get all items available on the table (face up)
     */
    public function getAvailableItemsOnTable(): array
    {
        return $this->game->getCollectionFromDb(
            "SELECT * FROM item_card WHERE card_location = '" . self::LOCATION_TABLE . "'"
        );
    }

    /**
     * Get specific item card by ID
     */
    public function getItemCard(int $cardId): ?array
    {
        return $this->game->getObjectFromDB(
            "SELECT * FROM item_card WHERE card_id = $cardId"
        );
    }

    /**
     * Get item name by type_arg
     */
    public function getItemName(int $typeArg): string
    {
        switch ($typeArg) {
            case self::ITEM_CUTLASS:
                return 'Cutlass';
            case self::ITEM_GROG:
                return 'Grog';
            case self::ITEM_TREASURE_MAP:
                return 'Treasure Map';
            case self::ITEM_ROPE:
                return 'Rope';
            case self::ITEM_LANTERN:
                return 'Lantern';
            case self::ITEM_POWDER_KEG:
                return 'Powder Keg';
            case self::ITEM_FIRST_AID:
                return 'First Aid Kit';
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
            case self::ITEM_CUTLASS:
                return 'cutlass'; // +2 battle strength
            case self::ITEM_GROG:
                return 'grog'; // Reduce fatigue
            case self::ITEM_TREASURE_MAP:
                return 'treasure_map'; // Extra treasure
            case self::ITEM_ROPE:
                return 'rope'; // Move between rooms without doors
            case self::ITEM_LANTERN:
                return 'lantern'; // See hidden passages
            case self::ITEM_POWDER_KEG:
                return 'powder_keg'; // Explosive damage
            case self::ITEM_FIRST_AID:
                return 'first_aid'; // Heal wounds
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
            case self::ITEM_CUTLASS:
                return '+2 battle strength in combat';
            case self::ITEM_GROG:
                return 'Reduces fatigue by 1';
            case self::ITEM_TREASURE_MAP:
                return 'Reveals additional treasure locations';
            case self::ITEM_ROPE:
                return 'Can move between rooms without doors';
            case self::ITEM_LANTERN:
                return 'Reveals hidden passages and secrets';
            case self::ITEM_POWDER_KEG:
                return 'Causes explosive damage in combat';
            case self::ITEM_FIRST_AID:
                return 'Heals wounds and reduces fatigue';
            default:
                return 'Unknown ability';
        }
    }

    /**
     * Swap item cards between two players
     */
    public function swapItemsBetweenPlayers(int $fromPlayerId, int $toPlayerId, int $itemId): bool
    {
        // Validate the item belongs to the from player
        $itemCard = $this->game->getObjectFromDB(
            "SELECT * FROM item_card WHERE card_id = $itemId AND card_location = '" . self::LOCATION_PLAYER . "' AND card_location_arg = $fromPlayerId"
        );
        
        if (!$itemCard) {
            return false;
        }
        
        // Get the target player's current item (if any)
        $targetItem = $this->getPlayerItemCard($toPlayerId);
        
        // Move the item to target player
        $this->game->DbQuery("UPDATE item_card SET card_location_arg = $toPlayerId WHERE card_id = $itemId");
        $this->game->DbQuery("UPDATE player SET player_item_card_id = $itemId WHERE player_id = $toPlayerId");
        
        if ($targetItem) {
            // Move target's item to from player
            $this->game->DbQuery("UPDATE item_card SET card_location_arg = $fromPlayerId WHERE card_id = {$targetItem['card_id']}");
            $this->game->DbQuery("UPDATE player SET player_item_card_id = {$targetItem['card_id']} WHERE player_id = $fromPlayerId");
        } else {
            // Target player had no item, so from player now has no item
            $this->game->DbQuery("UPDATE player SET player_item_card_id = NULL WHERE player_id = $fromPlayerId");
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
        
        return true;
    }

    /**
     * Swap item between player and table
     */
    public function swapItemWithTable(int $playerId, int $tableItemId): bool
    {
        // Validate the table item
        $tableItem = $this->game->getObjectFromDB(
            "SELECT * FROM item_card WHERE card_id = $tableItemId AND card_location = '" . self::LOCATION_TABLE . "'"
        );
        
        if (!$tableItem) {
            return false;
        }
        
        // Get player's current item
        $playerItem = $this->getPlayerItemCard($playerId);
        
        if (!$playerItem) {
            // Player has no item, just take the table item
            $this->game->DbQuery("UPDATE item_card SET card_location = '" . self::LOCATION_PLAYER . "', card_location_arg = $playerId WHERE card_id = $tableItemId");
            $this->game->DbQuery("UPDATE player SET player_item_card_id = $tableItemId WHERE player_id = $playerId");
        } else {
            // Perform the swap
            $this->game->DbQuery("UPDATE item_card SET card_location = '" . self::LOCATION_PLAYER . "', card_location_arg = $playerId WHERE card_id = $tableItemId");
            $this->game->DbQuery("UPDATE item_card SET card_location = '" . self::LOCATION_TABLE . "', card_location_arg = 0 WHERE card_id = {$playerItem['card_id']}");
            $this->game->DbQuery("UPDATE player SET player_item_card_id = $tableItemId WHERE player_id = $playerId");
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
        
        return true;
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
    public function destroyItem(int $itemId): void
    {
        // Move item to discard pile
        $this->game->DbQuery("UPDATE item_card SET card_location = '" . self::LOCATION_DISCARD . "', card_location_arg = 0 WHERE card_id = $itemId");
        
        // Remove reference from player if they had this item
        $this->game->DbQuery("UPDATE player SET player_item_card_id = NULL WHERE player_item_card_id = $itemId");
        
        // Notify about item destruction
        $this->game->notifyAllPlayers('itemDestroyed', 
            clienttranslate('An item has been destroyed!'), 
            [
                'item_id' => $itemId
            ]
        );
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
        return [
            'player_items' => $this->game->getCollectionFromDb(
                "SELECT * FROM item_card WHERE card_location = '" . self::LOCATION_PLAYER . "'"
            ),
            'table_items' => $this->getAvailableItemsOnTable(),
            'discarded_items' => $this->game->getCollectionFromDb(
                "SELECT * FROM item_card WHERE card_location = '" . self::LOCATION_DISCARD . "'"
            )
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
            case 'cutlass':
                // Applied automatically during battle calculations
                return true;
                
            case 'grog':
                // Reduce player fatigue
                $this->game->DbQuery("UPDATE player SET player_fatigue = GREATEST(0, player_fatigue - 1) WHERE player_id = $playerId");
                $this->game->notifyAllPlayers('itemUsed', 
                    clienttranslate('${player_name} uses Grog to reduce fatigue'), 
                    [
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
                        'ability' => $ability
                    ]
                );
                return true;
                
            case 'first_aid':
                // Heal wounds and reduce fatigue
                $this->game->DbQuery("UPDATE player SET player_fatigue = GREATEST(0, player_fatigue - 2) WHERE player_id = $playerId");
                $this->game->notifyAllPlayers('itemUsed', 
                    clienttranslate('${player_name} uses First Aid Kit to heal wounds'), 
                    [
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
                        'ability' => $ability
                    ]
                );
                return true;
                
            // Other abilities are applied contextually during gameplay
            case 'treasure_map':
            case 'rope':
            case 'lantern':
            case 'powder_keg':
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
        return [
            'players' => (int) $this->game->getUniqueValueFromDB("SELECT COUNT(*) FROM item_card WHERE card_location = '" . self::LOCATION_PLAYER . "'"),
            'table' => (int) $this->game->getUniqueValueFromDB("SELECT COUNT(*) FROM item_card WHERE card_location = '" . self::LOCATION_TABLE . "'"),
            'discard' => (int) $this->game->getUniqueValueFromDB("SELECT COUNT(*) FROM item_card WHERE card_location = '" . self::LOCATION_DISCARD . "'")
        ];
    }

    /**
     * Reset items for game restart (debugging/admin)
     */
    public function resetItems(): void
    {
        // Move all items back to table
        $this->game->DbQuery("UPDATE item_card SET card_location = '" . self::LOCATION_TABLE . "', card_location_arg = 0");
        
        // Clear player references
        $this->game->DbQuery("UPDATE player SET player_item_card_id = NULL");
        
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
        
        // Get other players' items (for potential trading)
        $otherPlayerItems = $this->game->getCollectionFromDb(
            "SELECT i.*, p.player_name FROM item_card i 
             JOIN player p ON i.card_location_arg = p.player_id 
             WHERE i.card_location = '" . self::LOCATION_PLAYER . "' AND i.card_location_arg != $playerId"
        );
        
        return [
            'player_item' => $playerItem,
            'table_items' => $tableItems,
            'other_player_items' => $otherPlayerItems
        ];
    }
}
