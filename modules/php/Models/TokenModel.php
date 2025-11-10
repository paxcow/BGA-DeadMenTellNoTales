<?php

namespace Bga\Games\DeadMenPax\Models;

use Bga\Games\DeadMenPax\DB\dbColumn;
use Bga\Games\DeadMenPax\DB\dbKey;

/**
 * Token model representing the token database table
 */
class TokenModel
{
    #[dbKey('token_id')]
    private string $tokenId;

    #[dbColumn('token_type')]
    private string $tokenType;

    #[dbColumn('token_location')]
    private string $tokenLocation;

    #[dbColumn('token_location_arg')]
    private string $tokenLocationArg = '0';

    #[dbColumn('token_state')]
    private int $tokenState = 0;

    #[dbColumn('token_order')]
    private int $tokenOrder = 0;

    #[dbColumn('front_type')]
    private string $frontType = '';

    #[dbColumn('front_value')]
    private ?int $frontValue = null;

    #[dbColumn('back_type')]
    private ?string $backType = null;

    #[dbColumn('back_value')]
    private ?int $backValue = null;

    /**
     * Gets the token ID.
     *
     * @return string
     */
    public function getTokenId(): string
    {
        return $this->tokenId;
    }

    /**
     * Gets the token type.
     *
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * Gets the token location.
     *
     * @return string
     */
    public function getTokenLocation(): string
    {
        return $this->tokenLocation;
    }

    /**
     * Gets the token location argument.
     *
     * @return int
     */
    public function getTokenLocationArg(): string
    {
        return $this->tokenLocationArg;
    }

    /**
     * Gets the token state.
     *
     * @return int
     */
    public function getTokenState(): int
    {
        return $this->tokenState;
    }

    /**
     * Sets the token ID.
     *
     * @param string $tokenId
     */
    public function setTokenId(string $tokenId): void
    {
        $this->tokenId = $tokenId;
    }

    /**
     * Sets the token type.
     *
     * @param string $tokenType
     */
    public function setTokenType(string $tokenType): void
    {
        $this->tokenType = $tokenType;
    }

    /**
     * Sets the token location.
     *
     * @param string $tokenLocation
     */
    public function setTokenLocation(string $tokenLocation): void
    {
        $this->tokenLocation = $tokenLocation;
    }

    /**
     * Sets the token location argument.
     *
     * @param int $tokenLocationArg
     */
    public function setTokenLocationArg(string $tokenLocationArg): void
    {
        $this->tokenLocationArg = $tokenLocationArg;
    }

    /**
     * Sets the token state.
     *
     * @param int $tokenState
     */
    public function setTokenState(int $tokenState): void
    {
        $this->tokenState = $tokenState;
    }

    public function getTokenOrder(): int
    {
        return $this->tokenOrder;
    }

    public function setTokenOrder(int $tokenOrder): void
    {
        $this->tokenOrder = $tokenOrder;
    }

    public function getFrontType(): string
    {
        return $this->frontType;
    }

    public function setFrontType(string $frontType): void
    {
        $this->frontType = $frontType;
    }

    public function getFrontValue(): ?int
    {
        return $this->frontValue;
    }

    public function setFrontValue(?int $frontValue): void
    {
        $this->frontValue = $frontValue;
    }

    public function getBackType(): ?string
    {
        return $this->backType;
    }

    public function setBackType(?string $backType): void
    {
        $this->backType = $backType;
    }

    public function getBackValue(): ?int
    {
        return $this->backValue;
    }

    public function setBackValue(?int $backValue): void
    {
        $this->backValue = $backValue;
    }

    /**
     * Checks if the token is in a room.
     *
     * @return bool
     */
    public function isInRoom(): bool
    {
        return str_contains($this->tokenLocation, 'room_');
    }

    /**
     * Checks if the token is on the board.
     *
     * @return bool
     */
    public function isOnBoard(): bool
    {
        return $this->tokenLocation === 'board';
    }

    /**
     * Checks if the token is in the supply.
     *
     * @return bool
     */
    public function isInSupply(): bool
    {
        return $this->tokenLocation === 'supply';
    }

    /**
     * Moves the token to a room.
     *
     * @param int $x
     * @param int $y
     */
    public function moveToRoom(int $x, int $y): void
    {
        $this->tokenLocation = "room_{$x}_{$y}";
        $this->tokenLocationArg = 0;
    }

    /**
     * Moves the token to the supply.
     */
    public function moveToSupply(): void
    {
        $this->tokenLocation = 'supply';
        $this->tokenLocationArg = 0;
    }

    /**
     * Gets the room coordinates if the token is in a room.
     *
     * @return array|null
     */
    public function getRoomCoordinates(): ?array
    {
        if (preg_match('/^room_(\d+)_(\d+)$/', $this->tokenLocation, $matches)) {
            return [(int)$matches[1], (int)$matches[2]];
        }
        return null;
    }

    /**
     * Creates a TokenModel from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $model = new self();
        $model->tokenId = $data['token_id'];
        $model->tokenType = $data['token_type'];
        $model->tokenLocation = $data['token_location'];
        $model->tokenLocationArg = (string) $data['token_location_arg'];
        $model->tokenState = (int) $data['token_state'];
        $model->frontType = $data['front_type'] ?? '';
        $model->frontValue = isset($data['front_value']) ? (int)$data['front_value'] : null;
        $model->backType = $data['back_type'] ?? null;
        $model->backValue = isset($data['back_value']) ? (int)$data['back_value'] : null;
        $model->tokenOrder = isset($data['token_order']) ? (int) $data['token_order'] : 0;
        
        return $model;
    }

    /**
     * Converts the TokenModel to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'token_id' => $this->tokenId,
            'token_type' => $this->tokenType,
            'token_location' => $this->tokenLocation,
            'token_location_arg' => $this->tokenLocationArg,
            'token_state' => $this->tokenState,
            'token_order' => $this->tokenOrder,
            'front_type' => $this->frontType,
            'front_value' => $this->frontValue,
            'back_type' => $this->backType,
            'back_value' => $this->backValue,
        ];
    }
}
