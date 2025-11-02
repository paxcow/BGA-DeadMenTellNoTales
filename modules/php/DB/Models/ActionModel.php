<?php

declare(strict_types=1);

namespace Bga\Games\DeadMenPax\DB\Models;

use Bga\Games\deadmenpax\Actions\ActionCommand;
use Bga\Games\deadmenpax\Actions\ActionManager;
use Bga\Games\DeadMenPax\DB\dbColumn;
use Bga\Games\DeadMenPax\DB\dbKey;
use BgaSystemException;

/**
 * Action model representing the action database table
 */
class ActionModel
{
    #[dbKey(name: "action_id")]
    private int $actionId;

    #[dbColumn(name: "action_json")]
    private string $actionEncoded;

    private const MAX_ACTION_JSON_LENGTH = 65535;

    /**
     * Gets the action ID.
     *
     * @return int
     */
    public function getActionId(): int
    {
        return $this->actionId;
    }

    /**
     * Gets the encoded action.
     *
     * @return string
     */
    public function getActionEncoded(): string
    {
        return $this->actionEncoded;
    }

    /**
     * Sets the action ID.
     *
     * @param int $actionId
     */
    public function setActionId(int $actionId): void
    {
        $this->actionId = $actionId;
    }

    /**
     * Sets the encoded action.
     *
     * @param string $actionEncoded
     */
    public function setActionEncoded(string $actionEncoded): void
    {
        $this->actionEncoded = $actionEncoded;
    }

    /**
     * Rebuild the ActionCommand object from stored JSON.
     *
     * @return ActionCommand
     * @throws BgaSystemException
     */
    public function getAction(): ActionCommand
    {
        if ($this->actionEncoded === '') {
            throw new BgaSystemException('No action JSON to rebuild');
        }
        $action = ActionManager::rebuildAction($this->actionEncoded);
        $action->action_id = $this->actionId;
        return $action;
    }

    /**
     * Encode and store an ActionCommand.
     *
     * @param ActionCommand $action The action to encode.
     * @throws BgaSystemException
     */
    public function setAction(ActionCommand $action): void
    {
        $this->actionEncoded = ActionManager::serializeAction($action);
        if (strlen($this->actionEncoded) > self::MAX_ACTION_JSON_LENGTH) {
            throw new BgaSystemException('Serialized action JSON is too long');
        }
    }

    /**
     * Creates a ActionModel from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $model = new self();
        $model->actionId = (int)$data['action_id'];
        $model->actionEncoded = (string)$data['action_json'];

        return $model;
    }

    /**
     * Converts the ActionModel to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'action_id' => $this->actionId,
            'action_json' => $this->actionEncoded,
        ];
    }
}
