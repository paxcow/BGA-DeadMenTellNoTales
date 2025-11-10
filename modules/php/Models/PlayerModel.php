<?php

namespace Bga\Games\DeadMenPax\Models;

use Bga\Games\DeadMenPax\DB\dbColumn;
use Bga\Games\DeadMenPax\DB\dbKey;

class PlayerModel
{
    #[dbKey('player_id')]
    public int $id;

    #[dbColumn('player_color')]
    public string $color = '';

    #[dbColumn('player_canal')]
    public string $canal = '';

    #[dbColumn('player_name')]
    public string $name = '';

    #[dbColumn('player_avatar')]
    public string $avatar = '';

    #[dbColumn('player_no')]
    public int $playerNo = 0;

    #[dbColumn('player_score')]
    public int $score = 0;

    #[dbColumn('player_fatigue')]
    public int $fatigue = 0;

    #[dbColumn('player_battle_strength')]
    public int $battleStrength = 0;

    #[dbColumn('player_room_x')]
    public int $roomX = -1;

    #[dbColumn('player_room_y')]
    public int $roomY = -1;

    #[dbColumn('player_is_on_ship')]
    public bool $isOnShip = false;

    #[dbColumn('player_actions_remaining')]
    public int $actionsRemaining = 0;

    #[dbColumn('player_max_actions')]
    public int $maxActions = 0;

    #[dbColumn('player_extra_actions')]
    public int $extraActions = 0;

    #[dbColumn('player_character_card_id')]
    public ?int $characterCardId = null;

    #[dbColumn('player_item_card_id')]
    public ?int $itemCardId = null;

    #[dbColumn('player_current_enemy_token_id')]
    public ?string $currentEnemyTokenId = null;

    #[dbColumn('player_current_battle_room_id')]
    public ?int $currentBattleRoomId = null;

    #[dbColumn('player_battle_state')]
    public ?string $battleState = null;
}
