<?php

namespace Bga\Games\DeadMenPax\DB\Models;

use Bga\Games\DeadMenPax\DB\dbColumn;
use Bga\Games\DeadMenPax\DB\dbKey;

class PlayerModel
{
    #[dbKey('player_id')]
    public int $id;

    #[dbColumn('player_score')]
    public int $score;

    #[dbColumn('player_fatigue')]
    public int $fatigue;

    #[dbColumn('player_battle_strength')]
    public int $battleStrength;

    #[dbColumn('player_room_x')]
    public int $roomX;

    #[dbColumn('player_room_y')]
    public int $roomY;

    #[dbColumn('player_is_on_ship')]
    public bool $isOnShip;

    #[dbColumn('player_actions_remaining')]
    public int $actionsRemaining;

    #[dbColumn('player_max_actions')]
    public int $maxActions;

    #[dbColumn('player_character_card_id')]
    public ?int $characterCardId;

    #[dbColumn('player_item_card_id')]
    public ?int $itemCardId;
}
