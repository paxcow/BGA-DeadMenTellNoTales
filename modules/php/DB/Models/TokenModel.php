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

    // Getters
    public function getTokenId(): string
    {
        return $this->tokenId;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getTokenLocation(): string
    {
        return $this->tokenLocation;
    }

    public function getTokenLocationArg(): int
    {
        return $this->tokenLocationArg;
    }

    public function getTokenState(): int
    {
        return $this->tokenState;
    }

    // Setters
    public function setTokenId(string $tokenId): void
    {
        $this->tokenId = $tokenId;
    }

    public function setTokenType(string $tokenType): void
    {
        $this->tokenType = $tokenType;
    }

    public function setTokenLocation(string $tokenLocation): void
    {
        $this->tokenLocation = $tokenLocation;
    }

    public function setTokenLocationArg(int $tokenLocationArg): void
    {
        $this->tokenLocationArg = $tokenLocationArg;
    }

    public function setTokenState(int $tokenState): void
    {
        $this->tokenState = $tokenState;
    }

    // Business logic methods
    public function isInRoom(): bool
    {
        return str_contains($this->tokenLocation, 'room_');
    }

    public function isOnBoard(): bool
    {
        return $this->tokenLocation === 'board';
    }

    public function isInSupply(): bool
    {
        return $this->tokenLocation === 'supply';
    }

    public function moveToRoom(int $x, int $y): void
    {
        $this->tokenLocation = "room_{$x}_{$y}";
        $this->tokenLocationArg = 0;
    }

    public function moveToSupply(): void
    {
        $this->tokenLocation = 'supply';
        $this->tokenLocationArg = 0;
    }

    public function getRoomCoordinates(): ?array
    {
        if (preg_match('/^room_(\d+)_(\d+)$/', $this->tokenLocation, $matches)) {
            return [(int)$matches[1], (int)$matches[2]];
        }
        return null;
    }

    // Create from array (for compatibility)
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

    // Convert to array (for compatibility)
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
