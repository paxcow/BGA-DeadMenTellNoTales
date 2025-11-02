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

    /**
     * Constructor.
     *
     * @param Table $game The game instance.
     */
    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->characterDeck = $this->game->deckFactory->createDeck('character_card');
        $this->playerDBManager = new PlayerDBManager($game);
        
        // NO auto-reshuffle: character deck depletes permanently
        $this->characterDeck->autoreshuffle = false;
    }

    /**
     * Sets up all 7 Character cards during game initialization.
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
     * Deals starting character cards to all players.
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
     * Draws a replacement character card when a pirate dies.
     *
     * @param int $playerId The ID of the player.
     * @return array|null The replacement character card, or null if none are available.
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
     * Gets a player's current character card.
     *
     * @param int $playerId The ID of the player.
     * @return array|null The character card, or null if the player has no character.
     */
    public function getPlayerCharacterCard(int $playerId): ?array
    {
        $playerCards = $this->characterDeck->getCardsInLocation(self::LOCATION_PLAYER, $playerId);
        return !empty($playerCards) ? array_values($playerCards)[0] : null;
    }

    /**
     * Gets a character name by `type_arg`.
     *
     * @param int $typeArg The `type_arg` of the character.
     * @return string The name of the character.
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
     * Gets a character ability by `type_arg`.
     *
     * @param int $typeArg The `type_arg` of the character.
     * @return string The ability of the character.
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
     * Gets a character ability description.
     *
     * @param int $typeArg The `type_arg` of the character.
     * @return string The ability description of the character.
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
     * Applies character ability effects during setup or replacement.
     *
     * @param int $playerId The ID of the player.
     * @param int $characterType The type of the character.
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
     * Checks if a player has a specific character ability.
     *
     * @param int $playerId The ID of the player.
     * @param string $ability The ability to check for.
     * @return bool True if the player has the ability, false otherwise.
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
     * Gets a player's character ability.
     *
     * @param int $playerId The ID of the player.
     * @return string The character ability.
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
     * Kills a player's character.
     *
     * @param int $playerId The ID of the player.
     * @param string $deathReason The reason for the character's death.
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
     * Gets the current deck count for UI display.
     *
     * @return int The number of cards in the deck.
     */
    public function getCharacterDeckCount(): int
    {
        return $this->characterDeck->countCardInLocation(self::LOCATION_DECK);
    }

    /**
     * Gets all dead characters.
     *
     * @return array An array of dead characters.
     */
    public function getDeadCharacters(): array
    {
        return $this->characterDeck->getCardsInLocation(self::LOCATION_DISCARD);
    }

    /**
     * Gets all active characters.
     *
     * @return array An array of active characters.
     */
    public function getActiveCharacters(): array
    {
        return $this->characterDeck->getCardsInLocation(self::LOCATION_PLAYER);
    }

    /**
     * Gets all character cards for debugging/admin purposes.
     *
     * @return array An array of all character cards.
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
     * Gets player items data including character info.
     *
     * @param int $playerId The ID of the player.
     * @return array An array of player character data.
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
     * Checks if any replacement characters are available.
     *
     * @return bool True if replacement characters are available, false otherwise.
     */
    public function hasReplacementCharacters(): bool
    {
        return $this->getCharacterDeckCount() > 0;
    }

    /**
     * Resets the character deck for game restart.
     */
    public function resetCharacterDeck(): void
    {
        $this->characterDeck->moveAllCardsInLocation(null, self::LOCATION_DECK);
        $this->characterDeck->shuffle(self::LOCATION_DECK);
        
        $this->game->notifyAllPlayers('characterDeckReset', 
            clienttranslate('The Character deck has been reset'), []);
    }
}
