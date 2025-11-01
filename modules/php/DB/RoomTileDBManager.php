<?php

namespace Bga\Games\DeadMenPax\DB;

use Bga\Games\DeadMenPax\DB\Models\RoomTileModel;
use Bga\GameFramework\Table;

class RoomTileDBManager extends DBManager
{
    public function __construct(Table $game)
    {
        parent::__construct('room_tile', RoomTileModel::class, $game);
    }
}
