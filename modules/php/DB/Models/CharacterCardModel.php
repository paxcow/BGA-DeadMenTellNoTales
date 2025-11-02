<?php

namespace Bga\Games\DeadMenPax\DB\Models;

use Bga\Games\DeadMenPax\DB\dbColumn;
use Bga\Games\DeadMenPax\DB\dbKey;

/**
 * Character card model representing the character_card database table
 */
class CharacterCardModel
{
    #[dbKey('card_id')]
    private int $cardId;

    #[dbColumn('card_type')]
    private string $cardType;

    #[dbColumn('card_type_arg')]
    private int $cardTypeArg;

    #[dbColumn('card_location')]
    private string $cardLocation;

    #[dbColumn('card_location_arg')]
    private int $cardLocationArg;

    /**
     * Gets the card ID.
     *
     * @return int
     */
    public function getCardId(): int
    {
        return $this->cardId;
    }

    /**
     * Gets the card type.
     *
     * @return string
     */
    public function getCardType(): string
    {
        return $this->cardType;
    }

    /**
     * Gets the card type argument.
     *
     * @return int
     */
    public function getCardTypeArg(): int
    {
        return $this->cardTypeArg;
    }

    /**
     * Gets the card location.
     *
     * @return string
     */
    public function getCardLocation(): string
    {
        return $this->cardLocation;
    }

    /**
     * Gets the card location argument.
     *
     * @return int
     */
    public function getCardLocationArg(): int
    {
        return $this->cardLocationArg;
    }

    /**
     * Sets the card ID.
     *
     * @param int $cardId
     */
    public function setCardId(int $cardId): void
    {
        $this->cardId = $cardId;
    }

    /**
     * Sets the card type.
     *
     * @param string $cardType
     */
    public function setCardType(string $cardType): void
    {
        $this->cardType = $cardType;
    }

    /**
     * Sets the card type argument.
     *
     * @param int $cardTypeArg
     */
    public function setCardTypeArg(int $cardTypeArg): void
    {
        $this->cardTypeArg = $cardTypeArg;
    }

    /**
     * Sets the card location.
     *
     * @param string $cardLocation
     */
    public function setCardLocation(string $cardLocation): void
    {
        $this->cardLocation = $cardLocation;
    }

    /**
     * Sets the card location argument.
     *
     * @param int $cardLocationArg
     */
    public function setCardLocationArg(int $cardLocationArg): void
    {
        $this->cardLocationArg = $cardLocationArg;
    }

    /**
     * Checks if the card is in a player's hand.
     *
     * @return bool
     */
    public function isInHand(): bool
    {
        return $this->cardLocation === 'player';
    }

    /**
     * Checks if the card is in the deck.
     *
     * @return bool
     */
    public function isInDeck(): bool
    {
        return $this->cardLocation === 'deck';
    }

    /**
     * Checks if the card is in the discard pile.
     *
     * @return bool
     */
    public function isDiscarded(): bool
    {
        return $this->cardLocation === 'discard';
    }

    /**
     * Moves the card to a player's hand.
     *
     * @param int $playerId
     */
    public function moveToPlayer(int $playerId): void
    {
        $this->cardLocation = 'player';
        $this->cardLocationArg = $playerId;
    }

    /**
     * Moves the card to the deck.
     */
    public function moveToDeck(): void
    {
        $this->cardLocation = 'deck';
        $this->cardLocationArg = 0;
    }

    /**
     * Moves the card to the discard pile.
     */
    public function discard(): void
    {
        $this->cardLocation = 'discard';
        $this->cardLocationArg = 0;
    }

    /**
     * Creates a CharacterCardModel from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $model = new self();
        $model->cardId = (int) $data['card_id'];
        $model->cardType = $data['card_type'];
        $model->cardTypeArg = (int) $data['card_type_arg'];
        $model->cardLocation = $data['card_location'];
        $model->cardLocationArg = (int) $data['card_location_arg'];
        
        return $model;
    }

    /**
     * Converts the CharacterCardModel to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'card_id' => $this->cardId,
            'card_type' => $this->cardType,
            'card_type_arg' => $this->cardTypeArg,
            'card_location' => $this->cardLocation,
            'card_location_arg' => $this->cardLocationArg,
        ];
    }
}
