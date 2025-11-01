<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\DB\PlayerDBManager;

/**
 * Manages Skelit Revenge cards using BGA DECK component
 * 19 cards total - drawn one per turn, auto-reshuffle when deck empty
 */
class SkelitDeckManager
{
    private Table $game;
    private $skelitDeck; // BGA Deck component
    private PlayerDBManager $playerDBManager;
    
    // Card locations
    public const LOCATION_DECK = 'deck';
    public const LOCATION_DISCARD = 'discard';
    
    // Skelit card types (different revenge effects)
    public const SKELIT_FIRE_SPREADS = 1;
    public const SKELIT_SKELETON_CREW = 2;
    public const SKELIT_ROOM_COLLAPSE = 3;
    public const SKELIT_TREASURE_GUARD = 4;
    public const SKELIT_POWDER_KEG = 5;
    public const SKELIT_LOCK_DOORS = 6;
    public const SKELIT_EXHAUSTION = 7;

    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->skelitDeck = $this->game->deckFactory->createDeck('skelit_card');
        $this->playerDBManager = new PlayerDBManager($game);
        
        // Enable auto-reshuffle: when deck is empty, shuffle discard pile back into deck
        $this->skelitDeck->autoreshuffle = true;
        
        // Set up callback for deck reshuffle notifications
        $this->skelitDeck->autoreshuffle_trigger = [
            'obj' => $this, 
            'method' => 'onSkelitDeckReshuffle'
        ];
    }

    /**
     * Callback method called when skelit deck auto-reshuffles
     */
    public function onSkelitDeckReshuffle(): void
    {
        $this->game->notifyAllPlayers('skelitDeckReshuffled', 
            clienttranslate('The Skelit Revenge deck has been reshuffled'), []);
    }

    /**
     * Setup all 19 Skelit Revenge cards during game initialization
     */
    public function setupSkelitCards(): void
    {
        $skelitCards = [];
        
        // Create the 19 Skelit Revenge cards with different effects
        // Distribution based on game balance
        
        // Fire Spreads (3 cards)
        for ($i = 0; $i < 3; $i++) {
            $skelitCards[] = [
                'type' => 'skelit',
                'type_arg' => self::SKELIT_FIRE_SPREADS,
                'nbr' => 1
            ];
        }
        
        // Skeleton Crew appears (4 cards) 
        for ($i = 0; $i < 4; $i++) {
            $skelitCards[] = [
                'type' => 'skelit',
                'type_arg' => self::SKELIT_SKELETON_CREW,
                'nbr' => 1
            ];
        }
        
        // Room Collapse (2 cards)
        for ($i = 0; $i < 2; $i++) {
            $skelitCards[] = [
                'type' => 'skelit',
                'type_arg' => self::SKELIT_ROOM_COLLAPSE,
                'nbr' => 1
            ];
        }
        
        // Treasure becomes Guarded (3 cards)
        for ($i = 0; $i < 3; $i++) {
            $skelitCards[] = [
                'type' => 'skelit',
                'type_arg' => self::SKELIT_TREASURE_GUARD,
                'nbr' => 1
            ];
        }
        
        // Powder Keg explodes (2 cards)
        for ($i = 0; $i < 2; $i++) {
            $skelitCards[] = [
                'type' => 'skelit',
                'type_arg' => self::SKELIT_POWDER_KEG,
                'nbr' => 1
            ];
        }
        
        // Doors get locked (3 cards)
        for ($i = 0; $i < 3; $i++) {
            $skelitCards[] = [
                'type' => 'skelit',
                'type_arg' => self::SKELIT_LOCK_DOORS,
                'nbr' => 1
            ];
        }
        
        // Pirates gain exhaustion (2 cards)
        for ($i = 0; $i < 2; $i++) {
            $skelitCards[] = [
                'type' => 'skelit',
                'type_arg' => self::SKELIT_EXHAUSTION,
                'nbr' => 1
            ];
        }
        
        // Create all cards in deck location
        $this->skelitDeck->createCards($skelitCards, self::LOCATION_DECK);
        
        // Shuffle the deck
        $this->skelitDeck->shuffle(self::LOCATION_DECK);
    }

    /**
     * Draw one Skelit Revenge card during the Skelit phase
     */
    public function drawSkelitCard(): ?array
    {
        $card = $this->skelitDeck->pickCardForLocation(self::LOCATION_DECK, 'temp');
        
        if ($card) {
            // Immediately discard the card after drawing (it's used once)
            $this->skelitDeck->playCard($card['id']); // Moves to discard pile
            
            return $card;
        }
        
        return null;
    }

    /**
     * Get the current deck count for UI display
     */
    public function getSkelitDeckCount(): int
    {
        return $this->skelitDeck->countCardInLocation(self::LOCATION_DECK);
    }

    /**
     * Get the discard pile count for UI display
     */
    public function getSkelitDiscardCount(): int
    {
        return $this->skelitDeck->countCardInLocation(self::LOCATION_DISCARD);
    }

    /**
     * Get all cards in discard pile (for viewing recent effects)
     */
    public function getSkelitDiscardPile(): array
    {
        return $this->skelitDeck->getCardsInLocation(self::LOCATION_DISCARD);
    }

    /**
     * Get the last discarded skelit card (most recent effect)
     */
    public function getLastSkelitCard(): ?array
    {
        $discardPile = $this->getSkelitDiscardPile();
        
        if (empty($discardPile)) {
            return null;
        }
        
        // Get the card with highest location_arg (most recently discarded)
        $lastCard = null;
        $maxLocationArg = -1;
        
        foreach ($discardPile as $card) {
            if ($card['card_location_arg'] > $maxLocationArg) {
                $maxLocationArg = $card['card_location_arg'];
                $lastCard = $card;
            }
        }
        
        return $lastCard;
    }

    /**
     * Get description of skelit effect by type_arg
     */
    public function getSkelitEffectDescription(int $typeArg): string
    {
        switch ($typeArg) {
            case self::SKELIT_FIRE_SPREADS:
                return 'Fire spreads to adjacent rooms';
            case self::SKELIT_SKELETON_CREW:
                return 'Skeleton crew appears in random rooms';
            case self::SKELIT_ROOM_COLLAPSE:
                return 'Random room tile collapses';
            case self::SKELIT_TREASURE_GUARD:
                return 'Random treasure becomes guarded';
            case self::SKELIT_POWDER_KEG:
                return 'Random powder keg explodes';
            case self::SKELIT_LOCK_DOORS:
                return 'Doors in random rooms become locked';
            case self::SKELIT_EXHAUSTION:
                return 'All pirates gain exhaustion';
            default:
                return 'Unknown skelit effect';
        }
    }

    /**
     * Execute the skelit revenge effect based on card type
     */
    public function executeSkelitEffect(array $skelitCard, $boardManager, $tokenManager, $pirateManager): void
    {
        $effectType = $skelitCard['card_type_arg'];
        $effectDescription = $this->getSkelitEffectDescription($effectType);
        
        $this->game->notifyAllPlayers('skelitEffect', 
            clienttranslate('Skelit\'s Revenge: ${effect}'), 
            [
                'effect' => $effectDescription,
                'card' => $skelitCard
            ]
        );
        
        switch ($effectType) {
            case self::SKELIT_FIRE_SPREADS:
                $this->executeFireSpreadEffect($boardManager);
                break;
                
            case self::SKELIT_SKELETON_CREW:
                $this->executeSkeletonCrewEffect($tokenManager, $boardManager);
                break;
                
            case self::SKELIT_ROOM_COLLAPSE:
                $this->executeRoomCollapseEffect($boardManager);
                break;
                
            case self::SKELIT_TREASURE_GUARD:
                $this->executeTreasureGuardEffect($tokenManager, $boardManager);
                break;
                
            case self::SKELIT_POWDER_KEG:
                $this->executePowderKegEffect($boardManager);
                break;
                
            case self::SKELIT_LOCK_DOORS:
                $this->executeLockDoorsEffect($boardManager);
                break;
                
            case self::SKELIT_EXHAUSTION:
                $this->executeExhaustionEffect($pirateManager);
                break;
        }
    }

    /**
     * Execute fire spread effect - fire spreads to adjacent rooms
     */
    private function executeFireSpreadEffect($boardManager): void
    {
        // Implementation would spread fire to adjacent rooms
        // This would interact with the BoardManager
        
        $this->game->notifyAllPlayers('fireSpread', 
            clienttranslate('Fire spreads across the ship!'), []);
    }

    /**
     * Execute skeleton crew effect - skeleton crew appears in random rooms
     */
    private function executeSkeletonCrewEffect($tokenManager, $boardManager): void
    {
        // Implementation would spawn skeleton crew tokens
        // This would interact with TokenManager and BoardManager
        
        $this->game->notifyAllPlayers('skeletonCrewAppears', 
            clienttranslate('Skeleton crew members appear on the ship!'), []);
    }

    /**
     * Execute room collapse effect - random room tile collapses
     */
    private function executeRoomCollapseEffect($boardManager): void
    {
        // Implementation would collapse a random room
        // This would interact with BoardManager
        
        $this->game->notifyAllPlayers('roomCollapse', 
            clienttranslate('A room collapses into the sea!'), []);
    }

    /**
     * Execute treasure guard effect - random treasure becomes guarded
     */
    private function executeTreasureGuardEffect($tokenManager, $boardManager): void
    {
        // Implementation would add guards to treasures
        // This would interact with TokenManager
        
        $this->game->notifyAllPlayers('treasureGuarded', 
            clienttranslate('Treasures become heavily guarded!'), []);
    }

    /**
     * Execute powder keg effect - random powder keg explodes
     */
    private function executePowderKegEffect($boardManager): void
    {
        // Implementation would explode powder kegs
        // This would interact with BoardManager
        
        $this->game->notifyAllPlayers('powderKegExplosion', 
            clienttranslate('Powder kegs explode across the ship!'), []);
    }

    /**
     * Execute lock doors effect - doors in random rooms become locked
     */
    private function executeLockDoorsEffect($boardManager): void
    {
        // Implementation would lock doors in rooms
        // This would interact with BoardManager
        
        $this->game->notifyAllPlayers('doorsLocked', 
            clienttranslate('Doors slam shut and become locked!'), []);
    }

    /**
     * Execute exhaustion effect - all pirates gain exhaustion
     */
    private function executeExhaustionEffect($pirateManager): void
    {
        // Implementation would add exhaustion to all pirates
        // This would interact with PirateManager
        
        $players = $this->playerDBManager->getAllObjects();
        foreach ($players as $player) {
            $player->fatigue++;
            $this->playerDBManager->saveObjectToDB($player);
        }
        
        $this->game->notifyAllPlayers('piratesExhausted', 
            clienttranslate('All pirates become more exhausted!'), []);
    }

    /**
     * Manually shuffle the skelit deck if needed
     */
    public function shuffleSkelitDeck(): void
    {
        $this->skelitDeck->shuffle(self::LOCATION_DECK);
        
        $this->game->notifyAllPlayers('skelitDeckShuffled', 
            clienttranslate('The Skelit Revenge deck has been shuffled'), []);
    }

    /**
     * Get all skelit cards for debugging/admin purposes
     */
    public function getAllSkelitCards(): array
    {
        return [
            'deck' => $this->skelitDeck->getCardsInLocation(self::LOCATION_DECK),
            'discard' => $this->skelitDeck->getCardsInLocation(self::LOCATION_DISCARD)
        ];
    }

    /**
     * Reset skelit deck - move all cards back to deck and shuffle
     */
    public function resetSkelitDeck(): void
    {
        $this->skelitDeck->moveAllCardsInLocation(null, self::LOCATION_DECK);
        $this->skelitDeck->shuffle(self::LOCATION_DECK);
        
        $this->game->notifyAllPlayers('skelitDeckReset', 
            clienttranslate('The Skelit Revenge deck has been reset'), []);
    }
}
