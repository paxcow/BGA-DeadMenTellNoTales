<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\DeadMenPax\Game;
use BgaUserException;

class ItemSwapChoice extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 10,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must choose new item when their item was taken'),
            descriptionMyTurn: clienttranslate('${you} must choose new item when your item was taken'),
            transitions: [
                'next' => TakeActions::class,
            ]
        );
    }

    public function getArgs(int $activePlayerId): array
    {
        $availableItems = [];
        
        // Get all items available on the table
        $tableItems = $this->game->getItemManager()->getItemsOnTable();
        foreach ($tableItems as $item) {
            $availableItems[] = [
                'itemId' => $item['id'],
                'itemName' => $item['name']
            ];
        }
        
        // Get the player who took the original player's item
        $originalActivePlayerId = $this->game->getOriginalActivePlayerFromItemSwap();
        
        return [
            'playerId' => $activePlayerId,
            'availableItems' => $availableItems,
            'originalActivePlayerId' => $originalActivePlayerId
        ];
    }

    public function onEnteringState(int $activePlayerId): void
    {
        // Initialize item swap choice state
        $this->game->initializeItemSwapChoice($activePlayerId);
    }

    #[PossibleAction]
    public function chooseNewItem(int $itemId): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        
        // Validate item is available on table
        $availableItems = $this->game->getItemManager()->getItemsOnTable();
        $itemAvailable = false;
        
        foreach ($availableItems as $item) {
            if ($item['id'] == $itemId) {
                $itemAvailable = true;
                break;
            }
        }
        
        if (!$itemAvailable) {
            throw new \BgaUserException("Item not available for selection");
        }
        
        // Assign new item to player
        $this->game->getItemManager()->assignItemToPlayer($playerId, $itemId);
        
        // Remove item from table
        $this->game->getItemManager()->removeItemFromTable($itemId);
        
        // Reactivate original active player
        $originalActivePlayerId = $this->game->getOriginalActivePlayerFromItemSwap();
        $this->game->reactivateOriginalActivePlayer($originalActivePlayerId);
        
        // Notify item choice
        $this->game->notify->all("itemChosen", clienttranslate('${player_name} chooses new item'), [
            "playerId" => $playerId,
            "itemId" => $itemId
        ]);
        
        return "next";
    }
}
