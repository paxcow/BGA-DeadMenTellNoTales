<?php

namespace Bga\Games\DeadMenPax\DB\Models;

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
    private int $tokenLocationArg = 0;

    #[dbColumn('token_state')]
    private int $tokenState = 0;

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
    public function getTokenLocationArg(): int
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
    public function setTokenLocationArg(int $tokenLocationArg): void
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
        $model->tokenLocationArg = (int) $data['token_location_arg'];
        $model->tokenState = (int) $data['token_state'];
        
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
        ];
    }
}
