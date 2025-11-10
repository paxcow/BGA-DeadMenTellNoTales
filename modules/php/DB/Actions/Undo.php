<?php

declare(strict_types=1);

namespace Bga\Games\DeadMenPax\DB\Actions;

use BgaSystemException;
use Bga\Games\DeadMenPax\NotificationManager;

/**
 * Trait providing undo functionality by replaying and reversing actions.
 */
trait Undo
{
    /**
     * Undo previous actions for the current player.
     *
     * @param bool        $unpass           Reactivate the player if they passed.
     * @param int         $steps            Number of actions to undo.
     * @param string|null $changeStateAfter State to transition to after undo.
     *
     * @return void
     * @throws BgaSystemException
     */
    public function undo(bool $unpass = false, int $steps = 1, ?string $changeStateAfter = null): void
    {
        // The BGA gamestate methods expect integer player IDs
        $playerIdInt = (int)$this->getCurrentPlayerId();
        // The ActionManager operates with string IDs
        $playerId = (string)$playerIdInt;

        if ($unpass) {
            $this->gamestate->setPlayersMultiactive([$playerIdInt], null);
        }

        if (!$this->gamestate->isPlayerActive($playerIdInt)) {
            return;
        }

        if (!isset($this->actionManager) || !$this->actionManager instanceof ActionManager) {
            throw new BgaSystemException('ActionManager not set on game instance');
        }

        // Retrieve handlers for actions
        $handlers = $this->getActionHandlers();

        // Determine how many actions can be undone
        $actions = $this->actionManager->getAllActions($playerId);
        $undoCount = min($steps, count($actions));

        // Reload actions into temporary state
        $this->actionManager->reloadAllActions($playerId, $handlers);

        // Undo actions in reverse order
        for ($i = 0; $i < $undoCount; $i++) {
            $lastAction = $this->actionManager->getLastAction($playerId);
            if ($lastAction) {
                $this->trace("UNDO " . get_class($lastAction));
                $lastAction->setHandlers($handlers);
                $notifier = new NotificationManager($this);
                $lastAction->undo($notifier);
                $this->actionManager->removeAction((string)$lastAction->action_id);
            }
        }

        // Change game state if specified
        if ($changeStateAfter !== null) {
            $this->gamestate->nextState($changeStateAfter);
        }
    }

    /**
     * Provide handlers for action methods.
     *
     * @return array<string,mixed>
     */
    abstract protected function getActionHandlers(): array;
}
