<?php

declare(strict_types=1);

namespace Bga\Games\deadmenpax\Actions;

abstract class ActionCommand
{
    public ?int $action_id = null;
    protected string $player_id;

    /**
     * Constructor.
     *
     * @param string $player_id The ID of the player performing the action.
     */
    public function __construct(string $player_id)
    {
        $this->player_id = $player_id;
    }

    /**
     * Gets the player ID.
     *
     * @return string The player ID.
     */
    public function getPlayerId(): string
    {
        return $this->player_id;
    }

    /**
     * Set handlers for this action.
     *
     * @param array<string, mixed> $handlers
     */
    public function setHandlers(array $handlers): void
    {
        foreach ($handlers as $propertyName => $propertyValue) {
            if (property_exists($this, $propertyName)) {
                $this->$propertyName = $propertyValue;
            }
        }
    }

    /**
     * Executes the action.
     *
     * @param ActionNotifier $notifier The notifier to use for sending notifications.
     */
    abstract public function do(ActionNotifier $notifier): void;

    /**
     * Reloads the action.
     *
     * @param ActionNotifier $notifier The notifier to use for sending notifications.
     */
    abstract public function reload(ActionNotifier $notifier): void;

    /**
     * Reverts the action.
     *
     * @param ActionNotifier $notifier The notifier to use for sending notifications.
     */
    abstract public function undo(ActionNotifier $notifier): void;

    /**
     * Commits the action.
     *
     * @param ActionNotifier $notifier The notifier to use for sending notifications.
     */
    abstract public function commit(ActionNotifier $notifier): void;
}
