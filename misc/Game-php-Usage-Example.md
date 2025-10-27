# Game.php Usage Example: ItemManager vs ItemManagerDeck

This document shows how to update your `Game.php` file to use the new `ItemManagerDeck` implementation.

## Current Game.php Constructor (Using ItemManager)

```php
<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;

class Game extends Table
{
    private ItemManager $itemManager;
    private BoardManager $boardManager;
    private TokenManager $tokenManager;
    // ... other managers

    public function __construct()
    {
        parent::__construct();
        
        // Initialize managers with current implementation
        $this->itemManager = new ItemManager($this);
        $this->boardManager = new BoardManager($this);
        $this->tokenManager = new TokenManager($this);
    }
    
    protected function setupNewGame($players, $options = []): void
    {
        // Setup game components
        $this->boardManager->setupBoard();
        
        // Cards would be set up manually
        // No centralized card setup in original
        
        foreach ($players as $playerId => $player) {
            // Manual card dealing logic
            $characterCard = $this->itemManager->drawRandomCharacterCard();
            if ($characterCard) {
                $this->itemManager->assignCharacterCardToPlayer($playerId, $characterCard['card_id']);
            }
            
            $itemCard = $this->itemManager->drawRandomItemCard();
            if ($itemCard) {
                $this->itemManager->assignItemCardToPlayer($playerId, $itemCard['card_id']);
            }
            
            $this->itemManager->applyCharacterSetupEffects($playerId);
        }
    }
}
```

## Updated Game.php Constructor (Using ItemManagerDeck)

```php
<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;

class Game extends Table
{
    private ItemManagerDeck $itemManager;  // Changed type
    private BoardManager $boardManager;
    private TokenManager $tokenManager;
    // ... other managers

    public function __construct()
    {
        parent::__construct();
        
        // Initialize managers with DECK implementation
        $this->itemManager = new ItemManagerDeck($this);  // Changed class
        $this->boardManager = new BoardManager($this);
        $this->tokenManager = new TokenManager($this);
    }
    
    protected function setupNewGame($players, $options = []): void
    {
        // Setup game components
        $this->boardManager->setupBoard();
        
        // Enhanced: Centralized card setup with DECK
        $this->itemManager->setupCards();           // Creates all cards and shuffles
        $this->itemManager->dealStartingCards();    // Automatically deals to all players
        
        // That's it! The DECK implementation handles:
        // - Creating all character and item cards
        // - Shuffling the deck
        // - Dealing cards to each player
        // - Applying character setup effects
        // - Notifying players of their cards
        // - Setting up table items
    }
}
```

## Method Usage Comparisons

### Getting Player Cards

**Original (returns CardModel):**
```php
public function getPlayerHand($playerId): array
{
    $characterCard = $this->itemManager->getPlayerCharacterCard($playerId);
    $itemCard = $this->itemManager->getPlayerItemCard($playerId);
    
    return [
        'character' => $characterCard ? [
            'id' => $characterCard->getCardId(),
            'type' => $characterCard->getCardType(),
            'type_arg' => $characterCard->getCardTypeArg()
        ] : null,
        'item' => $itemCard ? [
            'id' => $itemCard->getCardId(), 
            'type' => $itemCard->getCardType(),
            'type_arg' => $itemCard->getCardTypeArg()
        ] : null
    ];
}
```

**New (returns arrays):**
```php
public function getPlayerHand($playerId): array
{
    $characterCard = $this->itemManager->getPlayerCharacterCard($playerId);
    $itemCard = $this->itemManager->getPlayerItemCard($playerId);
    
    return [
        'character' => $characterCard,  // Already an array
        'item' => $itemCard            // Already an array
    ];
}

// Or even simpler:
public function getPlayerHand($playerId): array
{
    return $this->itemManager->getPlayerItems($playerId);  // Returns both cards + abilities
}
```

### Card Swapping Actions

**Original:**
```php
public function actionSwapItems($fromPlayerId, $toPlayerId, $itemId): void
{
    // Validate action
    $this->checkAction('swapItems');
    $currentPlayer = $this->getCurrentPlayerId();
    
    if ($currentPlayer != $fromPlayerId) {
        throw new \Exception("Not your turn");
    }
    
    // Perform swap
    $success = $this->itemManager->swapItems($fromPlayerId, $toPlayerId, $itemId);
    
    if (!$success) {
        throw new \Exception("Invalid swap");
    }
    
    // Notify players
    $this->notifyAllPlayers('itemSwapped', 
        clienttranslate('${player_name} swapped items'), 
        [
            'player_id' => $fromPlayerId,
            'player_name' => $this->getPlayerName($fromPlayerId),
            'from_player' => $fromPlayerId,
            'to_player' => $toPlayerId,
            'item_id' => $itemId
        ]
    );
    
    $this->gamestate->nextState('nextPlayer');
}
```

**New (same logic, enhanced with deck info):**
```php
public function actionSwapItems($fromPlayerId, $toPlayerId, $itemId): void
{
    // Validate action
    $this->checkAction('swapItems');
    $currentPlayer = $this->getCurrentPlayerId();
    
    if ($currentPlayer != $fromPlayerId) {
        throw new \Exception("Not your turn");
    }
    
    // Perform swap
    $success = $this->itemManager->swapItems($fromPlayerId, $toPlayerId, $itemId);
    
    if (!$success) {
        throw new \Exception("Invalid swap");
    }
    
    // Enhanced: Get deck status for additional info
    $deckCount = $this->itemManager->getDeckCount();
    $discardCount = $this->itemManager->getDiscardCount();
    
    // Notify players with enhanced info
    $this->notifyAllPlayers('itemSwapped', 
        clienttranslate('${player_name} swapped items'), 
        [
            'player_id' => $fromPlayerId,
            'player_name' => $this->getPlayerName($fromPlayerId),
            'from_player' => $fromPlayerId,
            'to_player' => $toPlayerId,
            'item_id' => $itemId,
            'deck_count' => $deckCount,      // Enhanced info
            'discard_count' => $discardCount  // Enhanced info
        ]
    );
    
    $this->gamestate->nextState('nextPlayer');
}
```

### Enhanced Features Available

**New Deck Management Actions:**
```php
public function actionShuffleDeck(): void
{
    $this->checkAction('shuffleDeck');
    
    // Manual shuffle if needed
    $this->itemManager->shuffleDeck();
    
    $this->gamestate->nextState('nextPlayer');
}

public function getGameProgression(): int
{
    // Enhanced progression calculation using deck info
    $totalCards = $this->itemManager->getDeckCount() + 
                 $this->itemManager->getDiscardCount() + 
                 $this->itemManager->countCardInLocation('player') +
                 $this->itemManager->countCardInLocation('table');
    
    $cardsInPlay = $totalCards - $this->itemManager->getDeckCount();
    
    return min(100, ($cardsInPlay / $totalCards) * 100);
}

public function getAllDatas(): array
{
    $result = parent::getAllDatas();
    
    // Enhanced game data with deck information
    $playerId = $this->getCurrentPlayerId();
    
    $result['player_cards'] = $this->itemManager->getPlayerItems($playerId);
    $result['table_items'] = $this->itemManager->getAvailableItemsOnTable();
    $result['deck_count'] = $this->itemManager->getDeckCount();
    $result['discard_count'] = $this->itemManager->getDiscardCount();
    
    // Enhanced: Card type distribution
    $result['card_counts'] = [
        'deck' => $this->itemManager->getCardCountsByType('deck'),
        'table' => $this->itemManager->getCardCountsByType('table'),
        'player' => $this->itemManager->getCardCountsByType('player')
    ];
    
    return $result;
}
```

### Error Handling with Auto-reshuffle

**Enhanced Error Handling:**
```php
public function actionDrawCard($playerId): void
{
    $this->checkAction('drawCard');
    
    try {
        // The DECK component automatically handles empty deck scenarios
        $card = $this->itemManager->cards->pickCard('deck', $playerId);
        
        if (!$card) {
            throw new \Exception("No more cards available");
        }
        
        $this->notifyPlayer($playerId, 'cardDrawn', '', ['card' => $card]);
        
    } catch (\Exception $e) {
        // Auto-reshuffle happened, notification already sent via callback
        $this->notifyPlayer($playerId, 'actionError', $e->getMessage(), []);
        return;
    }
    
    $this->gamestate->nextState('nextPlayer');
}
```

## Migration Checklist for Game.php

### 1. Update Constructor
- [ ] Change `new ItemManager($this)` to `new ItemManagerDeck($this)`
- [ ] Update type hint from `ItemManager` to `ItemManagerDeck`

### 2. Update setupNewGame()
- [ ] Add `$this->itemManager->setupCards();`
- [ ] Replace manual card dealing with `$this->itemManager->dealStartingCards();`
- [ ] Remove manual card creation loops

### 3. Update Method Calls
- [ ] Change `$card->getProperty()` to `$card['property']` where cards are used
- [ ] Update null checks for array-based cards
- [ ] Test all card-related actions

### 4. Add Enhanced Features (Optional)
- [ ] Add deck count to game data
- [ ] Add discard pile management
- [ ] Implement deck shuffle actions
- [ ] Add auto-reshuffle notifications

### 5. Update Notifications
- [ ] Include deck status in relevant notifications
- [ ] Add reshuffle event handling
- [ ] Update client-side card display logic

## Benefits After Migration

1. **Simplified Setup**: Single method call vs manual loops
2. **Auto-reshuffle**: Handles empty deck scenarios automatically
3. **Better Performance**: Optimized database operations
4. **Enhanced Data**: More game state information available
5. **Robust Shuffling**: Proper card ordering and randomization
6. **Battle-tested**: Using BGA's proven card management system

## Common Migration Issues

1. **Type Changes**: `CardModel` → `array`
2. **Property Access**: `->getProperty()` → `['property']`
3. **Null Handling**: Arrays handle differently than objects
4. **Auto-formatting**: IDE may reformat code, use final_file_content as reference

The migration provides significant benefits with minimal changes to your existing game logic.
