# State classes: State directory - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# State classes: State directory

From Board Game Arena

[Jump to navigation](#mw-head) [Jump to search](#searchInput)

  

**Game File Reference**

**[Overview](/Studio_file_reference "Studio file reference")**

-   [**dbmodel.sql**](/Game_database_model:_dbmodel.sql "Game database model: dbmodel.sql") - database model
-   [**gameinfos.inc.php**](/Game_meta-information:_gameinfos.inc.php "Game meta-information: gameinfos.inc.php") - meta-information
-   [**gameoptions.json**](/Options_and_preferences:_gameoptions.json,_gamepreferences.json "Options and preferences: gameoptions.json, gamepreferences.json") - game options & user preferences
-   [**img/**](/Game_art:_img_directory "Game art: img directory") - game art
-   [**Game Metadata Manager**](/Game_metadata_manager "Game metadata manager") - tags and metadata media
-   [**material.inc.php**](/Game_material_description:_material.inc.php "Game material description: material.inc.php") - static data
-   **misc/** - studio-only storage
-   **modules/** - additional game code
-   **States/** - State classes
-   [**states.inc.php**](/Your_game_state_machine:_states.inc.php "Your game state machine: states.inc.php") - state machine
-   [**stats.json**](/Game_statistics:_stats.json "Game statistics: stats.json") - statistics
-   [X.**action.php**](/Players_actions:_yourgamename.action.php "Players actions: yourgamename.action.php") - player actions
-   [X**.css**](/Game_interface_stylesheet:_yourgamename.css "Game interface stylesheet: yourgamename.css") - interface stylesheet
-   [**Game.php**](/Main_game_logic:_Game.php "Main game logic: Game.php") - main logic
-   [X.**js**](/Game_interface_logic:_yourgamename.js "Game interface logic: yourgamename.js") - interface logic
-   [X.**view.php**](/Game_layout:_view_and_template:_yourgamename.view.php_and_yourgamename_yourgamename.tpl "Game layout: view and template: yourgamename.view.php and yourgamename yourgamename.tpl") - dynamic game layout
-   [X\_X.**tpl**](/Game_layout:_view_and_template:_yourgamename.view.php_and_yourgamename_yourgamename.tpl "Game layout: view and template: yourgamename.view.php and yourgamename yourgamename.tpl") - static game layout

  

---

**Useful Components**

**Official**

-   [Deck](/Deck "Deck"): a PHP component to manage cards (deck, hands, picking cards, moving cards, shuffle deck, ...).
-   [PlayerCounter and TableCounter](/PlayerCounter_and_TableCounter "PlayerCounter and TableCounter"): PHP components to manage counters.
-   [Draggable](/Draggable "Draggable"): a JS component to manage drag'n'drop actions.
-   [Counter](/Counter "Counter"): a JS component to manage a counter that can increase/decrease (ex: player's score).
-   [ExpandableSection](/ExpandableSection "ExpandableSection"): a JS component to manage a rectangular block of HTML than can be displayed/hidden.
-   [Scrollmap](/Scrollmap "Scrollmap"): a JS component to manage a scrollable game area (useful when the game area can be infinite. Examples: Saboteur or Takenoko games).
-   [Stock](/Stock "Stock"): a JS component to manage and display a set of game elements displayed at a position.
-   [Zone](/Zone "Zone"): a JS component to manage a zone of the board where several game elements can come and leave, but should be well displayed together (See for example: token's places at Can't Stop).
-   [bga-animations](/BgaAnimations "BgaAnimations") : a JS component for animations.
-   [bga-cards](/BgaCards "BgaCards") : a JS component for cards.
-   [bga-dice](/BgaDice "BgaDice") : a JS component for dice.
-   [bga-autofit](/BgaAutofit "BgaAutofit") : a JS component to make text fit on a fixed size div.
-   [bga-score-sheet](/BgaScoreSheet "BgaScoreSheet") : a JS component to help you display an animated score sheet at the end of the game.

**Unofficial**

-   [BGA Code Sharing](/BGA_Code_Sharing "BGA Code Sharing") - Shared resources, projects on git hub, common code, other links
-   [BGA Studio Cookbook](/BGA_Studio_Cookbook "BGA Studio Cookbook") - Tips and instructions on using API's, libraries and frameworks
-   [Common board game elements image resources](/Common_board_game_elements_image_resources "Common board game elements image resources")

  

---

**Game Development Process**

-   [First steps with BGA Studio](/First_steps_with_BGA_Studio "First steps with BGA Studio")
-   [Create a game in BGA Studio: Complete Walkthrough](/Create_a_game_in_BGA_Studio:_Complete_Walkthrough "Create a game in BGA Studio: Complete Walkthrough")
-   [Tutorial reversi](/Tutorial_reversi "Tutorial reversi")
-   [Tutorial hearts](/Tutorial_hearts "Tutorial hearts")
-   [BGA Studio Guidelines](/BGA_Studio_Guidelines "BGA Studio Guidelines")
-   [BGA game Lifecycle](/BGA_game_Lifecycle "BGA game Lifecycle")
-   [Pre-release checklist](/Pre-release_checklist "Pre-release checklist")
-   [Post-release phase](/Post-release_phase "Post-release phase")
-   [Player Resources](/Help "Help") - add player help/rules to your game page

  

---

**Guides for Common Topics**

-   [Translations](/Translations "Translations") - make your game translatable
-   [Game Replay](/Game_replay "Game replay")
-   [Mobile Users](/Your_game_mobile_version "Your game mobile version")
-   [3D](/3D "3D")
-   [Compatibility](/Compatibility "Compatibility")

  

---

**Miscellaneous Resources**

-   [Studio FAQ](/Studio_FAQ "Studio FAQ")
-   [Tools and tips of BGA Studio](/Tools_and_tips_of_BGA_Studio "Tools and tips of BGA Studio") - Tips and instructions on setting up development environment
-   [Studio logs](/Studio_logs "Studio logs") - Instructions for log access
-   [Practical debugging](/Practical_debugging "Practical debugging") - Tips focused on debugging
-   [Troubleshooting](/Troubleshooting "Troubleshooting") - Most common "I am really stuck" situations
-   [Studio Bugs](https://studio.boardgamearena.com/bugs) - Reports against Studio itself (not BGA!)

State classes allow to create a PHP class for each game state. It allows to split the code in multiple files, without using Traits.

The advantage is that the IDE understands the structure and can provide auto-completion and error highlights, that are lost in Traits.

## Contents

-   [1 Structure](#Structure)
    -   [1.1 Base example](#Base_example)
    -   [1.2 Initial state](#Initial_state)
    -   [1.3 getArgs function](#getArgs_function)
        -   [1.3.1 Private info in args](#Private_info_in_args)
        -   [1.3.2 Flag to indicate a skipped state](#Flag_to_indicate_a_skipped_state)
    -   [1.4 onEnteringState function](#onEnteringState_function)
    -   [1.5 action functions](#action_functions)
    -   [1.6 zombie function](#zombie_function)
-   [2 Migrating for states written in states.inc.php and Game.php](#Migrating_for_states_written_in_states.inc.php_and_Game.php)
    -   [2.1 Moved elements](#Moved_elements)
    -   [2.2 New elements](#New_elements)

## Structure

### Base example

The State class in `modules/php/States/PlayerTurn.php` will have this structure:

<?php
declare(strict\_types=1);

namespace Bga\\Games\\<MyGameName>\\States;

use Bga\\GameFramework\\StateType;
use Bga\\GameFramework\\States\\GameState;
use Bga\\GameFramework\\States\\PossibleAction;
use Bga\\Games\\<MyGameName>\\Game;

class PlayerTurn extends GameState
{
    function \_\_construct(
        protected Game $game,
    ) {
        parent::\_\_construct($game,
            id: 2,
            type: StateType::ACTIVE\_PLAYER,

            // optional
            description: clienttranslate('${actplayer} must play a card or pass'),
            descriptionMyTurn: clienttranslate('${you} must play a card or pass'),
            transitions: \[\],
            updateGameProgression: false,
            initialPrivate: null,
        );
    }

    public function getArgs(): array
    {
        // the data sent to the front when entering the state
        return \[\];
    } 

    function onEnteringState(int $activePlayerId) {
        // the code to run when entering the state
    }   

    #\[PossibleAction\]
    public function actPlayCard(int $cardId, int $activePlayerId, array $args): string
    {
        // the code to run when the player triggers actPlayCard with bgaPerformAction
    }

    function zombie(int $playerId): string {
        // the code to run when the player is a Zombie
    }
}

The state must extends `Bga\GameFramework\States\GameState` and follow the same `__construct` function as the example. Only $game, id and type are mandatory, other parameters are optional. name can be specified, by default it will be the class name.

### Initial state

To indicate your initial state, add `return PlayerTurn::class;` from `setupNewGame` function.

### getArgs function

This function should return the necessary information for the front to display the information related to this state.

It accepts "magic" params that will be automatically filled by the framework:

-   `int $activePlayerId` (or `int $active_player_id`) will be filled by the id of the active player. To be used on ACTIVE\_PLAYER states only.
-   `int $playerId` (or `int $player_id`/`int $currentPlayerId`/`int $current_player_id`) will be filled by the player id of the current PRIVATE state. To be used on PRIVATE states only.

#### Private info in args

By default, all data provided through this method are PUBLIC TO ALL PLAYERS. Please do not send any private data with this method, as a cheater could see it even it is not used explicitly by the game interface logic.

However, it is possible to specify that some data should be sent to specific players only.

Example:

    function getArgs(int $activePlayerId): array  {
        return array(
            '\_private' => array(   // all data inside this array will be private
                $activePlayerId => array(   // will be sent only to that player   
                    'somePrivateData' => $this->getSomePrivateData($activePlayerId)   
                )
            ),

            'possibleMoves' => $this->getPossibleMoves()   // will be sent to all players
        );
    }

Inside the js file, these variables will be available through \`args.args.\_private\`. (e.g. \`args.args.\_private.somePrivateData\`)

IMPORTANT: in certain situations (i.e. MULTIPLE\_ACTIVE\_PLAYER game state) these "private data" features can have a significant impact on performance. Please do not use if not needed.

#### Flag to indicate a skipped state

By default, The front-end will be notified of entering/leaving all states. To speed up the front-end chaining of automatically passed states, you can disable this state change notification, so the front-end doesn't trigger the preparation steps for a state that you know will be automatically skipped, and it may reduce sent args. In this case, define the **\_no\_notify** flag to true in the state args.

function getArgs(int $activePlayerId): array {
  $playableCardsIds = ...;
  return \[
    'playableCardsIds' => $playableCardsIds,
    '\_no\_notify' => count(playableCardsIds) === 0,
  \];
}
function onEnteringState(int $activePlayerId ,array $args): void {
  if ($args\['\_no\_notify'\]) {
    return $this->actPass($activePlayerId); // return the redirection sent by the action!
  }
}

In this example, it might avoid a blinking message "You must play a card" (quickly replaced by the next state message) when you cannot play a card and the game automatically skips this state.

IMPORTANT: if you use \_no\_notify, you must handle a redirection to another state on the `onEnteringState` function!

Note that if you play synced notifications during a skipped state, it will display the notifications on the previous state. For example, for an endScore state width description "Computing end score..." sending a lot of animated notifications, you should NOT use this flag so the description is visible.

### onEnteringState function

This function will be triggered when you enter the state.

It accepts "magic" params that will be automatically filled by the framework:

-   `array $args` will be filled by the result of $this->getArgs().
-   `int $activePlayerId` (or `int $active_player_id`) will be filled by the id of the active player. To be used on ACTIVE\_PLAYER states only.
-   `int $playerId` (or `int $player_id`/`int $currentPlayerId`/`int $current_player_id`) will be filled by the player id of the current PRIVATE state. To be used on PRIVATE states only.

This function can do state redirection by returning a value :

-   a class name: `return NextPlayer::class` will redirect to the state declared in that class.
-   a state id: `return ST_END_GAME;` = `return 99;` will redirect to the state of that id. It must be typed as int, numbers in a string won't work.
-   a transition name: `return 'nextPlayer';` will redirect to the transition of that name (requires `transitions` to be declared in the constructor).

### action functions

These functions will be triggered when you call them from the front using bgaPerformAction. They must be prefixed by `act`.

Every normal function should have a `#[PossibleAction]` attribute on top of it to indicate the front it's a normal action for the player.

It accepts "magic" params that will be automatically filled by the framework:

-   `array $args` will be filled by the result of $this->getArgs().
-   `int $activePlayerId` (or `int $active_player_id`) will be filled by the id of the active player (not necessarily the one triggering the action!). To be used on ACTIVE\_PLAYER states only.
-   `int $currentPlayerId` (or `int $current_player_id`) will be filled by the id of the player who triggered the action.

The return value works the same way as onEnteringState.

If you trigger an action from the front, and it's not declared in this state, the framework will check if the function exists in the Game.php file (for actions that can be triggered at any state).

### zombie function

In non GAME states, the `zombie` function is mandatory. The first parameter `int $playerId` will be filled by the Zombified player id.

You can see some examples in the [Zombie Mode](/Zombie_Mode "Zombie Mode") page.

It accepts "magic" params that will be automatically filled by the framework:

-   `array $args` will be filled by the result of $this->getArgs().

The return value works the same way as onEnteringState.

## Migrating for states written in states.inc.php and Game.php

### Moved elements

The function `getArgs` replaces the function that was declared as `"args" => "argXXX"` on states.inc.php. Same for the function `onEnteringState` that was `"action" => "stXXX"` on states.inc.php. The `zombie` function doesn't have the state as first parameter anymore, because it's not needed in this context.

The possible actions for this states don't need to be declared as an array, they will be found with the tag `#[PossibleAction]` over each possible action.

The functions declared in Game.php will be accessible with `$this->game` instead of `$this`. The Game sub-objects are available on the State class too, so you can write `$this->notif->all` without needing to pass through the game variable.

### New elements

The `getArgs`, `onEnteringState` and `actXXX` functions can set some predefined parameters that will be automatically filled (see chapter above).

For all those functions, and also the `zombie` function, they can now send a redirection to a game state as a returned result (see chapter above). _If you use this writing, remove `$this->gamestate->nexState` to avoid double redirection!_

The initialPrivate parameter of the constructor can be null or an int as before, but can now also accept a class name as value `initialPrivate: PlaceCard::class`

You can now pass a state class as the parameter of `GameStateBuilder::gameSetup(PlayDisc::class)->build()`, and on some function that previously only accepted transitions, like `$this->gamestate->nextPrivateState(ConfirmTurn::class)` or `$this->gamestate->setPlayerNonMultiactive($currentPlayerId, EndRound::class)`

If all your classes are migrated to State classes, you can remove the states.inc.php file.

Retrieved from "[http:///index.php?title=State\_classes:\_State\_directory&oldid=26834](http:///index.php?title=State_classes:_State_directory&oldid=26834)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")