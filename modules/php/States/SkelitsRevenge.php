<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\DeadMenPax\Game;

class SkelitsRevenge extends GameState
{
    /**
     * Constructor.
     *
     * @param Game $game The game instance.
     */
    function __construct(
        protected Game $game,
    ) {
        parent::__construct($game,
            id: 12,
            type: StateType::GAME,
            name: "skelitsRevenge",
            description: "Skelit's Revenge strikes back...",
            action: "stSkelitsRevenge",
            transitions: [
                "nextPlayer" => PlayerTurn::class,
                "gameEnd" => GameEnd::class
            ],
        );
    }

    /**
     * Called when entering the game state.
     *
     * @param int $activePlayerId The active player ID.
     * @return string
     */
    function onEnteringState(int $activePlayerId): string {
        // Draw and resolve Skelit's Revenge card
        $card = $this->game->drawSkelitsRevengeCard();
        
        // Resolve fire effects
        $this->game->resolveFireEffects($card);
        
        // Resolve deckhand effects
        $this->game->resolveDeckhandEffects($card);
        
        // Resolve skeleton crew movement
        $this->game->resolveSkeletonCrewMovement($card);
        
        // Check for explosions and game end conditions
        if ($this->game->checkExplosions()) {
            return GameEnd::class;
        }
        
        // Check if game should end
        if ($this->game->checkGameEnd()) {
            return GameEnd::class;
        }
        
        // Move to next player
        $this->game->activeNextPlayer();
        return PlayerTurn::class;
    }
}
