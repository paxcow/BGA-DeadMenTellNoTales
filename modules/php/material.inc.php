<?php

// Room tile door constants (bit flags)
// These match the RoomTile class constants
define('DOOR_NORTH', 1);
define('DOOR_EAST', 2);
define('DOOR_SOUTH', 4);
define('DOOR_WEST', 8);

// Starting room tiles (4 total) - placed immediately on board during setup
$this->startingRooms = [
    [
        "tile_type" => "starting_room",
        "color" => "yellow",
        "pips" => 0,  // fire starting value
        "doors" => 15,  // All doors: NORTH + EAST + SOUTH + WEST = 1+2+4+8 = 15
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "x" => 0,
        "y" => 0,
        "is_starting_tile" => true
    ],
    [
        "tile_type" => "starting_room",
        "color" => "red",
        "pips" => 0,
        "doors" => 11,  // NORTH + EAST + WEST = 1+2+8 = 11 (no south)
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "x" => -1,
        "y" => 0,
        "is_starting_tile" => true
    ],
    [
        "tile_type" => "starting_room", 
        "color" => "yellow",
        "pips" => 0,
        "doors" => 11,  // NORTH + EAST + WEST = 1+2+8 = 11 (no south)
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "x" => 0,
        "y" => 1,
        "is_starting_tile" => true
    ],
    [
        "tile_type" => "starting_room",
        "color" => "red", 
        "pips" => 0,
        "doors" => 15,  // All doors: NORTH + EAST + SOUTH + WEST = 1+2+4+8 = 15
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "x" => 1,
        "y" => 0,
        "is_starting_tile" => true
    ]
];

// Regular room tiles (20 total) - created as deck during setup
$this->roomTiles = [
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 3,
        "doors" => 15,  // All doors: 1+2+4+8 = 15
        "has_powder_keg" => true,
        "keg_threshold" => 5,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 4,
        "doors" => 15,  // All doors: 1+2+4+8 = 15
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 4,
        "doors" => 15,  // All doors: 1+2+4+8 = 15
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 2,
        "doors" => 4,  // Only SOUTH = 4
        "has_powder_keg" => true,
        "keg_threshold" => 4,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 4,
        "doors" => 6,  // EAST + SOUTH = 2+4 = 6
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 1,
        "doors" => 12,  // SOUTH + WEST = 4+8 = 12
        "has_powder_keg" => true,
        "keg_threshold" => 3,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 2,
        "doors" => 6,  // EAST + SOUTH = 2+4 = 6
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 3,
        "doors" => 12,  // SOUTH + WEST = 4+8 = 12
        "has_powder_keg" => true,
        "keg_threshold" => 5,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 1,
        "doors" => 14,  // EAST + SOUTH + WEST = 2+4+8 = 14
        "has_powder_keg" => true,
        "keg_threshold" => 3,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 1,
        "doors" => 14,  // EAST + SOUTH + WEST = 2+4+8 = 14
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 2,
        "doors" => 14,  // EAST + SOUTH + WEST = 2+4+8 = 14
        "has_powder_keg" => true,
        "keg_threshold" => 4,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 3,
        "doors" => 2,  // Only EAST = 2
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 0,
        "doors" => 13,  // NORTH + SOUTH + WEST = 1+4+8 = 13
        "has_powder_keg" => true,
        "keg_threshold" => 2,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 3,
        "doors" => 14,  // EAST + SOUTH + WEST = 2+4+8 = 14
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 4,
        "doors" => 7,  // NORTH + EAST + SOUTH = 1+2+4 = 7
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 4,
        "doors" => 11,  // NORTH + EAST + WEST = 1+2+8 = 11
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 2,
        "doors" => 10,  // EAST + WEST = 2+8 = 10
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "red",
        "pips" => 0,
        "doors" => 13,  // NORTH + SOUTH + WEST = 1+4+8 = 13
        "has_powder_keg" => true,
        "keg_threshold" => 2,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 2,
        "doors" => 13,  // NORTH + SOUTH + WEST = 1+4+8 = 13
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ],
    [
        "tile_type" => "room",
        "color" => "yellow",
        "pips" => 3,
        "doors" => 10,  // EAST + WEST = 2+8 = 10
        "has_powder_keg" => false,
        "keg_threshold" => null,
        "is_starting_tile" => false
    ]
];

// Revenge card data
$this->revengeCards = [
    [
        "color" => "both",
        "threshold" => 0,
        "effect" => "crew"
    ],
    [
        "color" => "both",
        "threshold" => 1,
        "effect" => "crew"
    ],
    [
        "color" => "both",
        "threshold" => 2,
        "effect" => "crew"
    ],
    [
        "color" => "both",
        "threshold" => 3,
        "effect" => "crew"
    ],
    [
        "color" => "both",
        "threshold" => 4,
        "effect" => "add"
    ],
    [
        "color" => "both",
        "threshold" => 5,
        "effect" => null
    ],
    [
        "color" => "both",
        "threshold" => 5,
        "effect" => null
    ],
    [
        "color" => "red",
        "threshold" => 0,
        "effect" => "add"
    ],
    [
        "color" => "red",
        "threshold" => 1,
        "effect" => "add"
    ],
    [
        "color" => "red",
        "threshold" => 2,
        "effect" => "spread"
    ],
    [
        "color" => "red",
        "threshold" => 3,
        "effect" => "spread"
    ],
    [
        "color" => "red",
        "threshold" => 4,
        "effect" => "spread"
    ],
    [
        "color" => "red",
        "threshold" => 5,
        "effect" => null
    ],
    [
        "color" => "yellow",
        "threshold" => 0,
        "effect" => "add"
    ],
    [
        "color" => "yellow",
        "threshold" => 1,
        "effect" => "add"
    ],
    [
        "color" => "yellow",
        "threshold" => 2,
        "effect" => "spread"
    ],
    [
        "color" => "yellow",
        "threshold" => 3,
        "effect" => "spread"
    ],
    [
        "color" => "yellow",
        "threshold" => 4,
        "effect" => "spread"
    ],
    [
        "color" => "yellow",
        "threshold" => 5,
        "effect" => null
    ]
];

// Token data - standardized properties
$this->tokens = [
    [
        "front_type" => "crew",
        "front_value" => 3,
        "back_type" => "grog",
        "back_value" => 4,
        "quantity" => 2
    ],
    [
        "front_type" => "crew",
        "front_value" => 3,
        "back_type" => "cutlass",
        "back_value" => null,
        "quantity" => 1
    ],
    [
        "front_type" => "crew",
        "front_value" => 4,
        "back_type" => "cutlass",
        "back_value" => null,
        "quantity" => 2
    ],
    [
        "front_type" => "crew",
        "front_value" => 4,
        "back_type" => "grog",
        "back_value" => 5,
        "quantity" => 1
    ],
    [
        "front_type" => "crew",
        "front_value" => 5,
        "back_type" => "cutlass",
        "back_value" => null,
        "quantity" => 2
    ],
    [
        "front_type" => "guardian",
        "front_value" => 6,
        "back_type" => "treasure",
        "back_value" => null,
        "quantity" => 2
    ],
    [
        "front_type" => "guardian",
        "front_value" => 7,
        "back_type" => "treasure",
        "back_value" => null,
        "quantity" => 2
    ],
    [
        "front_type" => "guardian",
        "front_value" => 8,
        "back_type" => "treasure",
        "back_value" => null,
        "quantity" => 2
    ],
    [
        "front_type" => "trap",
        "front_value" => null,
        "back_type" => null,
        "back_value" => null,
        "quantity" => 6
    ]
];

// Item card data
$this->itemCards = [
    [
        "name" => clienttranslate("Blanket"),
        "text" => clienttranslate("May lower a Fire Die by 2 once per turn."),
        "cost" => 1
    ],
    [
        "name" => clienttranslate("Bucket"),
        "text" => clienttranslate("May lower a Fire Die in an adjacent room once per turn."),
        "cost" => 1
    ],
    [
        "name" => clienttranslate("Compass"),
        "text" => clienttranslate("One free Walk or Run Action per turn."),
        "cost" => 0
    ],
    [
        "name" => clienttranslate("Dagger"),
        "text" => clienttranslate("One free Eliminate Deckhand Action per turn."),
        "cost" => 0
    ],
    [
        "name" => clienttranslate("Pistol"),
        "text" => clienttranslate("May attack from an adjacent room for one Action once per turn. No fatigue is lost for a failed attack."),
        "cost" => 1
    ],
    [
        "name" => clienttranslate("Rum"),
        "text" => clienttranslate("One free Rest Action per turn."),
        "cost" => 0
    ],
    [
        "name" => clienttranslate("Sword"),
        "text" => clienttranslate("Add 1 to Strength in Battle."),
        "cost" => 0
    ]
];
