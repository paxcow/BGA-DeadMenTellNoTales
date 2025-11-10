<?php

declare(strict_types=1);

namespace Bga\Games\DeadMenPax\Managers;

use \BgaSystemException;

/**
 * NotificationManager
 * 
 * Extends BGA's notification system with additional functionality:
 * - Silent notifications (no message required)
 * - Dual notifications (current player + others)
 * - Player filtering and targeting
 * - Batch operations
 * - Enhanced message handling
 */
class NotificationManager
{
    private \Bga\GameFramework\Table $game;
    private $notify;
    private array $playerCache = [];

    public function __construct(\Bga\GameFramework\Table $game)
    {
        $this->game = $game;
        $this->notify = $game->notify;
    }

    /**
     * Send a silent notification (no message required)
     * Useful for state changes or data updates where no user-facing message is needed
     */
    public function silent(string $type, array $args = []): void
    {
        $this->notify->all($type, '', $args);
    }

    /**
     * Send a silent notification to a specific player
     */
    public function silentPlayer(int $playerId, string $type, array $args = []): void
    {
        $this->notify->player($playerId, $type, '', $args);
    }

    /**
     * Send the same notification to current player (with private message) and all others (with public message)
     * This replaces the need to send two separate notifications
     */
    public function notifyActiveAndOthers(
        string $type, 
        string $activePlayerMessage, 
        string $otherPlayersMessage, 
        array $activePlayerArgs = [], 
        array $otherPlayerArgs = []
    ): void {
        $activePlayerId = (int)$this->game->getActivePlayerId();
        
        $activePlayerMessage = clienttranslate($activePlayerMessage);
        
        $otherPlayersMessage = clienttranslate($otherPlayersMessage);
        
        // Get player names if needed
        if (isset($activePlayerArgs['player_id']) && !isset($activePlayerArgs['player_name'])) {
            $activePlayerArgs['player_name'] = $this->game->getPlayerNameById($activePlayerArgs['player_id']);
        }
        
        if (isset($otherPlayerArgs['player_id']) && !isset($otherPlayerArgs['player_name'])) {
            $otherPlayerArgs['player_name'] = $this->game->getPlayerNameById($otherPlayerArgs['player_id']);
        }
        
        // Send to active player
        $this->notify->player($activePlayerId, $type, $activePlayerMessage, $activePlayerArgs);
        
        // Send to all other players
        $this->notifyAllExcept([$activePlayerId], $type, $otherPlayersMessage, $otherPlayerArgs);
    }

    /**
     * Notify all players except specified ones
     */
    public function notifyAllExcept(array $excludedPlayerIds, string $type, string $message = '', array $args = []): void
    {
        $this->notify->all($type, clienttranslate($message), array_merge($args, ['excluded_players' => $excludedPlayerIds]));
    }

    /**
     * Notify all active players in a multi-active state
     */
    public function notifyActivePlayers(string $type, string $message = '', array $args = []): void
    {
        if (!$this->game->gamestate->isMultiactiveState()) {
            throw new BgaSystemException("Cannot notify active players: not in a multi-active state");
        }
        
        $activePlayerIds = $this->game->gamestate->getActivePlayerList();
        $this->notifyPlayers($activePlayerIds, $type, $message, $args);
    }

    /**
     * Notify all non-zombie players
     */
    public function notifyNonZombies(string $type, string $message = '', array $args = []): void
    {
        $nonZombieIds = $this->getNonZombiePlayerIds();
        $this->notifyPlayers($nonZombieIds, $type, $message, $args);
    }

    /**
     * Send notification to specific list of players
     */
    public function notifyPlayers(array $playerIds, string $type, string $message = '', array $args = []): void
    {
        $translatedMessage = clienttranslate($message);
        
        // Add player names if player_id is in args but player_name is missing
        if (isset($args['player_id']) && !isset($args['player_name']) && in_array($args['player_id'], $playerIds)) {
            $args['player_name'] = $this->game->getPlayerNameById($args['player_id']);
        }
        
        foreach ($playerIds as $playerId) {
            $this->notify->player($playerId, $type, $translatedMessage, $args);
        }
    }

    /**
     * Send a batch of notifications efficiently
     * Each notification should have: type, message, args, target (optional)
     */
    public function notifyBatch(array $notifications): void
    {
        foreach ($notifications as $notification) {
            $type = $notification['type'] ?? '';
            $message = $notification['message'] ?? '';
            $args = $notification['args'] ?? [];
            $target = $notification['target'] ?? 'all';
            
            if ($target === 'all') {
                $this->notify->all($type, $message, $args);
            } elseif ($target === 'current') {
                $activePlayerId = (int)$this->game->getActivePlayerId();
                $this->notify->player($activePlayerId, $type, $message, $args);
            } elseif (is_array($target)) {
                $this->notifyPlayers($target, $type, $message, $args);
            } elseif (is_int($target)) {
                $this->notify->player($target, $type, $message, $args);
            }
        }
    }

    /**
     * Send notification with automatic player name injection
     * If player_id is in args but player_name is missing, it will be automatically added
     */
    public function notifyWithPlayerName(string $type, string $message, array $args = []): void
    {
        if (isset($args['player_id']) && !isset($args['player_name'])) {
            $args['player_name'] = $this->game->getPlayerNameById($args['player_id']);
        }
        
        $this->notify->all($type, $message, $args);
    }

    /**
     * Send private notification to player with automatic name injection
     */
    public function notifyPlayerWithName(int $playerId, string $type, string $message, array $args = []): void
    {
        if (isset($args['player_id']) && !isset($args['player_name'])) {
            $args['player_name'] = $this->game->getPlayerNameById($args['player_id']);
        }
        
        $this->notify->player($playerId, $type, $message, $args);
    }

    /**
     * Enhanced notification with message formatting and automatic i18n
     * Automatically adds i18n flags for common translation patterns
     */
    public function notifyFormatted(string $type, string $message, array $args = []): void
    {
        $message = clienttranslate($message);
        
        // Auto-detect translation needs
        $i18nKeys = [];
        foreach ($args as $key => $value) {
            if (str_ends_with($key, '_name') || str_ends_with($key, '_title') || str_ends_with($key, '_desc')) {
                $i18nKeys[] = $key;
            }
        }
        
        if (!empty($i18nKeys)) {
            $args['i18n'] = array_merge($args['i18n'] ?? [], $i18nKeys);
        }
        
        $this->notify->all($type, $message, $args);
    }

    /**
     * Get list of non-zombie player IDs
     */
    private function getNonZombiePlayerIds(): array
    {
        $players = $this->game->loadPlayersBasicInfos();
        $nonZombieIds = [];
        
        foreach ($players as $playerId => $playerInfo) {
            if (!isset($playerInfo['player_zombie']) || $playerInfo['player_zombie'] != 1) {
                $nonZombieIds[] = (int)$playerId;
            }
        }
        
        return $nonZombieIds;
    }

    /**
     * Get cached player information
     */
    private function getPlayerInfo(int $playerId): ?array
    {
        if (!isset($this->playerCache[$playerId])) {
            $players = $this->game->loadPlayersBasicInfos();
            $this->playerCache[$playerId] = $players[$playerId] ?? null;
        }
        
        return $this->playerCache[$playerId];
    }

    /**
     * Clear internal cache (useful for game state changes)
     */
    public function clearCache(): void
    {
        $this->playerCache = [];
    }

    /**
     * Decorator pattern: Add custom notification decorator
     */
    public function addDecorator(callable $decorator): void
    {
        $this->notify->addDecorator($decorator);
    }
}
