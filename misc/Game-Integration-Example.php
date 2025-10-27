<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;

/**
 * Example Game.php integration showing how to use the new specialized card managers
 * This demonstrates the refactored multi-manager architecture
 */
class GameIntegrationExample extends Table
{
    // Specialized card managers
    private SkelitDeckManager $skelitManager;
    private CharacterDeckManager $characterManager;
    private ItemManagerRefactored $itemManager;
    
    // Other game managers (unchanged)
    private BoardManager $boardManager;
    private TokenManager $tokenManager;
    private PirateManager $pirateManager;

    public function __construct()
    {
        parent::__construct();
        
        // Initialize specialized card managers with separate databases
        $this->skelitManager = new SkelitDeckManager($this);
        $this->characterManager = new CharacterDeckManager($this);
        $this->itemManager = new ItemManagerRefactored($this);
        
        // Initialize other game managers (unchanged)
        $this->boardManager = new BoardManager($this);
        $this->tokenManager = new TokenManager($this);
        $this->pirateManager = new PirateManager($this);
    }

    protected function setupNewGame($players, $options = []): void
    {
        // Setup game components with new architecture
        
        // 1. Setup board and basic game state
        $this->boardManager->setupBoard();
        
        // 2. Setup specialized card systems
        $this->skelitManager->setupSkelitCards();        // 19 skelit revenge cards
        $this->characterManager->setupCharacterCards();   // 7 character cards  
        $this->itemManager->setupItemCards();            // 7 item cards
        
        // 3. Deal starting cards to players
        $this->characterManager->dealStartingCharacterCards(); // 1 character per player
        $this->itemManager->dealStartingItemCards();           // 1 item per player
        
        // 4. Setup other game components
        $this->tokenManager->setupTokens();
        $this->pirateManager->setupPirates();
        
        // 5. Place dinghy as special room tile (not a card anymore)
        $this->setupDinghyTile();
        
        // Game is now ready to play!
        $this->activeNextPlayer();
    }

    /**
     * Setup the dinghy as a special room tile
     */
    private function setupDinghyTile(): void
    {
        // Create dinghy as a special room tile
        $dinghyTile = new RoomTile(
            id: 999, // Special ID for dinghy
            doors: RoomTile::DOOR_NORTH | RoomTile::DOOR_SOUTH, // Can be placed with north/south connections
            color: RoomTile::COLOR_DINGHY,
            pips: 1,
            hasPowderKeg: false,
            hasTrapdoor: false,
            isStartingTile: false
        );
        
        $this->boardManager->addSpecialTile($dinghyTile);
    }

    /**
     * Main game turn - demonstrates interaction between managers
     */
    public function stPlayerTurn(): void
    {
        // 1. Player takes actions (handled by existing systems)
        
        // 2. End of turn: Draw Skelit Revenge card
        $this->executeSkelitPhase();
        
        // 3. Check for character death and replacement
        $this->checkCharacterSurvival();
        
        // 4. Continue to next player
        $this->activeNextPlayer();
    }

    /**
     * Execute the Skelit Revenge phase (end of each turn)
     */
    private function executeSkelitPhase(): void
    {
        // Draw one Skelit Revenge card
        $skelitCard = $this->skelitManager->drawSkelitCard();
        
        if ($skelitCard) {
            // Execute the effect with manager interactions
            $this->skelitManager->executeSkelitEffect(
                $skelitCard, 
                $this->boardManager, 
                $this->tokenManager, 
                $this->pirateManager
            );
            
            // Notify players about deck status
            $deckCount = $this->skelitManager->getSkelitDeckCount();
            $discardCount = $this->skelitManager->getSkelitDiscardCount();
            
            $this->notifyAllPlayers('skelitPhaseComplete', '', [
                'skelit_card' => $skelitCard,
                'deck_count' => $deckCount,
                'discard_count' => $discardCount
            ]);
        }
    }

    /**
     * Check if any characters died and need replacement
     */
    private function checkCharacterSurvival(): void
    {
        $players = $this->loadPlayersBasicInfos();
        
        foreach ($players as $playerId => $player) {
            // Check if player's pirate died (from explosions, exhaustion, etc.)
            $playerData = $this->getObjectFromDB("SELECT * FROM player WHERE player_id = $playerId");
            
            if ($playerData['player_fatigue'] >= 10) { // Example death condition
                // Kill current character
                $this->characterManager->killPlayerCharacter($playerId, 'exhaustion');
                
                // Draw replacement character if available
                $newCharacter = $this->characterManager->drawReplacementCharacter($playerId);
                
                if (!$newCharacter) {
                    // No more characters - player is eliminated
                    $this->eliminatePlayer($playerId);
                }
            }
        }
    }

    /**
     * Action: Player swaps items with another player
     */
    public function actionSwapItems(int $targetPlayerId, int $itemId): void
    {
        $this->checkAction('swapItems');
        $currentPlayerId = $this->getCurrentPlayerId();
        
        // Use ItemManager to handle the swap
        $success = $this->itemManager->swapItemsBetweenPlayers($currentPlayerId, $targetPlayerId, $itemId);
        
        if (!$success) {
            throw new \Exception("Invalid item swap");
        }
        
        $this->gamestate->nextState('nextAction');
    }

    /**
     * Action: Player swaps item with table
     */
    public function actionSwapItemWithTable(int $tableItemId): void
    {
        $this->checkAction('swapItemWithTable');
        $playerId = $this->getCurrentPlayerId();
        
        // Use ItemManager to handle the swap
        $success = $this->itemManager->swapItemWithTable($playerId, $tableItemId);
        
        if (!$success) {
            throw new \Exception("Invalid table item swap");
        }
        
        $this->gamestate->nextState('nextAction');
    }

    /**
     * Action: Player uses an item ability
     */
    public function actionUseItem(string $ability): void
    {
        $this->checkAction('useItem');
        $playerId = $this->getCurrentPlayerId();
        
        // Use ItemManager to handle the ability
        $success = $this->itemManager->useItemAbility($playerId, $ability);
        
        if (!$success) {
            throw new \Exception("Cannot use that ability");
        }
        
        $this->gamestate->nextState('nextAction');
    }

    /**
     * Get all game data for current player
     */
    public function getAllDatas(): array
    {
        $result = parent::getAllDatas();
        $playerId = $this->getCurrentPlayerId();
        
        // Get data from specialized managers
        $result['player_character'] = $this->characterManager->getPlayerCharacterData($playerId);
        $result['player_item'] = $this->itemManager->getPlayerItemData($playerId);
        $result['table_items'] = $this->itemManager->getAvailableItemsOnTable();
        
        // Skelit deck information (public)
        $result['skelit_deck_count'] = $this->skelitManager->getSkelitDeckCount();
        $result['skelit_discard_count'] = $this->skelitManager->getSkelitDiscardCount();
        $result['last_skelit_effect'] = $this->skelitManager->getLastSkelitCard();
        
        // Character deck information (public)
        $result['character_deck_count'] = $this->characterManager->getCharacterDeckCount();
        $result['character_replacements_available'] = $this->characterManager->hasReplacementCharacters();
        
        // Item information (public)
        $result['item_counts'] = $this->itemManager->getItemCounts();
        
        // Board and other game state (unchanged)
        $result['board'] = $this->boardManager->getBoardData();
        $result['tokens'] = $this->tokenManager->getAllTokens();
        
        return $result;
    }

    /**
     * Example of manager interaction: Explosion affects items and characters
     */
    public function executeExplosion(int $x, int $y): void
    {
        // Get players in explosion area
        $playersInArea = $this->pirateManager->getPlayersAtPosition($x, $y);
        
        foreach ($playersInArea as $playerId) {
            // Check if character ability provides fire resistance
            if ($this->characterManager->hasCharacterAbility($playerId, 'fire_resistance')) {
                // Reduced damage for fire-resistant characters
                $this->notifyPlayer($playerId, 'fireResistance', 
                    clienttranslate('Your character resists some fire damage!'), []);
                continue;
            }
            
            // Check if player has protective items
            if ($this->itemManager->hasItemAbility($playerId, 'first_aid')) {
                // Player can use first aid to survive
                $this->notifyPlayer($playerId, 'firstAidAvailable', 
                    clienttranslate('You can use First Aid to reduce damage!'), []);
            }
            
            // Apply damage/fatigue
            $this->pirateManager->addFatigue($playerId, 3);
            
            // Check if character died from explosion
            if ($this->pirateManager->getFatigue($playerId) >= 10) {
                $this->characterManager->killPlayerCharacter($playerId, 'explosion');
            }
        }
    }

    /**
     * Example of item-character ability synergy
     */
    public function canPlayerMoveToRoom(int $playerId, int $toX, int $toY): bool
    {
        $fromX = $this->pirateManager->getPlayerX($playerId);
        $fromY = $this->pirateManager->getPlayerY($playerId);
        
        // Check if there's a direct door connection
        if ($this->boardManager->hasDirectConnection($fromX, $fromY, $toX, $toY)) {
            return true;
        }
        
        // Check if player has rope item (move without doors)
        if ($this->itemManager->hasItemAbility($playerId, 'rope')) {
            $this->notifyPlayer($playerId, 'ropeUsed', 
                clienttranslate('You use the rope to move between rooms!'), []);
            return true;
        }
        
        // Check if character has movement bonus (move through fire)
        if ($this->characterManager->hasCharacterAbility($playerId, 'movement_bonus')) {
            $targetRoom = $this->boardManager->getRoomAt($toX, $toY);
            if ($targetRoom && $targetRoom->getFireLevel() > 0) {
                $this->notifyPlayer($playerId, 'fireMovement', 
                    clienttranslate('Your character can move through fire!'), []);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Game progression calculation using new managers
     */
    public function getGameProgression(): int
    {
        // Base progression on various factors
        
        // Skelit deck depletion (danger increases over time)
        $skelitProgress = (19 - $this->skelitManager->getSkelitDeckCount()) / 19 * 30;
        
        // Character losses (increases urgency)
        $deadCharacters = count($this->characterManager->getDeadCharacters());
        $characterProgress = ($deadCharacters / 7) * 20;
        
        // Board exploration/treasure collection
        $treasureProgress = $this->tokenManager->getTreasureCollectionProgress() * 50;
        
        return min(100, $skelitProgress + $characterProgress + $treasureProgress);
    }

    /**
     * Debug/admin methods for testing
     */
    public function debugResetCards(): void
    {
        if (!$this->isDebugMode()) return;
        
        $this->skelitManager->resetSkelitDeck();
        $this->characterManager->resetCharacterDeck();
        $this->itemManager->resetItems();
        
        $this->notifyAllPlayers('debugReset', 
            clienttranslate('All card systems have been reset'), []);
    }

    /**
     * Get comprehensive card status for debugging
     */
    public function getCardSystemStatus(): array
    {
        if (!$this->isDebugMode()) return [];
        
        return [
            'skelit_cards' => $this->skelitManager->getAllSkelitCards(),
            'character_cards' => $this->characterManager->getAllCharacterCards(),
            'item_cards' => $this->itemManager->getAllItemsData()
        ];
    }
}
