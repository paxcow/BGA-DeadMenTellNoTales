<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\DeadMenPax\Game;
use BgaUserException;

class SearchShip extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 3,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must draw and place a room tile'),
            descriptionMyTurn: clienttranslate('${you} must draw and place a room tile'),
            transitions: [
                "next" => TakeActions::class,
                "gameEnd" => GameEnd::class
            ]
        );
    }

    public function getArgs(int $activePlayerId): array
    {
        // Get the next tile to place
        $tileId = $this->game->getNextTileToPlace();
        $tileData = $this->game->getTileData($tileId);
        
        return [
            'tileId' => $tileId,
            'tileDoors' => $tileData['doors'],
            'tileColor' => $tileData['color'],
            'tilePips' => $tileData['pips'],
            'hasPowderKeg' => $tileData['has_powder_keg'] ?? false,
            'hasTrapdoor' => $tileData['has_trapdoor'] ?? false,
            'validPlacements' => $this->game->getValidPlacements($tileId)
        ];
    }

    public function onEnteringState(int $activePlayerId): void
    {
        // Draw the next room tile for placement
        $this->game->drawNextRoomTile();
    }

    #[PossibleAction]
    public function actPlaceTile(int $tileId, int $x, int $y, int $orientation): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        
        // Validate the placement
        $validPlacements = $this->game->getValidPlacements($tileId);
        $isValidPlacement = false;
        
        foreach ($validPlacements as $placement) {
            if ($placement['x'] == $x && $placement['y'] == $y && $placement['orientation'] == $orientation) {
                $isValidPlacement = true;
                break;
            }
        }
        
        if (!$isValidPlacement) {
            throw new \BgaUserException("Invalid tile placement");
        }
        
        // Place the tile using the game method
        $this->game->actPlaceTile($tileId, $x, $y, $orientation);
        
        // Transition to next state
        return "next";
    }
}
