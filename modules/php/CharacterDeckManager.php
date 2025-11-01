<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\DB\DBManager;
use Bga\Games\DeadMenPax\DB\Models\CharacterCardModel;
use Bga\Games\DeadMenPax\DB\PlayerDBManager;

/**
 * Manages Character cards using BGA DECK component
 * 7 cards total - one per player initially, remainder for pirate replacement (no reshuffle)
 */
class CharacterDeckManager
{
    private Table $game;
    private $characterDeck; // BGA Deck component
    private PlayerDBManager $playerDBManager;
    
    // Card locations
    public const LOCATION_DECK = 'deck';
    public const LOCATION_PLAYER = 'player';
    public const LOCATION_DISCARD = 'discard';
    
    // Character types (different pirate abilities)
    public const CHARACTER_LYDIA_LAMORE = 1;    // Extra action
    public const CHARACTER_CAPTAIN_BONES = 2;   // Fire resistance
    public const CHARACTER_BLACKBEARD = 3;      // Battle bonus
    public const CHARACTER_ANNE_BONNY = 4;      // Movement bonus
    public const CHARACTER_CALICO_JACK = 5;     // Lock picking
    public const CHARACTER_MARY_READ = 6;       // Treasure finding
    public const CHARACTER_HENRY_MORGAN = 7;    // Leadership

    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->characterDeck = $this->game->deckFactory->createDeck('character_card');
        $this->playerDBManager = new PlayerDBManager($game);
        
        // NO auto-reshuffle: character deck depletes permanently
        $this->characterDeck->autoreshuffle = false;
    }

    /**
     * Setup all 7 Character cards during game initialization
     */
    public function setupCharacterCards(): void
    {
        $characterCards = [];
        
        // Create all 7 unique character cards
        for ($i = 1; $i <= 7; $i++) {
            $characterCards[] = [
                'type' => 'character',
                'type_arg' => $i,
                'nbr' => 1
            ];
        }
        
        // Create all cards in deck location
        $this->characterDeck->createCards($characterCards, self::LOCATION_DECK);
        
        // Shuffle the deck for random distribution
        $this->characterDeck->shuffle(self::LOCATION_DECK);
    }

    /**
     * Deal starting character cards to all players (one per player)
     */
    public function dealStartingCharacterCards(): void
    {
        $players = $this->game->loadPlayersBasicInfos();
        
        foreach ($players as $playerId => $player) {
            // Draw one character card for each player
            $characterCard = $this->characterDeck->pickCard(self::LOCATION_DECK, $playerId);
            
            if ($characterCard) {
                $player = $this->playerDBManager->createObjectFromDB($playerId);
                $player->characterCardId = $characterCard['id'];
                $this->playerDBManager->saveObjectToDB($player);
                
                // Apply character setup effects
                $this->applyCharacterSetupEffects($playerId, $characterCard['card_type_arg']);
                
                // Notify player about their character
                $this->game->notifyPlayer($playerId, 'newCharacterCard', '', [
                    'card' => $characterCard,
                    'character_name' => $this->getCharacterName($characterCard['card_type_arg']),
                    'character_ability' => $this->getCharacterAbility($characterCard['card_type_arg'])
                ]);
            }
        }
    }

    /**
     * Draw replacement character card when a pirate dies
     */
    public function drawReplacementCharacter(int $playerId): ?array
    {
        // Check if there are any characters left in the deck
        $deckCount = $this->characterDeck->countCardInLocation(self::LOCATION_DECK);
        
        if ($deckCount === 0) {
            $this->game->notifyPlayer($playerId, 'noCharacterReplacement', 
                clienttranslate('No replacement characters available!'), []);
            return null;
        }
        
        // Draw a replacement character
        $characterCard = $this->characterDeck->pickCard(self::LOCATION_DECK, $playerId);
        
        if ($characterCard) {
            $player = $this->playerDBManager->createObjectFromDB($playerId);
            $player->characterCardId = $characterCard['id'];
            $this->playerDBManager->saveObjectToDB($player);
            
            // Apply character setup effects
            $this->applyCharacterSetupEffects($playerId, $characterCard['card_type_arg']);
            
            // Notify about replacement
            $this->game->notifyAllPlayers('characterReplacement', 
                clienttranslate('${player_name} gets a new pirate character'), 
                [
                    'player_id' => $playerId,
                    'player_name' => $this->game->getPlayerNameById($playerId),
                    'character_name' => $this->getCharacterName($characterCard['card_type_arg'])
                ]
            );
            
            $this->game->notifyPlayer($playerId, 'newCharacterCard', '', [
                'card' => $characterCard,
                'character_name' => $this->getCharacterName($characterCard['card_type_arg']),
                'character_ability' => $this->getCharacterAbility($characterCard['card_type_arg'])
            ]);
            
            return $characterCard;
        }
        
        return null;
    }

    /**
     * Get player's current character card
     */
    public function getPlayerCharacterCard(int $playerId): ?array
    {
        $playerCards = $this->characterDeck->getCardsInLocation(self::LOCATION_PLAYER, $playerId);
        return !empty($playerCards) ? array_values($playerCards)[0] : null;
    }

    /**
     * Get character name by type_arg
     */
    public function getCharacterName(int $typeArg): string
    {
        switch ($typeArg) {
            case self::CHARACTER_LYDIA_LAMORE:
                return 'Lydia Lamore';
            case self::CHARACTER_CAPTAIN_BONES:
                return 'Captain Bones';
            case self::CHARACTER_BLACKBEARD:
                return 'Blackbeard';
            case self::CHARACTER_ANNE_BONNY:
                return 'Anne Bonny';
            case self::CHARACTER_CALICO_JACK:
                return 'Calico Jack';
            case self::CHARACTER_MARY_READ:
                return 'Mary Read';
            case self::CHARACTER_HENRY_MORGAN:
                return 'Henry Morgan';
            default:
                return 'Unknown Pirate';
        }
    }

    /**
     * Get character ability by type_arg
     */
    public function getCharacterAbility(int $typeArg): string
    {
        switch ($typeArg) {
            case self::CHARACTER_LYDIA_LAMORE:
                return 'extra_action'; // Gets 6 actions instead of 5
            case self::CHARACTER_CAPTAIN_BONES:
                return 'fire_resistance'; // Reduced fire damage
            case self::CHARACTER_BLACKBEARD:
                return 'battle_bonus'; // +2 battle strength
            case self::CHARACTER_ANNE_BONNY:
                return 'movement_bonus'; // Can move through fire
            case self::CHARACTER_CALICO_JACK:
                return 'lock_picking'; // Can open locked doors
            case self::CHARACTER_MARY_READ:
                return 'treasure_finding'; // Finds extra treasure
            case self::CHARACTER_HENRY_MORGAN:
                return 'leadership'; // Boosts other pirates' abilities
            default:
                return '';
        }
    }

    /**
     * Get character ability description
     */
    public function getCharacterAbilityDescription(int $typeArg): string
    {
        switch ($typeArg) {
            case self::CHARACTER_LYDIA_LAMORE:
                return 'Gets 6 actions per turn instead of 5';
            case self::CHARACTER_CAPTAIN_BONES:
                return 'Takes reduced damage from fire';
            case self::CHARACTER_BLACKBEARD:
                return 'Has +2 battle strength in combat';
            case self::CHARACTER_ANNE_BONNY:
                return 'Can move through rooms with fire';
            case self::CHARACTER_CALICO_JACK:
                return 'Can open locked doors without keys';
            case self::CHARACTER_MARY_READ:
                return 'Finds extra treasure when searching';
            case self::CHARACTER_HENRY_MORGAN:
                return 'Boosts abilities of nearby pirates';
            default:
                return 'Unknown ability';
        }
    }

    /**
     * Apply character ability effects during setup or replacement
     */
    public function applyCharacterSetupEffects(int $playerId, int $characterType): void
    {
        $ability = $this->getCharacterAbility($characterType);
        
        $player = $this->playerDBManager->createObjectFromDB($playerId);
        $player->maxActions = 5;
        $player->actionsRemaining = 5;
        $player->battleStrength = 0;
        
        switch ($ability) {
            case 'extra_action':
                // Lydia Lamore gets 6 actions instead of 5
                $player->maxActions = 6;
                $player->actionsRemaining = 6;
                break;
                
            case 'battle_bonus':
                // Blackbeard starts with +2 battle strength
                $player->battleStrength = 2;
                break;
                
            // Other character effects are applied dynamically during gameplay
            // (fire_resistance, movement_bonus, etc.)
        }
        
        $this->playerDBManager->saveObjectToDB($player);
    }

    /**
     * Check if player has specific character ability
     */
    public function hasCharacterAbility(int $playerId, string $ability): bool
    {
        $characterCard = $this->getPlayerCharacterCard($playerId);
        
        if (!$characterCard) {
            return false;
        }
        
        $characterAbility = $this->getCharacterAbility($characterCard['card_type_arg']);
        return $characterAbility === $ability;
    }

    /**
     * Get player's character ability
     */
    public function getPlayerCharacterAbility(int $playerId): string
    {
        $characterCard = $this->getPlayerCharacterCard($playerId);
        
        if (!$characterCard) {
            return '';
        }
        
        return $this->getCharacterAbility($characterCard['card_type_arg']);
    }

    /**
     * Kill player's character (move to discard, prepare for replacement)
     */
    public function killPlayerCharacter(int $playerId, string $deathReason = 'explosion'): void
    {
        $characterCard = $this->getPlayerCharacterCard($playerId);
        
        if ($characterCard) {
            // Move character to discard pile
            $this->characterDeck->moveCard($characterCard['id'], self::LOCATION_DISCARD, 0);
            
            $player = $this->playerDBManager->createObjectFromDB($playerId);
            $player->characterCardId = null;
            $this->playerDBManager->saveObjectToDB($player);
            
            // Notify about character death
            $characterName = $this->getCharacterName($characterCard['card_type_arg']);
            $this->game->notifyAllPlayers('characterDeath', 
                clienttranslate('${player_name}\'s pirate ${character_name} has died from ${death_reason}!'), 
                [
                    'player_id' => $playerId,
                    'player_name' => $this->game->getPlayerNameById($playerId),
                    'character_name' => $characterName,
                    'death_reason' => $deathReason
                ]
            );
        }
    }

    /**
     * Get current deck count for UI display
     */
    public function getCharacterDeckCount(): int
    {
        return $this->characterDeck->countCardInLocation(self::LOCATION_DECK);
    }

    /**
     * Get all dead characters (in discard pile)
     */
    public function getDeadCharacters(): array
    {
        return $this->characterDeck->getCardsInLocation(self::LOCATION_DISCARD);
    }

    /**
     * Get all active characters (with players)
     */
    public function getActiveCharacters(): array
    {
        return $this->characterDeck->getCardsInLocation(self::LOCATION_PLAYER);
    }

    /**
     * Get all character cards for debugging/admin purposes
     */
    public function getAllCharacterCards(): array
    {
        return [
            'deck' => $this->characterDeck->getCardsInLocation(self::LOCATION_DECK),
            'players' => $this->characterDeck->getCardsInLocation(self::LOCATION_PLAYER),
            'discard' => $this->characterDeck->getCardsInLocation(self::LOCATION_DISCARD)
        ];
    }

    /**
     * Get player items data including character info
     */
    public function getPlayerCharacterData(int $playerId): array
    {
        $characterCard = $this->getPlayerCharacterCard($playerId);
        
        if (!$characterCard) {
            return [
                'character' => null,
                'character_name' => '',
                'character_ability' => '',
                'character_ability_description' => ''
            ];
        }
        
        return [
            'character' => $characterCard,
            'character_name' => $this->getCharacterName($characterCard['card_type_arg']),
            'character_ability' => $this->getCharacterAbility($characterCard['card_type_arg']),
            'character_ability_description' => $this->getCharacterAbilityDescription($characterCard['card_type_arg'])
        ];
    }

    /**
     * Check if any replacement characters are available
     */
    public function hasReplacementCharacters(): bool
    {
        return $this->getCharacterDeckCount() > 0;
    }

    /**
     * Reset character deck for game restart (debugging/admin)
     */
    public function resetCharacterDeck(): void
    {
        $this->characterDeck->moveAllCardsInLocation(null, self::LOCATION_DECK);
        $this->characterDeck->shuffle(self::LOCATION_DECK);
        
        $this->game->notifyAllPlayers('characterDeckReset', 
            clienttranslate('The Character deck has been reset'), []);
    }
}
