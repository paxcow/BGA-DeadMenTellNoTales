<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\DeadMenPax\Game;
use BgaUserException;

class Battle extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game,
            id: 5,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must fight a Skeleton Crew or Guard'),
            descriptionMyTurn: clienttranslate('${you} must fight a Skeleton Crew or Guard'),
            transitions: [
                'next'            => TakeActions::class,
                'resolveBattles'  => ResolveBattles::class,
                'playerRetreat'   => PlayerRetreat::class,
                'gameEnd'         => GameEnd::class,
            ]
        );
    }

    public function getArgs(int $activePlayerId): array
    {
        $player = $this->game->requirePirate($activePlayerId);
        $currentEnemy = $this->game->getCurrentBattleEnemy($activePlayerId);
        if ($currentEnemy === null) {
            throw new \BgaUserException("No enemy available for battle");
        }
        
        return [
            'playerId' => $activePlayerId,
            'enemyId' => $currentEnemy['id'],
            'enemyType' => $currentEnemy['type'],
            'enemyStrength' => $currentEnemy['strength'],
            'playerBattleTrack' => $player->battleStrength,
            'playerItemModifier' => $this->game->getItemManager()->getItemBattleModifier($activePlayerId),
            'canRetreat' => $currentEnemy['type'] === 'guard',
            'mustFightAgain' => false
        ];
    }

    public function onEnteringState(int $activePlayerId): void
    {
        // Initialize current battle enemy if not set
        $this->game->initializeCurrentBattle($activePlayerId);
    }

    #[PossibleAction]
    public function fight(bool $useBattleTrack, bool $useItem): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        
        // Roll battle die (1-6)
        $roll = rand(1, 6);
        
        // Calculate total strength
        $player = $this->game->requirePirate($playerId);
        $currentEnemy = $this->game->getCurrentBattleEnemy($playerId);
        
        $totalStrength = $roll;
        $modifiers = ['battleTrack' => 0, 'item' => 0];
        
        if ($useBattleTrack) {
            $modifiers['battleTrack'] = $player->battleStrength;
            $totalStrength += $player->battleStrength;
        }
        
        if ($useItem) {
            $itemModifier = $this->game->getItemManager()->getItemBattleModifier($playerId);
            $modifiers['item'] = $itemModifier;
            $totalStrength += $itemModifier;
        }
        
        // Compare to enemy strength
        $outcome = $totalStrength >= $currentEnemy['strength'] ? 'win' : 'lose';
        $revealedTokenId = null;
        $retreatRoll = null;
        $retreatOutcome = null;
        
        if ($outcome === 'win') {
            // Win: flip enemy token
            $this->game->getTokenManager()->flipEnemyToken($currentEnemy['id']);
            $revealedTokenId = $currentEnemy['id'];
            
            // Check if more enemies in room
            $remainingEnemies = $this->game->getTokenManager()->getEnemiesInRoom($currentEnemy['room_id']);
            if (!empty($remainingEnemies)) {
                return 'resolveBattles'; // More battles to fight
            }
        } else {
            // Lose: apply fatigue and determine retreat options
            $fatigueGained = 1;
            $this->game->getPirateManager()->adjustFatigue($playerId, $fatigueGained, 'battle_loss');
            
            if ($currentEnemy['type'] === 'guard' && $this->game->canRetreatFromGuardBattle($playerId)) {
                $retreatRoll = rand(1, 6);
                $retreatOutcome = $retreatRoll >= 4 ? 'success' : 'failure';
            }
        }
        
        // Reset battle track if used
        if ($useBattleTrack) {
            $player->battleStrength = 0;
            $this->game->getPirateManager()->persistPirate($player);
        }
        
        // Notify battle result
        $this->game->notify->all("battleUpdate", clienttranslate('${player_name} fights ${enemy_type}'), [
            "playerId" => $playerId,
            "enemyId" => $currentEnemy['id'],
            "outcome" => $outcome,
            "roll" => $roll,
            "modifiers" => $modifiers,
            "totalStrength" => $totalStrength,
            "enemyStrength" => $currentEnemy['strength'],
            "fatigueGained" => $outcome === 'lose' ? $fatigueGained : 0,
            "revealedTokenId" => $revealedTokenId,
            "retreatRoll" => $retreatRoll,
            "retreatOutcome" => $retreatOutcome
        ]);
        
        // Return appropriate next state
        if ($outcome === 'lose' && $retreatOutcome === 'success' && $currentEnemy['type'] === 'guard') {
            return 'playerRetreat';
        } elseif ($outcome === 'lose') {
            return 'next';
        } else {
            return 'next';
        }
    }

    #[PossibleAction]
    public function retreat(): string
    {
        $playerId = (int)$this->game->getActivePlayerId();
        $currentEnemy = $this->game->getCurrentBattleEnemy($playerId);
        
        // Validate that retreat is available (only vs Guard)
        if ($currentEnemy['type'] !== 'guard') {
            throw new \BgaUserException("Cannot retreat from this enemy");
        }
        
        return 'playerRetreat';
    }
}
