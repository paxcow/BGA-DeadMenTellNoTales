<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\Managers;

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

    /**
     * Constructor.
     *
     * @param Table $game The game instance.
     */
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
     * Callback method called when the skelit deck auto-reshuffles.
     */
    public function onSkelitDeckReshuffle(): void
    {
        $this->game->notifyAllPlayers('skelitDeckReshuffled', 
            clienttranslate('The Skelit Revenge deck has been reshuffled'), []);
    }

    /**
     * Sets up all 19 Skelit Revenge cards during game initialization.
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
     * Draws one Skelit Revenge card during the Skelit phase.
     *
     * @return array|null The drawn card, or null if no card could be drawn.
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
     * Gets the current deck count for UI display.
     *
     * @return int The number of cards in the deck.
     */
    public function getSkelitDeckCount(): int
    {
        return $this->skelitDeck->countCardInLocation(self::LOCATION_DECK);
    }

    /**
     * Gets the discard pile count for UI display.
     *
     * @return int The number of cards in the discard pile.
     */
    public function getSkelitDiscardCount(): int
    {
        return $this->skelitDeck->countCardInLocation(self::LOCATION_DISCARD);
    }

    /**
     * Gets all cards in the discard pile.
     *
     * @return array An array of cards in the discard pile.
     */
    public function getSkelitDiscardPile(): array
    {
        return $this->skelitDeck->getCardsInLocation(self::LOCATION_DISCARD);
    }

    /**
     * Gets the last discarded skelit card.
     *
     * @return array|null The last discarded card, or null if the discard pile is empty.
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
     * Gets the description of a skelit effect by `type_arg`.
     *
     * @param int $typeArg The `type_arg` of the effect.
     * @return string The description of the effect.
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
     * Executes the skelit revenge effect based on card type.
     *
     * @param array $skelitCard The skelit card.
     * @param mixed $boardManager The board manager.
     * @param mixed $tokenManager The token manager.
     * @param mixed $pirateManager The pirate manager.
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
     * Executes the fire spread effect.
     *
     * @param mixed $boardManager The board manager.
     */
    private function executeFireSpreadEffect($boardManager): void
    {
        // Implementation would spread fire to adjacent rooms
        // This would interact with the BoardManager
        
        $this->game->notifyAllPlayers('fireSpread', 
            clienttranslate('Fire spreads across the ship!'), []);
    }

    /**
     * Executes the skeleton crew effect.
     *
     * @param mixed $tokenManager The token manager.
     * @param mixed $boardManager The board manager.
     */
    private function executeSkeletonCrewEffect($tokenManager, $boardManager): void
    {
        // Implementation would spawn skeleton crew tokens
        // This would interact with TokenManager and BoardManager
        
        $this->game->notifyAllPlayers('skeletonCrewAppears', 
            clienttranslate('Skeleton crew members appear on the ship!'), []);
    }

    /**
     * Executes the room collapse effect.
     *
     * @param mixed $boardManager The board manager.
     */
    private function executeRoomCollapseEffect($boardManager): void
    {
        // Implementation would collapse a random room
        // This would interact with BoardManager
        
        $this->game->notifyAllPlayers('roomCollapse', 
            clienttranslate('A room collapses into the sea!'), []);
    }

    /**
     * Executes the treasure guard effect.
     *
     * @param mixed $tokenManager The token manager.
     * @param mixed $boardManager The board manager.
     */
    private function executeTreasureGuardEffect($tokenManager, $boardManager): void
    {
        // Implementation would add guards to treasures
        // This would interact with TokenManager
        
        $this->game->notifyAllPlayers('treasureGuarded', 
            clienttranslate('Treasures become heavily guarded!'), []);
    }

    /**
     * Executes the powder keg effect.
     *
     * @param mixed $boardManager The board manager.
     */
    private function executePowderKegEffect($boardManager): void
    {
        // Implementation would explode powder kegs
        // This would interact with BoardManager
        
        $this->game->notifyAllPlayers('powderKegExplosion', 
            clienttranslate('Powder kegs explode across the ship!'), []);
    }

    /**
     * Executes the lock doors effect.
     *
     * @param mixed $boardManager The board manager.
     */
    private function executeLockDoorsEffect($boardManager): void
    {
        // Implementation would lock doors in rooms
        // This would interact with BoardManager
        
        $this->game->notifyAllPlayers('doorsLocked', 
            clienttranslate('Doors slam shut and become locked!'), []);
    }

    /**
     * Executes the exhaustion effect.
     *
     * @param mixed $pirateManager The pirate manager.
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
     * Manually shuffles the skelit deck if needed.
     */
    public function shuffleSkelitDeck(): void
    {
        $this->skelitDeck->shuffle(self::LOCATION_DECK);
        
        $this->game->notifyAllPlayers('skelitDeckShuffled', 
            clienttranslate('The Skelit Revenge deck has been shuffled'), []);
    }

    /**
     * Gets all skelit cards for debugging/admin purposes.
     *
     * @return array An array of all skelit cards.
     */
    public function getAllSkelitCards(): array
    {
        return [
            'deck' => $this->skelitDeck->getCardsInLocation(self::LOCATION_DECK),
            'discard' => $this->skelitDeck->getCardsInLocation(self::LOCATION_DISCARD)
        ];
    }

    /**
     * Resets the skelit deck.
     */
    public function resetSkelitDeck(): void
    {
        $this->skelitDeck->moveAllCardsInLocation(null, self::LOCATION_DECK);
        $this->skelitDeck->shuffle(self::LOCATION_DECK);
        
        $this->game->notifyAllPlayers('skelitDeckReset', 
            clienttranslate('The Skelit Revenge deck has been reset'), []);
    }
}
