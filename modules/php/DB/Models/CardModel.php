<?php

namespace Bga\Games\DeadMenPax\DB\Models;

use Bga\Games\DeadMenPax\DB\dbColumn;
use Bga\Games\DeadMenPax\DB\dbKey;

/**
 * Card model representing the card database table
 */
class CardModel
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

    // Getters
    public function getCardId(): int
    {
        return $this->cardId;
    }

    public function getCardType(): string
    {
        return $this->cardType;
    }

    public function getCardTypeArg(): int
    {
        return $this->cardTypeArg;
    }

    public function getCardLocation(): string
    {
        return $this->cardLocation;
    }

    public function getCardLocationArg(): int
    {
        return $this->cardLocationArg;
    }

    // Setters
    public function setCardId(int $cardId): void
    {
        $this->cardId = $cardId;
    }

    public function setCardType(string $cardType): void
    {
        $this->cardType = $cardType;
    }

    public function setCardTypeArg(int $cardTypeArg): void
    {
        $this->cardTypeArg = $cardTypeArg;
    }

    public function setCardLocation(string $cardLocation): void
    {
        $this->cardLocation = $cardLocation;
    }

    public function setCardLocationArg(int $cardLocationArg): void
    {
        $this->cardLocationArg = $cardLocationArg;
    }

    // Business logic methods
    public function isInHand(): bool
    {
        return $this->cardLocation === 'player';
    }

    public function isInDeck(): bool
    {
        return $this->cardLocation === 'deck';
    }

    public function isOnTable(): bool
    {
        return $this->cardLocation === 'table';
    }

    public function isDiscarded(): bool
    {
        return $this->cardLocation === 'discard';
    }

    public function moveToPlayer(int $playerId): void
    {
        $this->cardLocation = 'player';
        $this->cardLocationArg = $playerId;
    }

    public function moveToTable(): void
    {
        $this->cardLocation = 'table';
        $this->cardLocationArg = 0;
    }

    public function moveToDeck(): void
    {
        $this->cardLocation = 'deck';
        $this->cardLocationArg = 0;
    }

    public function discard(): void
    {
        $this->cardLocation = 'discard';
        $this->cardLocationArg = 0;
    }

    // Create from array (for compatibility)
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

    // Convert to array (for compatibility)
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
