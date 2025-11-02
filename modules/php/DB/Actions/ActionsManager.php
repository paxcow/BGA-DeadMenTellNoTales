<?php

declare(strict_types=1);

namespace Bga\Games\deadmenpax\Actions;

use Bga\Games\deadmenpax\DB\DBManager;
use Bga\Games\DeadMenPax\DB\Models\ActionModel;
use Bga\GameFramework\Table;

class ActionManager
{
    private DBManager $db;
    private Table $game;

    /**
     * Constructor.
     *
     * @param Table $game The game instance.
     */
    public function __construct(Table $game)
    {
        $this->game = $game;
        $this->db = new DBManager('action_log', ActionModel::class, $game);
    }

    /**
     * Retrieve all actions, optionally filtered by player.
     *
     * @param string|null $playerId The player ID to filter by.
     * @return ActionCommand[]
     */
    public function getAllActions(?string $playerId = null): array
    {
        $actions = [];
        $rows = $this->db->getAllRows();
        foreach ($rows as $row) {
            $actionModel = ActionModel::fromArray($row);
            $action = $actionModel->getAction();
            if (!$action) {
                continue;
            }
            if ($playerId === null || $action->getPlayerId() === $playerId) {
                $actions[$actionModel->getActionId()] = $action;
            }
        }
        return $actions;
    }

    /**
     * Reload all actions into temporary state.
     *
     * @param string|null $playerId The player ID to filter by.
     * @param array<string,mixed> $handlers Handlers to set on the actions.
     */
    public function reloadAllActions(?string $playerId = null, array $handlers = []): void
    {
        $notifier = new ActionNotifier($playerId);
        foreach ($this->getAllActions($playerId) as $action) {
            $action->setHandlers($handlers);
            $action->reload($notifier);
        }
    }

    /**
     * Commit all actions to persistent state, then clear the log.
     *
     * @param string|null $playerId The player ID to filter by.
     * @param array<string,mixed> $handlers Handlers to set on the actions.
     */
    public function commitAllActions(?string $playerId = null, array $handlers = []): void
    {
        $notifier = new ActionNotifier($playerId);
        foreach ($this->getAllActions($playerId) as $action) {
            $action->setHandlers($handlers);
            $action->commit($notifier);
        }

        // clear after successful commit
        $this->clearAll($playerId);
    }

    /**
     * Retrieve a single action by its database key.
     *
     * @param string $keyValue The database key value.
     * @return ActionCommand|null
     */
    public function getActionByKey(string $keyValue): ?ActionCommand
    {
        $row = $this->db->getRow($keyValue);
        if (!$row) {
            return null;
        }
        $actionModel = ActionModel::fromArray($row);
        return $actionModel->getAction();
    }

    /**
     * Get the last action for a player or global.
     *
     * @param string|null $playerId The player ID to filter by.
     * @return ActionCommand|null
     */
    public function getLastAction(?string $playerId = null): ?ActionCommand
    {
        if ($playerId === null) {
            $row = $this->db->getLastRow();
            if (!$row) {
                return null;
            }
            return (ActionModel::fromArray($row))->getAction();
        }
        $actions = $this->getAllActions($playerId);
        return count($actions) ? end($actions) : null;
    }

    /**
     * Save a new action to the log.
     *
     * @param ActionCommand $action The action to save.
     */
    public function saveAction(ActionCommand $action): void
    {
        $actionModel = new ActionModel();
        $actionModel->setAction($action);
        $this->db->saveObjectToDB($actionModel);
    }

    /**
     * Remove an action by its ID.
     *
     * @param string $actionId The ID of the action to remove.
     */
    public function removeAction(string $actionId): void
    {
        $this->db->deleteObjectFromDb($actionId);
    }

    /**
     * Clear all actions, optionally for a specific player.
     *
     * @param string|null $playerId The player ID to filter by.
     */
    public function clearAll(?string $playerId = null): void
    {
        if ($playerId === null) {
            $this->db->clearAll();
        } else {
            foreach ($this->getAllActions($playerId) as $action) {
                if ($action instanceof ActionCommand && $action->action_id !== null) {
                    $this->db->deleteObjectFromDb((string)$action->action_id);
                }
            }
        }
    }

    /**
     * Serialize an action to JSON.
     *
     * @param ActionCommand $action The action to serialize.
     * @return string The JSON representation of the action.
     */
    public static function serializeAction(ActionCommand $action): string
    {
        $array = self::serializeObjectToArray($action);
        return json_encode($array);
    }

    /**
     * Recursively convert an object graph to array.
     *
     * @param mixed $value The value to serialize.
     * @param array<int,bool> $seen An array of seen object IDs to prevent recursion.
     * @return mixed
     */
    private static function serializeObjectToArray($value, array &$seen = [])
    {
        if (!is_object($value)) {
            return $value;
        }

        $id = spl_object_id($value);
        if (isset($seen[$id])) {
            return ['__recursive' => get_class($value)];
        }
        $seen[$id] = true;

        $ref = new \ReflectionClass($value);
        $data = ['__class' => $ref->getName()];
        foreach ($ref->getProperties() as $prop) {
            $prop->setAccessible(true);
            $data[$prop->getName()] = self::serializeObjectToArray($prop->getValue($value), $seen);
        }
        return $data;
    }

    /**
     * Rebuild action object from JSON string.
     *
     * @param string $json The JSON string.
     * @return ActionCommand
     */
    public static function rebuildAction(string $json): ActionCommand
    {
        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['__class'])) {
            throw new \BgaSystemException("Invalid action JSON");
        }
        /** @var ActionCommand $action */
        $action = self::deserializeArrayToObject($data);
        return $action;
    }

    /**
     * Recursive helper to convert array back to objects.
     *
     * @param mixed $data The data to deserialize.
     * @return mixed
     */
    private static function deserializeArrayToObject($data)
    {
        if (!is_array($data) || !isset($data['__class'])) {
            return $data;
        }

        $class = $data['__class'];
        if (!class_exists($class)) {
            throw new \BgaSystemException("Class $class not found during action rebuild");
        }
        $ref = new \ReflectionClass($class);
        /** @var ActionCommand $object */
        $object = $ref->newInstanceWithoutConstructor();
        foreach ($data as $key => $value) {
            if ($key === '__class') {
                continue;
            }
            $prop = $ref->getProperty($key);
            $prop->setAccessible(true);
            $prop->setValue($object, self::deserializeArrayToObject($value));
        }
        return $object;
    }
}
