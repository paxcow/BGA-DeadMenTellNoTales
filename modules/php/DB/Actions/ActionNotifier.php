<?php

declare(strict_types=1);

namespace Bga\Games\deadmenpax\Actions;

use Bga\GameFramework\Table;

class ActionNotifier
{
    private static Table $game;
    private ?int $player_id;

    /**
     * Sets the game instance.
     *
     * @param Table $game The game instance.
     */
    public static function setGame(Table $game): void
    {
        self::$game = $game;
    }

    /**
     * Constructor.
     *
     * @param int|null $player_id The ID of the player to notify.
     */
    public function __construct(?int $player_id = null)
    {
        $this->player_id = $player_id;
    }

    /**
     * Notifies the current player with a private message and all other players with a public message.
     *
     * @param string $notifType The notification type.
     * @param array|string $notifLog The notification log message.
     * @param array $notifArgs The notification arguments.
     */
    public function notifyPlayerAndOthers(string $notifType, array|string $notifLog, array $notifArgs): void
    {
        if (is_array($notifLog)) {
            $notifLogPublic = $notifLog['public'];
            $notifLogPrivate = $notifLog['private'];
        } else {
            $notifLogPublic = $notifLog;
            $notifLogPrivate = $notifLog;
        }
        if ($this->player_id !== null) {
            $this->notifyCurrentPlayer("{$notifType}_private", $notifLogPrivate, $notifArgs);
        }
        $this->notifyAllPlayers($notifType, $notifLogPublic, $notifArgs);
    }

    /**
     * Notifies all players.
     *
     * @param string $notifType The notification type.
     * @param string $notifLog The notification log message.
     * @param array $notifArgs The notification arguments.
     */
    public function notifyAll(string $notifType, string $notifLog, array $notifArgs): void
    {
        $this->notifyAllPlayers($notifType, $notifLog, $notifArgs);
    }

    /**
     * Notifies all players without a log message.
     *
     * @param string $notifType The notification type.
     * @param array $notifArgs The notification arguments.
     */
    public function notifyAllNoMessage(string $notifType, array $notifArgs): void
    {
        $this->notifyAllPlayers($notifType, '', $notifArgs);
    }

    /**
     * Notifies the current player if a player ID is set, otherwise notifies all players.
     *
     * @param string $notifType The notification type.
     * @param string $notifLog The notification log message.
     * @param array $notifArgs The notification arguments.
     */
    public function notify(string $notifType, string $notifLog, array $notifArgs): void
    {
        if ($this->player_id !== null) {
            $this->notifyCurrentPlayer($notifType, $notifLog, $notifArgs);
        } else {
            $this->notifyAllPlayers($notifType, $notifLog, $notifArgs);
        }
    }

    /**
     * Notifies the current player without a log message if a player ID is set, otherwise notifies all players.
     *
     * @param string $notifType The notification type.
     * @param array $notifArgs The notification arguments.
     */
    public function notifyNoMessage(string $notifType, array $notifArgs): void
    {
        if ($this->player_id !== null) {
            $this->notifyCurrentPlayer($notifType, '', $notifArgs);
        } else {
            $this->notifyAllPlayers($notifType, '', $notifArgs);
        }
    }

    /**
     * Sends a notification to the current player.
     *
     * @param string $notifType The notification type.
     * @param string $notifLog The notification log message.
     * @param array $notifArgs The notification arguments.
     */
    protected function notifyCurrentPlayer(string $notifType, string $notifLog, array $notifArgs): void
    {
        self::$game->notifyPlayer(
            $this->player_id,
            $notifType,
            $notifLog,
            $this->processNotifArgs($notifArgs)
        );
    }

    /**
     * Sends a notification to all players.
     *
     * @param string $notifType The notification type.
     * @param string $notifLog The notification log message.
     * @param array $notifArgs The notification arguments.
     */
    protected function notifyAllPlayers(string $notifType, string $notifLog, array $notifArgs): void
    {
        self::$game->notifyAllPlayers(
            $notifType,
            $notifLog,
            $this->processNotifArgs($notifArgs)
        );
    }

    /**
     * Processes notification arguments, adding player information.
     *
     * @param array $notifArgs The notification arguments.
     * @return array The processed notification arguments.
     */
    protected function processNotifArgs(array $notifArgs): array
    {
        $info = self::$game->loadPlayersBasicInfos();
        $playerName = '';
        if ($this->player_id !== null && isset($info[$this->player_id]['player_name'])) {
            $playerName = $info[$this->player_id]['player_name'];
        }

        return array_merge(
            [
                'playerId'   => $this->player_id,
                'player_id'  => $this->player_id,
                'playerName' => $playerName,
                'player_name'=> $playerName,
            ],
            $notifArgs
        );
    }
}
