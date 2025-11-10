<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax\Managers;

class StatsManager
{
    private Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Initialize all table and player statistics at game start.
     *
     * @param array $players Array of players keyed by playerId.
     */
    public function initGameStats(array $players): void
    {
        // Table stats
        $this->game->tableStats->init('turns_played', 0);
        $this->game->tableStats->init('treasures_looted', 0);
        $this->game->tableStats->init('rooms_exploded', 0);
        $this->game->tableStats->init('treasures_destroyed', 0);

        // Player stats
        foreach ($players as $playerId => $info) {
            $this->game->playerStats->init('turns_taken', 0);
            $this->game->playerStats->init('treasure_looted', 0);
            $this->game->playerStats->init('fires_fought', 0);
            $this->game->playerStats->init('battles_won', 0);
            $this->game->playerStats->init('battles_lost', 0);
            $this->game->playerStats->init('fatigue_gained', 0);
        }
    }

    /**
     * Convenience methods to increment stats.
     */
    public function incTurnPlayed(): void
    {
        $this->game->tableStats->inc('turns_played', 1);
    }

    public function incRoomExploded(): void
    {
        $this->game->tableStats->inc('rooms_exploded', 1);
    }
    
    public function incTreasuresDestroyed(): void
    {
        $this->game->tableStats->inc('treasures_destroyed', 1);
    }

    public function incTreasureLooted(): void
    {
        $this->game->tableStats->inc('treasures_looted', 1);
    }

    public function incPlayerTurns(int $playerId): void
    {
        $this->game->playerStats->inc('turns_taken', 1, $playerId);
    }

    public function incPlayerTreasure(int $playerId): void
    {
        $this->game->playerStats->inc('treasure_looted', 1, $playerId);
    }

    public function incPlayerFiresFought(int $playerId): void
    {
        $this->game->playerStats->inc('fires_fought', 1, $playerId);
    }

    public function incPlayerBattlesWon(int $playerId): void
    {
        $this->game->playerStats->inc('battles_won', 1, $playerId);
    }

    public function incPlayerBattlesLost(int $playerId): void
    {
        $this->game->playerStats->inc('battles_lost', 1, $playerId);
    }

    public function incPlayerFatigue(int $playerId, int $amount): void
    {
        $this->game->playerStats->inc('fatigue_gained', $amount, $playerId);
    }

    /**
     * Retrieve a table-level statistic by name.
     *
     * @param string $stat The statistic name.
     * @return int|float|bool
     */
    public function getTableStat(string $stat)
    {
        return $this->game->tableStats->get($stat);
    }

    /**
     * Retrieve a player-level statistic by name.
     *
     * @param string $stat The statistic name.
     * @param int $playerId The player ID.
     * @return int|float|bool
     */
    public function getPlayerStat(string $stat, int $playerId)
    {
        return $this->game->playerStats->get($stat, $playerId);
    }
}
