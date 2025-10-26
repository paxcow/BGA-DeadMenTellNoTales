<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;

/**
 * Manages item cards, character cards, and their abilities for Dead Men Pax
 */
class ItemManager
{
    private Table $game;
    
    // Card types
    public const CARD_TYPE_CHARACTER = 'character';
    public const CARD_TYPE_ITEM = 'item';
    public const CARD_TYPE_SKELIT = 'skelit';
    public const CARD_TYPE_DINGHY = 'dinghy';
    
    // Card locations
    public const LOCATION_DECK = 'deck';
    public const LOCATION_PLAYER = 'player';
    public const LOCATION_TABLE = 'table';
    public const LOCATION_DISCARD = 'discard';

    public function __construct(Table $game)
    {
        $this->game = $game;
    }

    /**
     * Get player's character card
     */
    public function getPlayerCharacterCard(int $playerId): ?array
    {
        $card = $this->game->getObjectFromDB(
            "SELECT * FROM card WHERE card_type = '" . self::CARD_TYPE_CHARACTER . "' AND card_location = '" . self::LOCATION_PLAYER . "' AND card_location_arg = $playerId"
        );
        
        return $card ?: null;
    }

    /**
     * Get player's item card
     */
    public function getPlayerItemCard(int $playerId): ?array
    {
        $card = $this->game->getObjectFromDB(
            "SELECT * FROM card WHERE card_type = '" . self::CARD_TYPE_ITEM . "' AND card_location = '" . self::LOCATION_PLAYER . "' AND card_location_arg = $playerId"
        );
        
        return $card ?: null;
    }

    /**
     * Get all items available on the table (face up)
     */
    public function getAvailableItemsOnTable(): array
    {
        return $this->game->getCollectionFromDb(
            "SELECT * FROM card WHERE card_type = '" . self::CARD_TYPE_ITEM . "' AND card_location = '" . self::LOCATION_TABLE . "' ORDER BY card_id"
        );
    }

    /**
     * Assign a character card to a player during setup
     */
    public function assignCharacterCardToPlayer(int $playerId, int $cardId): void
    {
        $this->game->DbQuery("UPDATE card SET card_location = '" . self::LOCATION_PLAYER . "', card_location_arg = $playerId WHERE card_id = $cardId AND card_type = '" . self::CARD_TYPE_CHARACTER . "'");
        
        // Update player table with character card reference
        $this->game->DbQuery("UPDATE player SET player_character_card_id = $cardId WHERE player_id = $playerId");
    }

    /**
     * Assign an item card to a player during setup
     */
    public function assignItemCardToPlayer(int $playerId, int $cardId): void
    {
        $this->game->DbQuery("UPDATE card SET card_location = '" . self::LOCATION_PLAYER . "', card_location_arg = $playerId WHERE card_id = $cardId AND card_type = '" . self::CARD_TYPE_ITEM . "'");
        
        // Update player table with item card reference
        $this->game->DbQuery("UPDATE player SET player_item_card_id = $cardId WHERE player_id = $playerId");
    }

    /**
     * Swap item cards between two players or between player and table
     */
    public function swapItems(int $fromPlayerId, int $toPlayerId, int $itemId): bool
    {
        // Validate the item belongs to the from player
        $itemCard = $this->game->getObjectFromDB(
            "SELECT * FROM card WHERE card_id = $itemId AND card_type = '" . self::CARD_TYPE_ITEM . "' AND card_location = '" . self::LOCATION_PLAYER . "' AND card_location_arg = $fromPlayerId"
        );
        
        if (!$itemCard) {
            return false;
        }
        
        // Get the target player's current item (if any)
        $targetItem = $this->getPlayerItemCard($toPlayerId);
        
        // Swap the items
        $this->game->DbQuery("UPDATE card SET card_location_arg = $toPlayerId WHERE card_id = $itemId");
        $this->game->DbQuery("UPDATE player SET player_item_card_id = $itemId WHERE player_id = $toPlayerId");
        
        if ($targetItem) {
            // Move target's item to from player
            $this->game->DbQuery("UPDATE card SET card_location_arg = $fromPlayerId WHERE card_id = {$targetItem['card_id']}");
            $this->game->DbQuery("UPDATE player SET player_item_card_id = {$targetItem['card_id']} WHERE player_id = $fromPlayerId");
        } else {
            // Target player had no item, so from player now has no item
            $this->game->DbQuery("UPDATE player SET player_item_card_id = NULL WHERE player_id = $fromPlayerId");
        }
        
        return true;
    }

    /**
     * Swap item between player and table
     */
    public function swapItemWithTable(int $playerId, int $tableItemId): bool
    {
        // Validate the table item
        $tableItem = $this->game->getObjectFromDB(
            "SELECT * FROM card WHERE card_id = $tableItemId AND card_type = '" . self::CARD_TYPE_ITEM . "' AND card_location = '" . self::LOCATION_TABLE . "'"
        );
        
        if (!tableItem) {
            return false;
        }
        
        // Get player's current item
        $playerItem = $this->getPlayerItemCard($playerId);
        
        if (!$playerItem) {
            return false; // Player has no item to swap
        }
        
        // Perform the swap
        $this->game->DbQuery("UPDATE card SET card_location = '" . self::LOCATION_PLAYER . "', card_location_arg = $playerId WHERE card_id = $tableItemId");
        $this->game->DbQuery("UPDATE card SET card_location = '" . self::LOCATION_TABLE . "', card_location_arg = 0 WHERE card_id = {$playerItem['card_id']}");
        $this->game->DbQuery("UPDATE player SET player_item_card_id = $tableItemId WHERE player_id = $playerId");
        
        return true;
    }

    /**
     * Get character ability for a player
     */
    public function getCharacterAbility(int $playerId): string
    {
        $characterCard = $this->getPlayerCharacterCard($playerId);
        
        if (!$characterCard) {
            return '';
        }
        
        // This would be expanded based on actual character definitions
        return $this->getAbilityByCardTypeArg(self::CARD_TYPE_CHARACTER, $characterCard['card_type_arg']);
    }

    /**
     * Get item ability for a player
     */
    public function getItemAbility(int $playerId): string
    {
        $itemCard = $this->getPlayerItemCard($playerId);
        
        if (!$itemCard) {
            return '';
        }
        
        // This would be expanded based on actual item definitions
        return $this->getAbilityByCardTypeArg(self::CARD_TYPE_ITEM, $itemCard['card_type_arg']);
    }

    /**
     * Check if player can use a specific ability
     */
    public function canUseAbility(int $playerId, string $ability): bool
    {
        $characterAbility = $this->getCharacterAbility($playerId);
        $itemAbility = $this->getItemAbility($playerId);
        
        return $characterAbility === $ability || $itemAbility === $ability;
    }

    /**
     * Get ability description by card type and type arg
     */
    private function getAbilityByCardTypeArg(string $cardType, int $typeArg): string
    {
        // This would be populated with actual card definitions
        // For now, return placeholder abilities
        
        if ($cardType === self::CARD_TYPE_CHARACTER) {
            switch ($typeArg) {
                case 1: return 'extra_action'; // Lydia Lamore gets 6 actions
                case 2: return 'fire_resistance'; // Reduced fire damage
                case 3: return 'battle_bonus'; // Battle strength bonus
                case 4: return 'movement_bonus'; // Can move through fire
                default: return '';
            }
        }
        
        if ($cardType === self::CARD_TYPE_ITEM) {
            switch ($typeArg) {
                case 1: return 'cutlass'; // +2 battle strength
                case 2: return 'grog'; // Reduce fatigue
                case 3: return 'treasure_map'; // Extra treasure
                case 4: return 'rope'; // Move between rooms without doors
                default: return '';
            }
        }
        
        return '';
    }

    /**
     * Apply character ability effects during game setup
     */
    public function applyCharacterSetupEffects(int $playerId): void
    {
        $ability = $this->getCharacterAbility($playerId);
        
        switch ($ability) {
            case 'extra_action':
                // Lydia Lamore gets 6 actions instead of 5
                $this->game->DbQuery("UPDATE player SET player_max_actions = 6, player_actions_remaining = 6 WHERE player_id = $playerId");
                break;
                
            case 'battle_bonus':
                // Character starts with +1 battle strength
                $this->game->DbQuery("UPDATE player SET player_battle_strength = player_battle_strength + 1 WHERE player_id = $playerId");
                break;
                
            // Other character setup effects would go here
        }
    }

    /**
     * Get items in specific room positions (for explosion effects)
     */
    public function getItemsInPositions(array $positions): array
    {
        // Items carried by players don't get destroyed directly by room explosions
        // Only items placed as tokens in rooms would be destroyed
        // This method would be for tracking item tokens, not player-held items
        
        // For now, return empty as items are held by players, not placed in rooms
        // This could be expanded if the game has item tokens that can be dropped
        return [];
    }

    /**
     * Destroy an item (used by explosion effects)
     */
    public function destroyItem(int $itemId): void
    {
        // Move item to discard pile
        $this->game->DbQuery("UPDATE card SET card_location = '" . self::LOCATION_DISCARD . "', card_location_arg = 0 WHERE card_id = $itemId");
        
        // Remove reference from player if they had this item
        $this->game->DbQuery("UPDATE player SET player_item_card_id = NULL WHERE player_item_card_id = $itemId");
    }

    /**
     * Get all player items for client display
     */
    public function getPlayerItems(int $playerId): array
    {
        $characterCard = $this->getPlayerCharacterCard($playerId);
        $itemCard = $this->getPlayerItemCard($playerId);
        
        return [
            'character' => $characterCard,
            'item' => $itemCard,
            'character_ability' => $characterCard ? $this->getAbilityByCardTypeArg(self::CARD_TYPE_CHARACTER, $characterCard['card_type_arg']) : '',
            'item_ability' => $itemCard ? $this->getAbilityByCardTypeArg(self::CARD_TYPE_ITEM, $itemCard['card_type_arg']) : ''
        ];
    }

    /**
     * Setup item cards for game start
     */
    public function setupItemCards(): void
    {
        // Place some item cards face up on the table
        // This would be called during game setup
        $availableItems = $this->game->getCollectionFromDb(
            "SELECT card_id FROM card WHERE card_type = '" . self::CARD_TYPE_ITEM . "' AND card_location = '" . self::LOCATION_DECK . "' ORDER BY RAND() LIMIT 3"
        );
        
        foreach ($availableItems as $cardId => $card) {
            $this->game->DbQuery("UPDATE card SET card_location = '" . self::LOCATION_TABLE . "' WHERE card_id = $cardId");
        }
    }

    /**
     * Draw random character card from deck
     */
    public function drawRandomCharacterCard(): ?array
    {
        $card = $this->game->getObjectFromDB(
            "SELECT card_id FROM card WHERE card_type = '" . self::CARD_TYPE_CHARACTER . "' AND card_location = '" . self::LOCATION_DECK . "' ORDER BY RAND() LIMIT 1"
        );
        
        return $card ?: null;
    }

    /**
     * Draw random item card from deck
     */
    public function drawRandomItemCard(): ?array
    {
        $card = $this->game->getObjectFromDB(
            "SELECT card_id FROM card WHERE card_type = '" . self::CARD_TYPE_ITEM . "' AND card_location = '" . self::LOCATION_DECK . "' ORDER BY RAND() LIMIT 1"
        );
        
        return $card ?: null;
    }

    /**
     * Check if player has specific item ability
     */
    public function hasItemAbility(int $playerId, string $ability): bool
    {
        $itemAbility = $this->getItemAbility($playerId);
        return $itemAbility === $ability;
    }

    /**
     * Check if player has specific character ability
     */
    public function hasCharacterAbility(int $playerId, string $ability): bool
    {
        $characterAbility = $this->getCharacterAbility($playerId);
        return $characterAbility === $ability;
    }
}
