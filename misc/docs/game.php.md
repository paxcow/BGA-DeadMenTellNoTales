# Main game logic: Game.php - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# Main game logic: Game.php

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
-   [**States/**](/State_classes:_State_directory "State classes: State directory") - State classes
-   [**states.inc.php**](/Your_game_state_machine:_states.inc.php "Your game state machine: states.inc.php") - state machine
-   [**stats.json**](/Game_statistics:_stats.json "Game statistics: stats.json") - statistics
-   [X.**action.php**](/Players_actions:_yourgamename.action.php "Players actions: yourgamename.action.php") - player actions
-   [X**.css**](/Game_interface_stylesheet:_yourgamename.css "Game interface stylesheet: yourgamename.css") - interface stylesheet
-   **Game.php** - main logic
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

This is the main file for your game logic. Here you initialize the game, persist data, implement the rules and notify the client interface of changes.

This is the main class that implements the "server" callbacks. As it is a server it cannot initiate any data communicate with the game client (running in browser) and only can respond to client using notifications.

Your php class instance won't be in memory between two callbacks, every time client send a request a new class will be created, constructor will be called and eventually your callback function.

**Note:** this file is now named Game.php, located in the modules/php directory. If you see it named yourgamename.game.php in the root dir, it's the legacy usage. In the legacy usage, the namepaces don't exists.

## Contents

-   [1 File Structure](#File_Structure)
-   [2 Accessing player information](#Accessing_player_information)
-   [3 Accessing the database](#Accessing_the_database)
-   [4 Use globals](#Use_globals)
-   [5 Use globals (numbers only / game options)](#Use_globals_\(numbers_only_/_game_options\))
    -   [5.1 BGA predefined globals](#BGA_predefined_globals)
-   [6 Game states and active players](#Game_states_and_active_players)
    -   [6.1 Activate player handling](#Activate_player_handling)
    -   [6.2 Multiple activate player handling](#Multiple_activate_player_handling)
    -   [6.3 States functions](#States_functions)
    -   [6.4 Private parallel states](#Private_parallel_states)
        -   [6.4.1 State Arguments in Private parallel states](#State_Arguments_in_Private_parallel_states)
        -   [6.4.2 Inactive Players](#Inactive_Players)
-   [7 Actions (autowired)](#Actions_\(autowired\))
    -   [7.1 Possible attributes](#Possible_attributes)
    -   [7.2 checkAction](#checkAction)
    -   [7.3 act prefix](#act_prefix)
-   [8 Players turn order](#Players_turn_order)
-   [9 Notifications](#Notifications)
    -   [9.1 Notify](#Notify)
        -   [9.1.1 HTML in Notifications](#HTML_in_Notifications)
        -   [9.1.2 Recursive Notifications](#Recursive_Notifications)
        -   [9.1.3 Excluding some players](#Excluding_some_players)
        -   [9.1.4 Using player names](#Using_player_names)
    -   [9.2 NotifyPlayer](#NotifyPlayer)
    -   [9.3 Notification decorators](#Notification_decorators)
-   [10 Randomization](#Randomization)
    -   [10.1 Dice and bga\_rand](#Dice_and_bga_rand)
    -   [10.2 Arrays](#Arrays)
    -   [10.3 shuffle and cards shuffling](#shuffle_and_cards_shuffling)
    -   [10.4 Other methods](#Other_methods)
-   [11 Game statistics](#Game_statistics)
-   [12 Translations](#Translations)
-   [13 Manage player scores and Tie breaker](#Manage_player_scores_and_Tie_breaker)
    -   [13.1 Normal scoring](#Normal_scoring)
    -   [13.2 Tie breaker](#Tie_breaker)
    -   [13.3 Co-operative game](#Co-operative_game)
    -   [13.4 Semi-coop](#Semi-coop)
    -   [13.5 Only "winners" and "losers"](#Only_"winners"_and_"losers")
    -   [13.6 Solo](#Solo)
    -   [13.7 Player elimination](#Player_elimination)
-   [14 Reflexion time](#Reflexion_time)
-   [15 Undo moves](#Undo_moves)
-   [16 Managing errors and exceptions](#Managing_errors_and_exceptions)
-   [17 Zombie mode](#Zombie_mode)
-   [18 User preferences](#User_preferences)
-   [19 Player color preferences](#Player_color_preferences)
    -   [19.1 Custom color assignments](#Custom_color_assignments)
-   [20 Legacy games API](#Legacy_games_API)
-   [21 Players text input and moderation](#Players_text_input_and_moderation)
-   [22 Language dependent games API](#Language_dependent_games_API)
-   [23 Debugging and Tracing](#Debugging_and_Tracing)
-   [24 Creating other classes](#Creating_other_classes)

## File Structure

The details of how the file is structured are described directly with comments in the code skeleton provided to you.

Here is the basic structure:

-   **\_\_construct**: the game constructor, where you define global variables and initiaze class members.
-   **setupNewGame**: initial setup of the game. Takes an array of players, indexed by player\_id. Structure of each player includes player\_name, player\_canal, player\_avatar, and flags indicating admin/ai/premium/order/language/beginner.
-   **getAllDatas**: where you retrieve all game data during a complete reload of the game. Return value must be associative array. Value of 'players' is reserved for returning players data from players table, if you set it it must follow certain rules

       $result \['players'\] = $this->getCollectionFromDb("SELECT \`player\_id\` \`id\`, \`player\_score\` \`score\`, \`player\_no\` \`no\`, \`player\_color\` \`color\` FROM \`player\`");
       // Returned value must include \['players'\]\[$player\_id\]\]\['score'\] for scores to populate when F5 is pressed.

-   **getGameProgression**: where you compute the game progression indicator. Returns a number indicating percent of progression (0-100). Used to calculate ELO changes of remaining players when a player quits, or as a conceding requirement (in non-tournament 2 player games, a player may concede if the progression is at least 50%).
-   Utility functions: your utility functions.
-   Player actions: the entry points for players actions ([more info here](https://en.doc.boardgamearena.com/Players_actions:_yourgamename.action.php)).
-   Game state arguments: methods to return additional data on specific game states ([more info here](http://en.doc.boardgamearena.com/Your_game_state_machine:_states.inc.php#args)).
-   Game state actions: the logic to run when entering a new game state ([more info here](http://en.doc.boardgamearena.com/Your_game_state_machine:_states.inc.php#action)).
-   **initTable**: (not part of template) - this function is called for every php callback by the framework and it can be implement by the game (empty by default). You can use it in rare cases where you need to read database and manipulate some data before any ANY php entry functions are called (such as getAllDatas,action\*,st\*, etc). Note: it is not called before arg\* methods
-   **zombieTurn**: what to do it's the turn of a zombie player.
-   **upgradeTableDb**: function to migrate database if you change it after release on production.
-   **getGameName**: returns the game name. This will be setup when you create the project. If you are copying files in from another project, make sure you keep this function intact. It must return the right game name, or lots of things will be broken.

## Accessing player information

**Important**: In the following methods, be mindful of the difference between the "active" player and the "current" player. The **active** player is the player whose turn it is - not necessarily the player who sent a request! The **current** player is the player who sent the request and will see the results returned by your methods: not necessarily the player whose turn it is!

  

getPlayersNumber()

Returns the number of players playing at the table

Note: doesn't work in the beggining of setupNewGame (use count($players) instead). It will work after initialization of player table.

getActivePlayerId()

Get the "active\_player", whatever what is the current state type.

Note: it does NOT mean that this player is active right now, because state type could be "game" or "multiplayer"

Note: avoid using this method in a "multiplayer" state because it does not mean anything.

getActivePlayerName()

Get the "active\_player" name

Note: avoid using this method in a "multiplayer" state because it does not mean anything.

getPlayerNameById($player\_id)

Get the name by id

getPlayerColorById($player\_id)

Get the color by id

getPlayerNoById($player\_id)

Get 'player\_no' (number) by id

  

loadPlayersBasicInfos()

Get an associative array with generic data about players (ie: not game specific data).

The key of the associative array is the player id. The returned table is cached, so ok to call multiple times without performance concerns.

The content of each value is:

\* player\_name - the name of the player

\* player\_color (ex: ff0000) - the color code of the player (as string)

\* player\_no - the position of the player at the start of the game in natural table order, i.e. 1,2,3

                $players = $this->loadPlayersBasicInfos();
                foreach ($players as $player\_id => $info) {
                    $player\_color = $info\['player\_color'\];
                    ...
                }

Note: if you want array of player ids only you can do this:

   $player\_ids =  array\_keys($this->loadPlayersBasicInfos());  

getCurrentPlayerId(bool $bReturnNullIfNotLogged = false) int

Get the "current\_player". The current player is the one from which the action originated (the one who sent the request).

**Be careful**: This is not necessarily the active player!

In general, you shouldn't use this method, unless you are in "multiplayer" state.

**Very important**: in your setupNewGame and zombieTurn function, you must never use getCurrentPlayerId() or getCurrentPlayerName(),

otherwise it will fail with a "Not logged" error message (these actions are triggered from the main site and propagated to the gameserver from a server, not from a browser. As a consequence, there is no current player associated to these actions).

getCurrentPlayerName(bool $bReturnEmptyIfNotLogged = false) string

Get the "current\_player" name.

Note: this will throw an exception if current player is not at the table, i.e. spectator

Be careful using this method (see above).

getCurrentPlayerColor()

Get the "current\_player" color.

Note: this will throw an exception if current player is not at the table, i.e. spectator

Be careful using this method (see above).

isCurrentPlayerZombie()

Check the "current\_player" zombie status. If true, player is zombie, i.e. left or was kicked out of the game.

Note: this will throw an exception if current player is not at the table, i.e. spectator

isSpectator()

Check the "current\_player" spectator status. If true, the user accessing the game is a spectator (not part of the game). For this user, the interface should display all public information, and no private information (like a friend sitting at the same table as players and just spectating the game).

getActivePlayerColor()

This function does not seems to exist in API, if you need it here is implementation

     function getActivePlayerColor() {
       $player\_id = $this->getActivePlayerId();
       $players = $this->loadPlayersBasicInfos();
       if (isset($players\[$player\_id\]))
           return $players\[$player\_id\]\['player\_color'\];
       else
           return null;
   }

isPlayerZombie($player\_id)

This method does not exists, but if you need it it looks like this

   protected function isPlayerZombie($player\_id) {
       $players = $this->loadPlayersBasicInfos();
       if (! isset($players\[$player\_id\]))
           throw new \\BgaSystemException("Player $player\_id is not playing here");
       
       return ($players\[$player\_id\]\['player\_zombie'\] == 1);
   }

## Accessing the database

The main game logic should be the only point from which you should access the game database. You access your database using SQL queries with the methods below.

**IMPORTANT**

BGA uses [database transactions](http://dev.mysql.com/doc/refman/5.0/en/sql-syntax-transactions.html). This means that your database changes WON'T BE APPLIED to the database until your request ends normally (web request, not database request). Using transactions is in fact very useful for you; at any time, if your game logic detects that something is wrong (example: a disallowed move), you just have to throw an exception and all changes to the game situation will be removed. This also means that you need not (and in fact cannot) use your own transactions for multiple related database operations.

However there are sets of database operation that will do implicit commit (most common mistake is to use "TRUNCATE"), you cannot use these operations during the game, it breaks the unrolling of transactions and will lead to nasty issues ([https://mariadb.com/kb/en/sql-statements-that-cause-an-implicit-commit](https://mariadb.com/kb/en/sql-statements-that-cause-an-implicit-commit)).

All methods below are part of game class (and view class) and can be accessed using $this->

DbQuery( string $sql )

This is the generic method to access the database.

It can execute any type of SELECT/UPDATE/DELETE/REPLACE/INSERT query on the database. Returns result of the query.

For SELECT queries, the specialized methods below are much better.

Do not use method for TRUNCATE, DROP and other table altering operations. See disclamer above about implicit commits. If you really need TRUNCATE use DELETE FROM xxx instead.

getUniqueValueFromDB( string $sql )

Returns a unique value from DB or null if no value is found.

$sql must be a SELECT query.

Raise an exception if more than 1 row is returned.

getCollectionFromDB( string $sql, bool $bSingleValue=false ) array

Returns an associative array of rows for a sql SELECT query.

The key of the resulting associative array is the first field specified in the SELECT query.

The value of the resulting associative array is an associative array with all the field specified in the SELECT query and associated values.

First column must be a primary or alternate key (semantically, it does not actually have to declared in sql as such).

The resulting collection can be empty (it won't be null).

If you specified $bSingleValue=true and if your SQL query requests 2 fields A and B, the method returns an associative array "A=>B", otherwise its A=>\[A,B\]

Note: The name a bit misleading, it really return associative array, i.e. map and NOT a collection. You cannot use it to get list of values which may have duplicates (hence primary key requirement on first column). If you need simple array use getObjectListFromDB() method.

Example 1:

$result = $this->getCollectionFromDB( "SELECT \`player\_id\` \`id\`, \`player\_name\` \`name\`, \`player\_score\` \`score\` FROM \`player\`" );

Result:
\[
 1234 => \[ 'id'=>1234, 'name'=>'myuser0', 'score'=>1 \],
 1235 => \[ 'id'=>1235, 'name'=>'myuser1', 'score'=>0 \]
\]

Example 2:

$result = $this->getCollectionFromDB( "SELECT \`player\_id\` \`id\`, \`player\_name\` \`name\` FROM \`player\`", true );

Result:
\[
 1234 => 'myuser0',
 1235 => 'myuser1'
\]

getNonEmptyCollectionFromDB(string $sql) array

Same as getCollectionFromDB($sdl), but raise an exception if the collection is empty. Note: this function does NOT have 2nd argument as previous one does.

getObjectFromDB(string $sql) array

Returns one row for the sql SELECT query as an associative array or null if there is no result (where fields are keys mapped to values)

Raise an exception if the query return more than one row (you can use LIMIT 1 in the query to avoid the exception)

Example:

$result = $this->getObjectFromDB( "SELECT \`player\_id\` \`id\`, \`player\_name\` \`name\`, \`player\_score\` \`score\` FROM \`player\` WHERE \`player\_id\` = '$player\_id'" );

Result:
\[
  'id'=>1234, 'name'=>'myuser0', 'score'=>1 
\]

getNonEmptyObjectFromDB(string $sql) array

Similar to previous one, but raise an exception if no row is found

getObjectListFromDB(string $sql, bool $bUniqueValue=false) array

Return an array of rows for a sql SELECT query.

The result is the same as "getCollectionFromDB" except that the result is a simple array (and not an associative array).

The result can be empty.

If you specified $bUniqueValue=true and if your SQL query request 1 field, the method returns directly an array of values.

Example 1:

$result = $this->getObjectListFromDB( "SELECT \`player\_id\` \`id\`, \`player\_name\` \`name\`, \`player\_score\` \`score\` FROM \`player\`" );

Result:
\[
 \[ 'id'=>1234, 'name'=>'myuser0', 'score'=>1 \],
 \[ 'id'=>1235, 'name'=>'myuser1', 'score'=>0 \]
\]

Example 2:

$result = $this->getObjectListFromDB( "SELECT \`player\_name\` \`name\` FROM \`player\`", true );

Result:
\[
 'myuser0',
 'myuser1'
\]

getDoubleKeyCollectionFromDB(string $sql, bool $bSingleValue=false) array

Return an associative array of associative array, from a SQL SELECT query.

First array level correspond to first column specified in SQL query.

Second array level correspond to second column specified in SQL query.

If $bSingleValue = true, keep only third column on result

  

DbGetLastId()

Return the PRIMARY key of the last inserted row (see PHP mysql\_insert\_id function).

DbAffectedRow() int

Return the number of row affected by the last operation

escapeStringForDB(string $string) string

You must use this function on every string type data in your database that contains unsafe data.

(unsafe = can be modified by a player).

This method makes sure that no SQL injection will be done through the string used, **as long as the SQL statement uses single quotes around the string. This is important!**

_Note: if you using standard types in ajax actions, like AT\_alphanum it is sanitized before arrival. This function is only needed if you manage to get an unchecked string, like in the games where user has to enter text as a response._ _Note: This function does not escape % and \_ by default, which are wildcards if used in an SQL "LIKE" statement. The developer must determine if this is desirable behavior._

  
Note: see Editing [Game database model: dbmodel.sql](/Game_database_model:_dbmodel.sql "Game database model: dbmodel.sql") to know how to define your database model.

## Use globals

Sometimes, you want a single global value for your game, and you don't want to create a DB table specifically for it.

You can do this with the BGA framework "globals". Your value will be stored in the "bga\_globals" table in the database, and you can access it with simple methods.

The keys are strings, so you might want to store them in constants to avoid mistakes (the key must be at maximum 50 characters long).

The variable can be of any type (number, string, array, object) and will be stored as a JSON (so functions cannot be stored, and circular references on objects will trigger an Exception when storing).

Having a string key and a JSON serialization allows you to debug easily by looking at the bga\_globals table content.

_Note: this globals module doesn't use cache, so every call to one of the following methods makes a DB request._

**globals->set(string $name, $mixed $obj): void**

Define the value of a global variable.

const FIRST\_PLAYER\_ID = "firstPlayerId";
$this->globals->set(FIRST\_PLAYER\_ID, array\_keys($players)\[0\]);

**globals->get(string $name, $mixed $defaultValue = null, ?string $class = null): mixed**

Get the value of a global variable. Returns \`null\` if not set, except specified otherwise in the optional \`$defaultValue\`.

$currentFirstPlayerId = $this->globals->get(FIRST\_PLAYER\_ID);
...
$selectedCardsIds = $this->globals->get(SELECTED\_CARDS\_IDS, \[\]);

You can specify the expected class type, if you save a class and don't want it returned as a stdClass.

$undo = new Undo($playerId, $moves);
$this->globals->set(UNDO, $undo);

$undo = $this->globals->get(UNDO); // will return an stdClass
$undo = $this->globals->get(UNDO, class: Undo::class); // will return an Undo class

Note: your class should be a plain object with no mandatory constructor params. Example:

class Undo {
    public function \_\_construct(
        public ?int $playerId = null,
        public ?array $moves = null,
    ) {}
}

**globals->getAll(...$names): array**

Get the value of all global variables, as a key=>value array. You can set a list of names to only get matching variables. In that case, non-existent keys will not be set in the returned array, so if you have a key with null value, it means the key has been set to null previously.

$variables = $this->globals->getAll();
...
$diceVariables = $this->globals->getAll(DIE1, DIE2);

Use with PHP's [extract()](https://www.php.net/extract) function to quickly assign multiple variables:

extract($this->globals->getAll('endTime', 'hour', 'vipWelcome'));
// $endTime, $hour, and $vipWelcome (may) now exist

if (!empty($endTime)) {
   ...

**globals->delete(...$names): void**

Delete a global variable or a list of global variables stored in database.

$this->globals->delete(SELECTED\_CARDS\_IDS);
...
$this->globals->delete(SELECTED\_CARDS\_IDS, UNDO, AFTER\_DISCARD\_RETURN\_STATE);

  
**globals->has(string $name): bool**

Indicates if a global variable is stored in database.

$cardSelectionIsStarted = $this->globals->has(SELECTED\_CARDS\_IDS);

**globals->inc(string $name, $int $inc): int**

Increments the value of a global variable, and return the incremented value. Will trigger an exception if the variable is not a numeric value.

$this->globals->inc(PLAYED\_ACTIONS\_IN\_CURRENT\_TURN, 1);
...
$totalSpent = $this->globals->inc(SPENT\_COINS\_IN\_CURRENT\_TURN, $cardCost);

## Use globals (numbers only / game options)

Before the \`$this->globals\`, you could only store numeric values as globals. Theses "GameStateValues" are stored in the "global" table in the database, and you can access it with simple methods.

All methods below are members of the game class and should be accessed via $this->

**initGameStateLabels(array $labelsMap): void**

This method should be located at the beginning of constructor of _yourgamename.game.php_. This is where you define the globals used in your game logic, by assigning them IDs.

You can define up to 80 globals, with IDs from 10 to 89 (inclusive, there can be gaps). Also you must use this method to access value of game options [Game\_options\_and\_preferences:\_gameoptions.inc.php](/Game_options_and_preferences:_gameoptions.inc.php "Game options and preferences: gameoptions.inc.php"), in that case, IDs need to be between 100 and 199. You must **not** use globals outside the range defined above, as those values are used by other components of the framework.

   function \_\_construct() {
        parent::\_\_construct();
        $this->initGameStateLabels(\[ 
                "my\_first\_global\_variable" => 10,
                "my\_second\_global\_variable" => 11,
                "my\_game\_variant" => 100
        \]);
         // other code ...
   }

NOTE: The methods below WILL throw an exception if label is not defined using the call above.

**setGameStateInitialValue( string $label, int $value ): void**

Initialize global value. This is not required if you ok with default value if 0. This should be called from setupNewGame function.

  
**getGameStateValue( string $label, int $default = 0): int**

Retrieve the value of a global. Returns $default if global is not been initialized (by setGameStateInitialValue).

NOTE: this method use globals "cache" if you directly manipulated globals table OR call this function after undoRestorePoint() - it won't work as expected.

  $value = $this->getGameStateValue('my\_first\_global\_variable');

For debugging purposes, you can have labels and value pairs send to client side by inserting that code in your "getAllDatas":

$labels = array\_keys($this->mygamestatelabels);
$result\['myglobals'\] = array\_combine($labels, array\_map(\[$this,'getGameStateValue'\],$labels));

That assumes you stored your label mapping in $this->mygamestatelabels in constructor

  $this->mygamestatelabels=\["my\_first\_global\_variable" => 10, ...\];
  $this->initGameStateLabels($this->mygamestatelabels);

**setGameStateValue( string $label, int $value ): void**

Set the current value of a global.

  $this->setGameStateValue('my\_first\_global\_variable', 42);

**incGameStateValue( string $label, int $increment ): int**

Increment the current value of a global. If increment is negative, decrement the value of the global.

Return the final value of the global. If global was not initialized it will initialize it as 0.

NOTE: this method use globals "cache" if you directly manipulated globals table OR call this function after undoRestorePoint() - it won't work as expected.

  $value = $this->incGameStateValue('my\_first\_global\_variable', 1);

  

### BGA predefined globals

BGA already defines some globals in the _global_ database table. You should not change them directly but it can be useful to know what they mean when debugging:

global\_id

label

Meaning

1

Current state

2

Active player id

3

next\_move\_id

Next move number

4

Game id

5

Table creator id

6

playerturn\_nbr

Player turn number

7

gameprogression

Game progression

8

initial\_reflexion\_time

Initial reflection time

9

additional\_reflexion\_time

Additional reflection time

200

reflexion\_time\_profile

Game speed

Real-time games:

-   0 = Real-time • Fast paced
-   1 = Real-time • Normal speed
-   2 = Real-time • Slow speed
-   9 = No time limit • with friends only

Turn-based games:

-   10 = Fast Turn-based • 24 moves per day
-   11 = Fast Turn-based • 12 moves per day
-   12 = Fast Turn-based • 8 moves per day
-   13 = Turn-based • 4 moves per day
-   14 = Turn-based • 3 moves per day
-   15 = Turn-based • 2 moves per day
-   17 = Turn-based • 1 move per day
-   19 = Turn-based • 1 move per 2 days
-   20 = No time limit • with friends only

201

bgaranking\_mode

Game mode

-   0 = Normal mode
-   1 = Friendly mode (no ELO)
-   2 = Arena mode

207

game\_language

GAMESTATE\_GAME\_LANG

300

game\_db\_version

GAMESTATE\_GAMEVERSION: Current version of the game (when in production)

301

game\_result\_neutralized

GAMESTATE\_GAME\_RESULT\_NEUTRALIZED

302

neutralized\_player\_id

GAMESTATE\_NEUTRALIZED\_PLAYER\_ID

304

undo\_moves\_stored

GAMESTATE\_UNDO\_MOVES\_STORED

305

undo\_moves\_player

GAMESTATE\_UNDO\_MOVES\_PLAYER

306

lock\_screen\_timestamp

GAMESTATE\_LOCK\_TIMESTAMP

## Game states and active players

### Activate player handling

$this->activeNextPlayer()

Make the next player active in the natural player order.

Note: you CANNOT use this method in a ACTIVE\_PLAYER or MULTIPLE\_ACTIVE\_PLAYER state. You must use a GAME type game state for this.

$this->activePrevPlayer()

Make the previous player active (in the natural player order).

Note: you CANNOT use this method in a ACTIVE\_PLAYER or MULTIPLE\_ACTIVE\_PLAYER state. You must use a GAME type game state for this.

$this->gamestate->changeActivePlayer( $player\_id )

You can call this method to make any player active.

Note: you CANNOT use this method in a ACTIVE\_PLAYER or MULTIPLE\_ACTIVE\_PLAYER state. You must use a GAME type game state for this.

$this->getActivePlayerId()

Return the "active\_player" id

Note: it does NOT mean that this player is active right now, because state type could be GAME or MULTIPLE\_ACTIVE\_PLAYER

Note: avoid using this method in a MULTIPLE\_ACTIVE\_PLAYER state because it does not mean anything.

### Multiple activate player handling

$this->gamestate->setAllPlayersMultiactive()

All playing players are made active. Update notification is sent to all players (this will trigger **onUpdateActionButtons**).

Usually, you use this method at the beginning of a game state (e.g., "stGameState") which transitions to a MULTIPLE\_ACTIVE\_PLAYER state in which multiple players have to perform some action. Do not use this method if you going to make some more changes in the active player list. (I.e., if you want to take away MULTIPLE\_ACTIVE\_PLAYER status immediately afterwards, use setPlayersMultiactive instead.)

Example of usage:

    function st\_MultiPlayerInit() {
        $this->gamestate->setAllPlayersMultiactive();
    }
    

$this->stMakeEveryoneActive()

this method can be used in state machine to make everybody active as "st" method of multiplayeractive state, it just calls $this->gamestate->setAllPlayersMultiactive()

This is to be used in state declaration:

    2 => array(
    		"name" => "playerTurnPlace",
    		"description" => \\clienttranslate('Other player must place ships'),
    		"descriptionmyturn" => \\clienttranslate('${you} must place ships (click on YOUR SHIPS board to place)'),
    		"type" => MULTIPLE\_ACTIVE\_PLAYER,
                'action' => 'stMakeEveryoneActive',
                'args' => 'arg\_playerTurnPlace',
    	     	"possibleactions" => array( "actionBla" ),
                "transitions" => array( "next" => 4, "last" => 99)
    ),
    

$this->gamestate->setAllPlayersNonMultiactive( $next\_state )

All playing players are made inactive. Transition to next state, by transition name or State class name.

$this->gamestate->setPlayersMultiactive( $players, $next\_state, $bExclusive = false )

Make a specific list of players active during a multiactive gamestate. Update notification is sent to all players whose state changed.

"players" is the array of player id that should be made active. If "players" is not empty the value of "next\_state" will be ignored (you can put whatever you want)

If "bExclusive" parameter is not set or false it doesn't deactivate other previously active players. If its set to true, the players who will be multiactive at the end are only these in "$players" array

In case "players" is empty, the method trigger the "next\_state" transition, by transition name or State class name, to go to the next game state.

returns true if state transition happened, false otherwise

$this->gamestate->setPlayerNonMultiactive( $player\_id, $next\_state )

During a multiactive game state, make the specified player inactive.

Usually, you call this method during a multiactive game state after a player did his action. It is also possible to call it directly from multiplayer action handler.

If this player was the last active player, the method trigger the "next\_state" transition, by transition name or State class name, to go to the next game state.

returns true if state transition happened, false otherwise

Example of usage (see state declaration of playerTurnPlace above):

    function actionBla($args) {
        $this->checkAction('actionBla');
        // handle the action using $this->getCurrentPlayerId()
        $this->gamestate->setPlayerNonMultiactive( $this->getCurrentPlayerId(), 'next');
    }

$this->gamestate->getActivePlayerList()

With this method you can retrieve the list of the active player at any time.

During a GAME type gamestate, it will return a void array.

During a ACTIVE\_PLAYER type gamestate, it will return an array with one value (the active player id).

During a MULTIPLE\_ACTIVE\_PLAYER type gamestate, it will return an array of the active players id.

Note: you should only use this method in the latter case.

  

$this->gamestate->updateMultiactiveOrNextState( $next\_state\_if\_none )

Sends update notification about multiplayer changes. All multiactive set\* functions above do that, however if you want to change state manually using db queries for complex calculations, you have to call this yourself after. Do not call this if you calling one of the other setters above. Next state param is a transition name or State class name.

Example: you have player teams and you want to activate all players in one team

        $sql = "UPDATE \`player\` SET \`player\_is\_multiactive\` = '0'";
        $this->DbQuery( $sql );
        $sql = "UPDATE \`player\` SET \`player\_is\_multiactive\` = '1' WHERE \`player\_id\` = '$player\_id' AND \`player\_team\` = '$team\_no'";
        $this->DbQuery( $sql );
        
        $this->gamestate->updateMultiactiveOrNextState( 'error' );

updating database manually

Use this helper function to change multiactive state without sending notification

    /\*\*
     \* Changes values of multiactivity in db, does not sent notifications.
     \* To send notifications after use updateMultiactiveOrNextState
     \* @param number $player\_id, player id <=0 or null - means ALL
     \* @param number $value - 1 multiactive, 0 non multiactive
     \*/
    function dbSetPlayerMultiactive($player\_id = -1, $value = 1) {
        if (! $value)
            $value = 0;
        else
            $value = 1;
        $sql = "UPDATE \`player\` SET \`player\_is\_multiactive\` = '$value' WHERE \`player\_zombie\` = 0 and \`player\_eliminated\` = 0";
        if ($player\_id > 0) {
            $sql .= " AND \`player\_id\` = $player\_id";
        }
        $this->DbQuery($sql);
    }

$this->gamestate->isPlayerActive($player\_id)

Return true if specified player is active right now.

This method take into account game state type, ie nobody is active if game state is "game" and several players can be active if game state is "multiplayer"

$this->bIndependantMultiactiveTable

This flag can be set to true in constructor of game.php to force creation of second table to handle multiplayer states (normally these are in player table), this is very advanced feature.

ONLY use it after you deploy you game to production if you receive unusual amount of bug report with dead lock symptoms DURING multiactiveplayer states

   function \_\_construct() {
     ...
     $this->bIndependantMultiactiveTable=true;
   }

### States functions

$this->gamestate->nextState( $transition )

Change current state to a new state. Important: the $transition parameter is the name of the transition (or the state class name), and NOT the name of the target game state, see [Your game state machine: states.inc.php](/Your_game_state_machine:_states.inc.php "Your game state machine: states.inc.php") for more information about states.

_with State classes, you should not need it and you can just return the class name of the next state._

**$this->gamestate->jumpToState($stateNum)**

Change current state to a new state. Important: the $stateNum parameter is the key of the state. See [Your game state machine: states.inc.php](/Your_game_state_machine:_states.inc.php "Your game state machine: states.inc.php") for more information about states.

Note: this is very advanced method, it should not be used in normal cases. Specific advanced cases include - jumping to specific state from "do\_anytime" actions, jumping to dispatcher state or jumping to recovery state from zombie player function

_with State classes, you should not need it and you can just return the class name of the next state._

$this->checkAction( $actionName, $bThrowException=true )

Check if the current player can perform a specific action in the current game state, and optionally throw an exception if they can't.

The action is valid if it is listed in the "possibleactions" array for the current game state (see game state description).

This method MUST be the first one called in ALL your PHP methods that handle player actions that are not using [Action autowiring](/Main_game_logic:_yourgamename.game.php#Actions_\(autowired\) "Main game logic: yourgamename.game.php"), in order to make sure a player doesn't perform an action not allowed by the rules at the point in the game. It should not be called from methods where the current player is not necessarily the active player, otherwise it may fail with an "It is not your turn" exception.

If "bThrowException" is set to "false", the function returns **false** in case of failure instead of throwing an exception. This is useful when several actions are possible, in order to test each of them without throwing exceptions.

_With auto-wired actions, you should not need it._

$this->gamestate->checkPossibleAction( $action )

(rarely used)

This works exactly like "checkAction" (above), except that it does NOT check if the current player is active.

**Note: This does NOT check either spectator or eliminated status, so those checks must be done manually.**

This is used specifically in certain game states when you want to authorize additional actions for players that are not active at the moment.

Example: in _Libertalia_, you want to authorize players to change their mind about the card played. They are of course not active at the time they change their mind, so you cannot use "checkAction"; use "checkPossibleAction" instead.

This is how PHP action looks that returns player to active state (only for multiplayeractive states). To be able to execute this on client do not call checkAction on js side for this specific action.

  function actionUnpass() {
       $this->gamestate->checkPossibleAction('actUnpass'); // player changed mind about passing while others were thinking
       $this->gamestate->setPlayersMultiactive(array ($this->getCurrentPlayerId() ), 'error', false);
   }

  

$this->gamestate->getCurrentMainState()

Returns the current main state, ignoring private parallel states.

To get the state as an array, use $this->gamestate->getCurrentMainState()->toArray() and it will get a result similar to $this->gamestate->state()

$this->gamestate->getCurrentMainStateId()

Returns the current main state id, ignoring private parallel states. (previously, it was the state\_id() function, that returned the state numer as a string).

$this->gamestate->getCurrentState(int $playerId)

Returns the current state for a player. If the player is in private parallel state, it means the current private state for this player.

To get the state as an array, use $this->gamestate->getCurrentState($playerId)->toArray()

$this->gamestate->getCurrentStateId(int $playerId)

Returns the current state id for a player. If the player is in private parallel state, it means the current private state for this player.

$this->gamestate->isMultiactiveState()

Return true if we are in MULTIPLE\_ACTIVE\_PLAYER state, false otherwise

### Private parallel states

See the overview of private parallel states [here](/Your_game_state_machine:_states.inc.php#Private_parallel_states "Your game state machine: states.inc.php").

$this->gamestate->initializePrivateStateForAllActivePlayers()

All active players in a multiactive state are entering a first private state defined in the master state's initialprivate parameter.

Every time you need to start a private parallel states you need to call this or similar methods below.

Note: at least one player needs to be active (see [above](#Multiple_activate_player_handling)) and current game state must be a multiactive state with initialprivate parameter defined

Note: initialprivate parameter of master state should be set to the id of the first private state. This private state needs to be defined in states.php with the type set to 'private'.

Note: this method is usually preceded with activating some or all players

Note: initializing private state can run action or args methods of the initial private state

Example of usage:

    function stStartPlayerTurn() {
        // This is usually done in master state action method
        
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->initializePrivateStateForAllActivePlayers();

        // in some cases you can move immediately some or all players to different private states
        if ($someCondition) {
            //move all players to different state 
            $this->gamestate->nextPrivateStateForAllActivePlayers("some\_transition");
        }

        if ($other condition) {
            //move single player to different state
            $this->gamestate->nextPrivateState($specificPlayerId, "some\_transition");
        }
    }
    

$this->gamestate->initializePrivateStateForPlayers($playerIds)

Players with specified ids are entering a first private state defined in the master state initialprivate parameter.

Same considerations apply as for the method above.

$this->gamestate->initializePrivateState($playerId)

Player with the specified id is entering a first private state defined in the master state initialprivate parameter.

Everytime you need to start a private parallel states you need to call this or similar methods above

Note: player needs to be active (see [above](#Multiple_activate_player_handling)) and current game state must be a multiactive state with initialprivate parameter defined

Note: initialprivate parameter of master state should be set to the id of the first private state. This private state needs to be defined in states.php with the type set to 'private'.

Note: this method is usually preceded with activating that player

Note: initializing private state can run action or args methods of the initial private state

Example of usage:

    function st\_ChangeMind() {
        // This player finished his move before, but now decides change something while other players are still active
        // We activate the player and initialize his private state
        $this->gamestate->setPlayersMultiactive(\[$this->getCurrentPlayerId()\], "");
        $this->gamestate->initializePrivateState(this->getCurrentPlayerId());

        // It is also possible to move the player to some other specific state immediately
        $this->gamestate->nextPrivateState($this->getCurrentPlayerId(), "some\_transition");
    }
    

$this->gamestate->nextPrivateStateForAllActivePlayers($transition)

All active players will transition to next private state by specified transition

Note: game needs to be in a master state which allows private parallel states

Note: transition should lead to another private state (i.e. a state with type defined as 'private'

Note: transition should be defined in private state in which the players currently are, or be a state class name.

Note: this method can run action or args methods of the target state

Note: this is usually used after initializing the private state to move players to specific private state according to the game logic

Example of usage:

    function stStartPlayerTurn() {
        // This is usually done in master state action method
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->initializePrivateStateForAllActivePlayers();

        if ($specificOption) {
            //move all players to different state 
            $this->gamestate->nextPrivateStateForAllActivePlayers("some\_transition");
        }
    }
    

$this->gamestate->nextPrivateStateForPlayers($playerIds, $transition)

Players with specified ids will transition to next private state specified by provided transition, by transition name or State class name.

Same considerations apply as for the method above.

$this->gamestate->nextPrivateState($playerId, $transition)

Player with specified id will transition to next private state specified by provided transition, by transition name or State class name.

Note: game needs to be in a master state which allows private parallel states

Note: transition should lead to another private state (i.e. a state with type defined as 'private'

Note: transition should be defined in private state in which the players currently are.

Note: this method can run action or args methods of the target state for specified player

Note: this is usually used after some player actions to move to next private state

Example of usage:

    function actSomeAction() {
        $this->checkAction("actSomeAction"); //needs to be defined in the current state
        $this->gamestate->nextPrivateState($this->getCurrentPlayerId(), "some\_transition");
    }
    

$this->gamestate->unsetPrivateStateForAllPlayers()

All players private state will be reset to null, which means they will get out of private parallel states and be in a master state like the private states are not used

Note: game needs to be in a master state which allows private parallel states

Note: this is usually used to clean up after leaving a master state in which private states were used, but can be used in other cases when we want to exit private parallel states and use a regular multiactive state for all players

Note: After unseting private state only actions on master state are possible

Note: Usually it is not necessary to unset private states as they will be initialized to first private state when private states are needed again. Nevertheless it is generally better to clean private state after exiting private parallel states to avoid bugs.

Example of usage:

    function stNextRound() {
        $this->gamestate->unsetPrivateStateForAllPlayers();
    }
    

$this->gamestate->unsetPrivateStateForPlayers($playerIds, $transition)

For players with specified ids private state will be reset to null, which means they will get out of private parallel states and be in a master state like the private states are not used.

Same considerations apply as for the method above.

$this->gamestate->unsetPrivateState($playerId)

For player with specified id private state will be reset to null, which means they will get out of private parallel states and be in a master state like the private states are not used

Note: game needs to be in a master state which allows private parallel states

Note: this is usually used when deactivating player to clean up their parallel state

Note: After unseting private state only actions on master state are possible

Note: Usually it is not necessary to unset private state as it will be initialized to first private state when private states are needed again. Nevertheless it is generally better to clean private state when not needed to avoid bugs.

Example of usage:

    function done() {
        $this->gamestate->setPlayerNonMultiactive( $this->getCurrentPlayerId(), "newTurn" );
        $this->gamestate->unsetPrivateState($this->getCurrentPlayerId());
    }
    

$this->gamestate->setPrivateState($playerId, $newStateId)

For player with specified id a new private state would be set

Note: game needs to be in a master state which allows private parallel states

Note: this should be rarely used as it doesn't check if the transition is allowed (it doesn't even specifies transition). This can be useful in very complex cases when standard state machine is not adequate (i.e. specific cards can lead to some micro action in various states where defining transitions back and forth can become very tedious.) NewStateId can also be a state class name.

Note: this method can run action or args methods of the target state for specified player

Example of usage:

    function actSomeAction() {
        $this->checkAction("actSomeAction"); //needs to be defined in the current state

        if ($playerHaveSpecificCard)
            return $this->gamestate->setPrivateState($this->getCurrentPlayerId(), 35);

        $this->gamestate->nextPrivateState($this->getCurrentPlayerId(), "some\_transition");        
    }
    

$this->gamestate->getPrivateState($playerId)

This return the private state or null if not initialized or not in private state

#### State Arguments in Private parallel states

The args method called for private states will have the player\_id passed to it, allowing you to customise the arguments returned for that player.

Example of usage:

    function argMyPrivateState($player\_id) {
        return array(
          'my\_data' => $this->getPlayerSpecificData($player\_id)
        );
    }
    

#### Inactive Players

During Private Parallel State, active players will be managed by the private state that is current assigned to them.

Inactive players will be managed by the master MULTIPLE\_ACTIVE\_PLAYER state, so your client should respond to that state in order to display any status message advising players that they are waiting for others to have their turn, or to add any buttons that allow players to potentially "break in" and become active.

## Actions (autowired)

The action function should be prefixed by "act" and match the names specified in the "possibleactions" field on the state file.

If your actions start with "act", they will be autowired, that means you can declare action functions on the game.php file and call them directly from the front (with bgaPerformAction). You don't need to use action.php file anymore. The query param from the front request will be matched with the PHP variable of the same name, and it needs to be properly typed. This works for basic types: int, bool, float, string.

public function actPlayCard(int $cardId) { ... }

This function can be called from front-side with

this.bgaPerformAction('actPlayCard', {
  cardId: this.selectedCardId // the "cardId" param match the PHP variable name
});

(Note: if you also declare the function in the action.php, it will be used instead of the autowiring)

If you want more complex types like int array or JSON, you'll need to specify it using the Param attributes. Same if the PHP variable name is different from the front query param, or if you want to specify the tests that should be realized on the parameter before calling the function.

### Possible attributes

**BoolParam(?string $name = null)**

use \\Bga\\GameFramework\\Actions\\Types\\BoolParam;

...

public function actRaiseBet(#\[BoolParam(name: 'raise')\] bool $raiseBet)

public function actRaiseBet(#\[BoolParam(name: 'raise')\] bool $raise) // NOTE: it's the same as actRaiseBet(bool $raise) if PHP var name and param name are the same

**IntParam(?string $name = null, public ?int $min = null, public ?int $max = null)**

use \\Bga\\GameFramework\\Actions\\Types\\IntParam;

...

public function actPlayCard(#\[IntParam(name: 'id')\] int $cardId)

public function actSpendGold(#\[IntParam(min: 1)\] int $gold) // will trigger an exception if param is < 1

public function actPlaceCardOnSpot(int $cardId, #\[IntParam(min: 1, max: 5)\] int $spot) // will trigger an exception if spot is not in 1 to 5 range

**FloatParam(?string $name = null, public ?int $min = null, public ?int $max = null)**

Works the same way as IntParam, import is `use \Bga\GameFramework\Actions\Types\FloatParam;`

**StringParam(?string $name = null, public ?bool $alphanum = false, public ?bool $alphanum\_dash = false, public ?bool $base64 = false, public ?array $enum)**

use \\Bga\\GameFramework\\Actions\\Types\\StringParam;

...

public function actSubmitWord(#\[StringParam(alphanum: true)\] string $word) // will trigger an exception if the word is not alphanum

public function actChooseAction(#\[StringParam(enum: \['move', 'attack', 'pass'\])\] string $action)  // will trigger an exception if the parameter doesn't match a value in the enum

Note: You should do one of the tests of the attribute, or test it yourself on the function, to ensure the user is not sending forbidden characters to the function.

**IntArrayParam(?string $name = null, public ?int $min = null, public ?int $max = null)**

use \\Bga\\GameFramework\\Actions\\Types\\IntArrayParam;

...

public function actDiscardCards(#\[IntArrayParam()\] array $ids)

public function actDiscardCards(#\[IntArrayParam(min: 2, max: 8)\] array $ids)  // will trigger an exception if the array length is not in the 2 to 8 range. It doesn't check the min/max of the array values!

This function can be called from front-side with

const selectedCardIds = \[8, 12, 25\];
this.bgaPerformAction('actDiscardCards', {
  ids: selectedCardIds.join(',')
});

**JsonParam(?string $name = null, public ?bool $associative = true, public ?bool $alphanum = true)**

use \\Bga\\GameFramework\\Actions\\Types\\JsonParam;

...

public function actPlanComplexStuff(#\[JsonParam()\] array $answer) {} // force conversion to array during PHP json decode
public function actPlanComplexStuff(#\[JsonParam(associative: false)\] object $answer) {} // force conversion to object during PHP json decode

public function actPlanComplexStuff(#\[JsonParam(associative: null)\] mixed $answer) {} // default PHP json decode

On client side:

     const lala ={a: 2, b: "string", c: \[1,2,4\]};
     this.bgaPerformAction("actPlanComplexStuff", {
       answer: JSON.stringify(lala)
     });

### checkAction

The autowiring also triggers the checkAction, so you don't have to call it at the beginning of the function. If you need a function that should not trigger checkAction (an action that can be played during someone else turn), you can disable it this way:

use \\Bga\\GameFramework\\Actions\\CheckAction;


#\[CheckAction(false)\]
public function actSetAutopass(bool $autopass) { ... }

#\[CheckAction(false)\]
public function actCancelChooseAction() {
  $this->gamestate->checkPossibleAction('actCancelChooseAction');
  ...
}

If you disable `checkAction`, you probably need to call `$this->gamestate->checkPossibleAction` instead at the beginning of your function. You likely also want to disable client checkAction calls implied by bgaPerformAction.

### act prefix

The act prefix needed to activate the autowiring is not just to have some consistency in naming, it's also intended to protect your code. If we let autowired action to any function, a player may do a request to 'setPlayerScore' function, even if you never intended for it to be called front side, and it would allow almost undetectable cheating for anyone reading the code of your game.

## Players turn order

When table is created the "natural" player order is assigned to player at random, and stored in "read-only" field "player\_no". If you need to create a custom order you should never change natural order but have a separate data structure. For example you can alter the players table to add another "custom\_order" field, you can use state globals or you can use your natural board database, to store meeple\_color/position\_location pair. BGA currently does not provide any API to create/store custom player order.

**getNextPlayerTable()**

Return an associative array which associate each player with the next player around the table.

In addition, key 0 is associated to the first player to play.

Example: if three player with ID 1000, 2000 and 3000 are around the table, in this order, the method returns:

   array( 
    1000 => 2000, 
    2000 => 3000, 
    3000 => 1000, 
    0 => 1000 
   );

**getPrevPlayerTable()**

Same as above, but the associative array associate the previous player around the table. However there no 0 index here.

**getPlayerAfter( $player\_id )**

Get player playing after given player in natural playing order.

**getPlayerBefore( $player\_id )**

Get player playing before given player in natural playing order.

Note: There is no API to modify this order, if you have custom player order you have to maintain it in your database and have custom function to access it.

**createNextPlayerTable( $players, $bLoop=true )**

Using $players array creates a map of current => next as in example from getNextPlayerTable(), however you can use custom order here. If parmeter $bLoop is set to true then last player will points to first (creaing a loop), false otherwise. In any case index 0 points to first player (first element of $players array). $players is array of player ids in desired order.

Note: This function **DOES NOT** change the order in database, it only creates a map using key/values as descibed.

Example of usage:

    function getNextPlayerTableCustom() {
        $starting = $this->getStartingPlayer(); // custom function to get starting player
        $player\_ids = $this->getPlayerIdsInOrder($starting); // custom function to create players array starting from starting player
        return $this->createNextPlayerTable($player\_ids, false); // create next player table in custom order
    }

  

$table = $this->createNextPlayerTable(\[3000,2000,1000\], false);

will return:
   \[ 
    3000 => 2000, 
    2000 => 1000, 
    1000 => null,
    0 => 3000 
   \]

## Notifications

To understand notifications, please read [The BGA Framework at a glance](http://www.slideshare.net/boardgamearena/the-bga-framework-at-a-glance) first.

**IMPORTANT**

Notifications are crutial part of BGA framework. Everything which players see, including all changes which happen on frontend (including setup), are done via notfications.

Some notifications are built into the framework, and others are custom, game specific notifications. Generally, you should not send or handle framework notifications, except exposed APIs.

Example of framework notifications:

-   game setup (first time client connects to game)
-   game state change
-   active player change

Example of custom notifications:

-   game piece moved
-   player scored points

Notifications are queued, i.e. sent at the very end of the action, when it ends normally. It means that if you throw an exception for any reason (i.e: move is not allowed), no notifications will be sent to players.

Notification can be sent from the following functions (and transive calls):

-   action handler (act\* in State classes or Game.php). It is a MUST actually. At least one notification must be sent (but it includes state transition which sends it)
-   game state transition (onEnteringState in State classes)
-   setupNewGame (after player tables setup is finished)

Notification cannot be sent from the following:

-   .view.php
-   .action.php
-   material.inc.php
-   constructor of .game.php
-   arg\* methods of .game.php

There are conceptually two types of notification:

-   public - sent to all clients who are listening - that includes all players in the table as well as spectators
-   private - sent only to a single player

The bundle of notifications sent at the end of an action is considered a single "move" and the table's move counter increases. If you are ONLY sending private notifications during action handling, they will not have an associated `move_id` (to avoid this, add simple public notification with empty message). In the rare case that you want to change this behavior, you can apply some hackery described in [BGA\_Studio\_Cookbook](/BGA_Studio_Cookbook "BGA Studio Cookbook")

Note: the total notification size is now limited to 128K. It seems a lot, but don't forget that notification are bundled and only send at the end of an action which also INCLUDES all game state transitions that follow.

The notifications are handled on JS side by subscribing to notifications (only do it for custom notifications!). See \[Game\_interface\_logic:\_yourgamename.js#Notifications\]

### Notify

**notify->all(string $notification\_type,string | NotificationMessage $message,array $notification\_args )**

Send a notification to all players of the game and spectators (public).

-   notification\_type: A string that defines the type of your notification.

Your game interface Javascript logic will use this to know what is the type of the received notification (and to trigger the corresponding method).

-   message: A string that defines what is to be displayed in the game log.

You can use an empty string here (_). In this case, nothing is displayed in the game log._

Unless its empty, use "\\clienttranslate" method to make sure string is translated.

You can use arguments in your $notification\_log string, that refers to values defines in the "$notification\_args" argument (see below). Note: Make sure you only use single quotes ('), otherwise PHP will try to interpolate the variable and will ignore the values in the args array.

-   notification\_args: The arguments of your notifications, as an associative array.

This array will be transmitted to the game interface logic, in order the game interface can be updated.

Complete notify all example (from "Reversi"):

$this->notify->all( "playDisc", \\clienttranslate( '${player\_name} plays a disc and turns over ${returned\_nbr} disc(s)' ),
 array(
        'player\_id' => $player\_id,
        'player\_name' => self::getActivePlayerName(),
        'returned\_nbr' => count( $turnedOverDiscs ),
        'x' => $x,
        'y' => $y
     ) );

You can see in the example above the use of the "\\clienttranslate" method, and the use of 2 arguments "player\_name" and "returned\_nbr" in the notification log.

**Important**: NO private data must be sent with this method, as a cheater could see it even if it is not used explicitly by the game interface logic. If you want to send private information to a player, please use notify->player below.

**Important**: this array is serialized to be sent to the browsers, and will be saved with the notification to be able to replay the game later. If it is too big, it can make notifications slower / less reliable, and replay archives very big (to the point of failing). So as a general rule, you should send only the minimum of information necessary to update the client interface with no overhead in order to keep the notifications as light as possible.

**Important**: When the game page is reloaded (i.e. F5 or when loading turn based game) all previous notifications are replayed as history notifications. These notifications do not trigger notification handlers and are used basically to build the game log. Because of that most of the notification arguments (except i18n, player\_id and all arguments referenced in the message), are removed from these history notifications. If you need additional arguments in history notifications you can add special field **preserve** to notification arguments, like this:

$this->notify->all( "playDisc", \\clienttranslate( '${player\_name} plays a disc and turns over ${returned\_nbr} disc(s)' ),
 array(
        'player\_id' => $player\_id,
        'player\_name' => self::getActivePlayerName(),
        'returned\_nbr' => count( $turnedOverDiscs ),
        'x' => $x,
        'y' => $y,
        'preserve' => \[ 'x', 'y' \]
     ) );

In this example, fields x and y will be preserved when replaying history notification at the game load.

NOTE: The ONLY reason 'preserve' is useful if you have custom method to render notifications in game log which changes some text arguments into html (i.e. to insert the images instead of plain text). Do not use preserve "just in case" - it will only bloat the logs and make game load VERY slow.

**Important**: If both public and private notifications are sent to the same player in the same action (AJAX call), they will initially appear in the log in the order in which they were called, but they are placed into the game log in the following order: All private notifications first, then all public notifications. This means that when the page is refreshed, or when a player loads an asynchronous game, if you have called any public notifications _before_ the last private notification, they will appear out of order in the log.

#### HTML in Notifications

You CAN use some HTML inside your notification log, however it not recommended for many reasons:

-   Its bad architecture, ui elements leak into server now you have to manage ui in many places
-   If you decided to change something in ui in a future version, old games replay and tutorials may not work, since they use stored notifications (for example if the change occured during the game and later this game is used for tutorial)
-   When you read log preview for old games its unreadable (this is log before you enter the game replay, useful for troubleshooting or game analysis)
-   Its more data to transfer and store in db
-   Its nightmare for translators, at least don't put HTML tags inside the "\\clienttranslate" method. You can use a notification argument instead, and provide your HTML through this argument.

If you still want to have pretty pictures in the log check this [BGA\_Studio\_Cookbook#Inject\_images\_and\_styled\_html\_in\_the\_log](/BGA_Studio_Cookbook#Inject_images_and_styled_html_in_the_log "BGA Studio Cookbook").

#### Recursive Notifications

If your notification contains some phrases that build programmatically you may need to use recursive notifications. In this case the argument can be not only the string but an array itself, which contains 'log' and 'args', i.e.

 $this->notify->all('message', \\clienttranslate('Game moves ${token\_name\_rec}'),
                  \['token\_name\_rec' => \['log' => '${token\_name} #${token\_number}',
                                      'args'=> \['token\_name' => \\clienttranslate('Boo'), 'token\_number' => $number, 'i18n' => \['token\_name'\] \]
                                     \]
                  \]);

  

#### Excluding some players

Sometimes you want to notify all players of a message but not have it appear in the log of specific players (for example, have every player see "Player X draws a card" but have Player X will get a private notification "You draw the Ace of Spades", so you want them not to see the public one).

To send a notification to all players but have some clients ignore it, send it as normal from the server, but implement **setIgnoreNotificationCheck** on the client to ignore the message under given conditions. See the [Game\_interface\_logic:\_yourgamename.js#Ignoring\_notifications](/Game_interface_logic:_yourgamename.js#Ignoring_notifications "Game interface logic: yourgamename.js") documentation for more details. Note: do not send private info with such notification, hiding something on client side is not hacker safe.

#### Using player names

The variable for player name must be ${player\_name} in order to be highlighted with the player color in the game log, it has to have matching 'player\_id' argument in $notification\_args. If you want a second player name in the log, name the variable ${player\_name2} and $player\_id2, etc. For the multiple player case, usage of player\_id and player\_name is not mandatory i.e. skipping to player\_id1 and player\_name1 is fine.

Special handling of arguments:

-   ${player\_name} - this will be wrapped in html and text shown using color of the corresponding player, some colors also have reserved background. This will apply recursively as well.
-   ${player\_name1}, ${player\_name2}, ${player\_name3}, etc. work the same as above

In order for this to work you must pass corresponding player\_idX AND player\_nameX in args i.e.

   $this->notify->all(
     'buyAndPassCard',
     \\clienttranslate('${player\_name} buys ${card\_name} and passes it to ${player\_name2}'),
     \[
       'player\_id' => $player1\['id'\],
       'player\_name' => $player1\['name'\],
       'player\_id2' => $player2\['id'\],
       'player\_name2' => $player2\['name'\],
       ...
     \]
   );

Note: player\_name is not translatable, never add it to i18n array

### NotifyPlayer

**notify->player(int $player\_id, string $notification\_type, string | NotificationMessage $message, $notification\_args )**

Same as above, except that the notification is sent to one player only. The player must be the player at the game table.

This method must be used each time some private information must be transmitted to a player.

Important: the variable for player name must be ${player\_name} in order to be highlighted with the player color in the game log. Since its a private notification it may be more appropriate to use "you" insted

  

 $this->notify->player($player\_id,'message',\\clienttranslate('You draw ${card\_name}'), \[ ... \]);

or

 $this->notify->player($player\_id,'message',\\clienttranslate('${player\_name} draws ${card\_name}'), \[ 'player\_name' => $this->getPlayerNameById($player\_id), ... \]);

  
Note: Spectators cannot be notified using this method, because their player ID is not available via loadPlayersBasicInfos() or otherwise. You must use notify->all() for any notification that spectators should get.

### Notification decorators

**notify->addDecorator(callable $fn)**

Add a decorator function that will be called on notification args before each call of notify->all and notify->player. _Note: it won't work with deprecated functions notifyAllPlayers and notifyPlayer._

To avoid duplicating the code in your notification args, you can register decorators in your Game \_\_construct function.

// in construct :
$this->notify->addDecorator(fn(string $message, array $args) => $this->decoratePlayerNameNotifArg($message, $args));
// could also be written this (ugly) way : $this->notify->addDecorator(\[$this, 'decoratePlayerNameNotifArg'\]);

// with the Utils functions
public function decoratePlayerNameNotifArg(string $message, array $args): array {
    // if the notif message contains ${player\_name} but it isn't set in the args, add it on args from $args\['player\_id'\]
    if (isset($args\['player\_id'\]) && !isset($args\['player\_name'\]) && str\_contains($message, '${player\_name}')) {
        $args\['player\_name'\] = $this->getPlayerNameById($args\['player\_id'\]);
    }
    return $args;
}

With this example, you can call `$this->notify->all("pass", clienttranslate('${player_name} passes'), [ "player_id" => $player_id ]);` and the front will receive the player\_name arg along the player\_id one.

  
You can also declare it as an inline function :

// in construct :
this->notify->addDecorator(function(string $message, array $args) {
    // if the notif message contains ${card\_name} but it isn't set in the args, add it on args from $args\['card'\]
    // also set it up for translations by adding it to the i18n array
    if (isset($args\['card'\]) && !isset($args\['card\_name'\]) && str\_contains($message, '${card\_name}')) {
        $args\['card\_name'\] = $args\['card'\]->name;
        $args\['i18n'\]\[\] = \['card\_name'\];
    }
    return $args;
});

With this example, you can call `$this->notify->all("playCard", clienttranslate('${player_name} plays ${card_name}'), [ "player_id" => $player_id, card => $card ]);` and the front will receive the card\_name arg with the flag to translate it.

## Randomization

A large number of board games rely on random, most often based on dice, cards shuffling, picking some item in a bag, and so on. This is very important to ensure a high level of randomness for each of these situations.

Here's are a list of techniques you should use in these situations, from the best to the worst.

### Dice and bga\_rand

**\\bga\_rand( min, max )** This is a BGA framework function that provides you a random number between "min" and "max" (inclusive), using the best available random method available on the system.

This is the preferred function you should use, because we are updating it when a better method is introduced.

As of now, bga\_rand is based on the PHP function "random\_int", which ensures a cryptographic level of randomness.

In particular, it is **mandatory** to use it for all **dice throw** (ie: games using other methods for dice throwing will be rejected by BGA during review).

Note: rand() and mt\_rand() are deprecated on BGA and should not be used anymore, as their randomness is not as good as "bga\_rand".

### Arrays

PHP's [array\_rand()](https://www.php.net/array_rand) and [shuffle()](https://www.php.net/shuffle) functions are not cryptographically secure. Instead, the following code based on PHP's [random\_int()](https://www.php.net/random_int) provides a better method to choose a random key, value, or slice of an array. (the slice preserves keys)

    private function getRandomKey(array $array)
    {
        $size = count($array);
        if ($size == 0) {
            trigger\_error("getRandomKey(): Array is empty", E\_USER\_WARNING);
            return null;
        }
        $rand = random\_int(0, $size - 1);
        $slice = array\_slice($array, $rand, 1, true);
        foreach ($slice as $key => $value) {
            return $key;
        }
    }

    private function getRandomValue(array $array)
    {
        $size = count($array);
        if ($size == 0) {
            trigger\_error("getRandomValue(): Array is empty", E\_USER\_WARNING);
            return null;
        }
        $rand = random\_int(0, $size - 1);
        $slice = array\_slice($array, $rand, 1, true);
        foreach ($slice as $key => $value) {
            return $value;
        }
    }

    private function getRandomSlice(array $array, int $count)
    {
        $size = count($array);
        if ($size == 0) {
            trigger\_error("getRandomSlice(): Array is empty", E\_USER\_WARNING);
            return null;
        }
        if ($count < 1 || $count > $size) {
            trigger\_error("getRandomSlice(): Invalid count $count for array with size $size", E\_USER\_WARNING);
            return null;
        }
        $slice = \[\];
        $randUnique = \[\];
        while (count($randUnique) < $count) {
            $rand = random\_int(0, $size - 1);
            if (array\_key\_exists($rand, $randUnique)) {
                continue;
            }
            $randUnique\[$rand\] = true;
            $slice += array\_slice($array, $rand, 1, true);
        }
        return $slice;
    }

### shuffle and cards shuffling

To shuffle items, like a pile of cards, the best way is to use the BGA PHP [Deck](/Deck "Deck") component and to use "shuffle" method. This ensures that the best available shuffling method is used, and that if in the future we improve it your game will be up to date.

As of now, the Deck component shuffle method is based on PHP "shuffle" method, which has quite good randomness (even if not as good as bga\_rand). In consequence, we accept other shuffling methods during reviews, as long as they are based on PHP "shuffle" function (or similar, like "array\_rand").

### Other methods

Mysql "RAND()" function has not enough randomness to be a valid method to get a random element on BGA. This function has been used in some existing games and has given acceptable results, but now it should be avoided and you should use other methods instead.

## Game statistics

There are 2 types of statistics:

-   a "player" statistic is a statistic associated to a player
-   a "table" statistic is a statistic not associated to a player (global statistic for this game).

See [Game statistics: stats.inc.php](/Game_statistics:_stats.inc.php "Game statistics: stats.inc.php") to see how you define statistics for your game.

  
**tableStats->init(string|array $nameOrNames, int|float|bool $value)**

**playerStats->init(string|array $nameOrNames, int|float|bool $value, bool $updateTableStat = false)**

_previously : initStat( $table\_or\_player, $name, $value)_

Create a statistic entry with a default value.

This method must be called for each statistic of your game, in your setupNewGame method. If you neglect to call this for a statistic, and also do not update the value during the course of a certain game using xxxStats->set or xxxStats->inc, the value of the stat will be undefined rather than 0. This will result in it being ignored at the end of the game, as if it didn't apply to that particular game, and excluded from cumulative statistics. As a consequence - if do not want statistic to be applied, do not init it, or call set or inc on it.

'$nameOrNames' is the name of your statistic, as it has been defined in your stats.inc.php file, or an array of those names.

'$value' is the initial value of the statistic.

'updateTableStat' for playerStats initiates the tableStats of the same name, so if you store for example 'playedTurns' in both 'table' and 'player' call only `playerStats->init('playedTurns', 0, true)` and it will init for both 'table' and 'player'.

  
**tableStats->set(string $name, int|float|bool $value)**

**playerStats->set(string $name, int|float|bool $value, int $player\_id)**

**playerStats->setAll(string $name, int|float|bool $value)** (same as set, but for all players at once)

_previously : setStat( $value, $name, $player\_id = null )_

Set a statistic $name to $value.

**tableStats->inc(string $name, int|float $delta)**

**playerStats->inc(string $name, int|float $delta, int $player\_id, bool $updateTableStat = false)**

**playerStats->incAll(string $name, int|float $delta)** (same as inc, but for all players at once)

_previously : incStat( $delta, $name, $player\_id = null )_

Increment (or decrement) specified statistic value by $delta value. Same behavior as setStat function, except you cannot increment boolean values. 'updateTableStat' will also inc the tableStats of the same name.

**tableStats->get(string $name): int|float|bool**

**playerStats->get(string $name, int $player\_id): int|float|bool**

_previously : getStat( $name, $player\_id = null )_

Return the value of statistic specified by $name. Useful when creating derivative statistics such as average.

**playerStats->getAll(string $name): array**

Returns the values for all players, as an associative array $player\_id => $value

## Translations

See [Translations](/Translations "Translations")

## Manage player scores and Tie breaker

### Normal scoring

At the end of the game, players automatically get a rank depending on their score: the player with the biggest score is #1, the player with the second biggest score is #2, and so on...

During the game, you update player's score using $this->playerScore.

Examples:

  // +2 points to active player
  $this->playerScore->inc($activePlayerId, 2);

  // Set score of active player to 5
  $this->playerScore->set($activePlayerId, 5);

Note: don't forget to notify the client side in order the score control can be updated accordingly.

### Tie breaker

Tie breaker is used when two players get the same score at the end of a game.

Tie breaker is using $this->playerScoreAux. It is updated exactly like $this->playerScore.

Tie breaker score is displayed only for players who are tied at the end of the game. Most of the time, it is not supposed to be displayed explicitly during the game.

When you are using "playerScoreAux" functionality, you must describe the formula to use in your gameinfos.inc.php file like this:

         'tie\_breaker\_description' => totranslate("Describe here your tie breaker formula"),

This description will be used as a tooltip to explain to players how this auxiliary score has been calculated.

See also [Multiple Tie Breaker Management](https://en.doc.boardgamearena.com/Game_meta-information:_gameinfos.inc.php#Multiple_tie_breaker_management).

### Co-operative game

To make everyone win/lose together in a full-coop game:

Add the following in gameinfos.inc.php : 'is\_coop' => 1, // full cooperative

Assign a score of zero to everyone if it's a loss. Assign the same score > 0 to everyone if it's a win.

### Semi-coop

If the game is not full-coop, then everyone loses = everyone is tied. I.e. set score to 0 to everybody.

### Only "winners" and "losers"

For some games, there is only a group (or a single) "winner", and everyone else is a "loser", with no "end of game rank" (1st, 2nd, 3rd...).

Examples:

-   Coup
-   Not Alone
-   Werewolves
-   Quantum

In this case:

-   Set the scores so that the winner has the best score, and the other players have the same (lower) score.
-   Add the following lines to gameinfos.inc.php:

// If in the game, all losers are equal (no score to rank them or explicit in the rules that losers are not ranked between them), set this to true 
// The game end result will display "Winner" for the 1st player and "Loser" for all other players
'losers\_not\_ranked' => true,

Werewolves and Coup are implemented like this, as you can see here:

-   [https://boardgamearena.com/#!gamepanel?game=werewolves&section=lastresults](https://boardgamearena.com/#!gamepanel?game=werewolves&section=lastresults)
-   [https://boardgamearena.com/#!gamepanel?game=coupcitystate&section=lastresults](https://boardgamearena.com/#!gamepanel?game=coupcitystate&section=lastresults)

Adding this has the following effects:

-   On game results for this game, "Winner" or "Loser" is going to appear instead of the usual "1st, 2nd, 3rd, ...".
-   When a game is over, the result of the game will be "End of game: Victory" or "End of game: Defeat" depending on the result of the CURRENT player (instead of the usual "Victory of XXX").
-   When calculating ELO points, if there is at least one "Loser", no "victorious" player can lose ELO points, and no "losing" player can win ELO point. Usually it may happened because being tie with many players with a low rank is considered as a tie and may cost you points. If losers\_not\_ranked is set, we prevent this behavior and make sure you only gain/loss ELO when you get the corresponding results.

Important: this SHOULD NOT be used for cooperative games (see is\_coop parameter), or for 2 players games (it makes no sense in this case).

### Solo

If game supports solo variant, a negative or zero score means defeat, a positive score means victory.

### Player elimination

In some games, this is useful to eliminate a player from the game in order he/she can start another game without waiting for the current game end.

This case should be rare. Please don't use player elimination feature if some player just has to wait the last 10% of the game for game end. This feature should be used only in games where players are eliminated all along the game (typical examples: "Perudo" or "The Werewolves of Miller's Hollow").

Usage:

-   Player to eliminate should NOT be active anymore (preferably use the feature in a "game" type game state).
-   In your PHP code:

 $this->eliminatePlayer( <player\_to\_eliminate\_id> );

-   the player is informed in a dialog box that he no longer have to play and can start another game if he/she wants too (with buttons "stay at this table" "quit table and back to main site"). In any case, the player is free to start & join another table from now.
-   When your game is over, all players who have been eliminated before receive a "notification" (the small "!" icon on the top right of the BGA interface) that indicate them that "the game has ended" and invite them to review the game results.

**Important:** this should not be used on a player who has already left the game ("zombie") as leaving/being kicked of the game (outside of the scope of the rules) is not the same as being eliminated from the game (according to the rules), except if in the course of the game, the zombie player is eliminated according to the rules.

**Important:** When all surviving players are eliminated at the same time BGA framework causes the game to be abandoned automatically. To circumvent this, the game should leave 1 player not eliminated but change final scores accordingly and end the game.

## Reflexion time

function giveExtraTime( $player\_id, $specific\_time=null )

Give standard extra time to this player in real time game. It does not affect turn based games.

Standard extra time (when you don't pass $specific\_time) depends on the speed of the game (small with "slow" game option, bigger with other options).

You can also specify an exact time to add, in seconds, with the "specific\_time" argument (rarely used). Using $specific\_time is not recommended as this does not adjust for the game speed.

Important Note: the total reflection time cannot be more than time allowed for the turn. So you can never make it 10 minutes if turn max is 3 min (the max is determined by game speed). As a consequence there is no point adding this at the begging of the turn of in game setup, it only matters after the action which does not lead to turn end (which is when this player becomes inactive).

Note: standard extra time is automatically doubled for begginer

   function actionBla($args) {
       $this->checkAction('actionBla');
       $playerId = $this->getActivePlayerId();
       $this->giveExtraTime($player\_id);
       // do some inter-turn action
       $this->notifyAll(...);
       // no state transition as this player did an action but stayed active
   }

**$this->tableOptions->isTurnBased(): bool**

Returns true if game is turn based

**$this->tableOptions->isRealTime(): bool**

Returns true if game is in real time

## Undo moves

Please read our [BGA Undo policy](/BGA_Undo_policy "BGA Undo policy") before.

  
**Important**: Before using these methods, you must also add the following to your "gameinfos.inc.php" file, otherwise these methods are ineffective:

 'db\_undo\_support' => true

Note: if you deploy undo support after game is in production this will take into effect for new games only, old games will give user an error if user choses Undo action, but otherwise it should not affect them.

  

function undoSavepoint( )

Save the whole game situation inside an "Undo save point".

There is only ONE undo save point available (see [BGA Undo policy](/BGA_Undo_policy "BGA Undo policy")). Cannot use in multiactivate state or in game state where next state is multiactive.

Note: this function does not actually do anything when it is called, it only raises the flag to store the database AFTER transaction is over. So the actual state will be saved when you exit the function calling it (technically before first queued notification is sent, which matters if you transition to game state not to user state after), this may affect what you end up saving.

  

function undoRestorePoint()

Restore the situation previously saved as an "Undo save point".

You must make sure that the active player is the same after and before the undoRestorePoint (ie: this is your responsibility to ensure that the player that is active when this method is called is exactly the same than the player that was active when the undoSavePoint method has been called).

   function actUndo() {
       $this->undoRestorePoint();
       return PlayTurn::class; // transition to single player state (i.e. beginning of player actions for this turn)
   }

**Important note**: if you are reading game state variable right after restore (without changing state first) it won't work properly as the global table cache is not automatically refreshed after undoRestorePoint(). So you should either change state immediately to refresh game state values, or use $this->gamestate->reloadState() to refresh the state. If you choose to do the latest, be aware that this will bring the state machine back to the state during which the save point snapshot has been taken using undoSavepoint().

## Managing errors and exceptions

Note: when you throw an exception, all database changes and all notifications are cancelled immediately. This way, the game situation that existed before the request is completely restored.

throw new UserException(string|NotificationMessage $message)

Base class to notify a user error

You must throw this exception when a player wants to do something that they are not allowed to do.

The error message will be shown to the player as a "red message".

The error will be displayed to the player, as it's a UserException, the error message should be translated (with clientranslate(), and if there are args wrap them into a NotificationMessage)

Throwing such an exception is NOT considered a bug, so it is not traced in BGA error logs.

Note: if your IDE doesn't do it for you, you'll need to add `use Bga\GameFramework\UserException;` at the top of your file.

Example from Gomoku:

     throw new UserException(clienttranslate("There is already a stone on this intersection, you can't play there"));

Example with translated UserException using args :

     throw new UserException(new NotificationMessage(clienttranslate('You must play a card bigger than ${min}', \[min => $minCardValue\])));

throw new SystemException (string|NotificationMessage $message)

Base class to notify a system exception. The message will be hidden from the user, but show in the logs. Use this if the message contains technical information.

throw new VisibleSystemException (string|NotificationMessage $message)

You must throw this exception when you detect something that is not supposed to happened in your code.

The error message is shown to the user as an "Unexpected error", in order that he can report it in the forum.

The error message is logged in BGA error logs.

it should not be translated, as it's just to bring debug details to you when the player reports the issue.

## Zombie mode

When a player leaves a game for any reason (expelled, quit), he becomes a "zombie player". You must implement a Zombie that will handle the role of the leaver player

Read [Zombie mode](/Zombie_Mode "Zombie Mode") to get all details of the expected code.

## User preferences

**$this->userPreferences->get(int $playerId, int $prefId): ?int**

Return the value of a user preference for a player. It will return the value currently selected in the select combo box, in the top-right menu.

## Player color preferences

BGA premium users may choose their preferred color for playing. For example, if they are used to play green for every board game, they can select "green" in their BGA preferences page.

_To test it, you can set your preferred color on your BGA Studio account._

Making your game compatible with colors preferences is very easy and requires only 1 line of configuration change:

On your gameinfos.inc.php file, add the following lines :

 // Favorite colors support: if set to "true", support attribution of favorite colors based on player's preferences (see reattributeColorsBasedOnPreferences PHP method)
 // NB: this parameter is used only to flag games supporting this feature; you must use (or not use) reattributeColorsBasedOnPreferences PHP method to actually enable or disable the feature.
 'favorite\_colors\_support' => true,

Then, on your main <your\_game>.game.php file check the code of "setupNewGame". New template already have correct code, but if you editing very old game and it may be absent.

       $gameinfos = $this->getGameinfos();
       ...
       if ($gameinfos\['favorite\_colors\_support'\])
           $this->reattributeColorsBasedOnPreferences($players, $gameinfos\['player\_colors'\]); // this should be above reloadPlayersBasicInfos()
       $this->reloadPlayersBasicInfos();

  
The "reattributeColorsBasedOnPreferences" method reattributes all colors, taking into account players color preferences and available colors.

Note that you must update the colors to indicate the colors available for your game.

Some important remarks:

-   for some games (i.e. Chess), the color has an influence on a mechanism of the game, most of the time by giving a special advantage to a player (i.e. Starting the game). Color preference mechanism must NOT be used in such a case.
-   your logic should NEVER consider that the first player has the color X, that the second player has the color Y, and so on. If this is the case, your game will NOT be compatible with reattributeColorsBasedOnPreferences as this method attribute colors to players based on their preferences and not based as their order at the table.

### Custom color assignments

Some colors don't play nicely with BGA's color difference algorithm. If you receive feedback that colors are not well chosen, you can bypass the BGA algorithm by specifying a map from user preference colors to game colors.

For example, you may wish to assign BGA's blue to your game's baby blue: `"0000ff" /* Blue */ => "89CFF0",` whereas otherwise, a deep purple might be chosen instead. Just be sure that the assigned colors are also present in the `player_colors` array passed to `reattributeColorsBasedOnPreferences`, otherwise the assignment will be ignored.

To do this, implement this method in your `X.game.php` class.

Note: the user preference colors (the keys in the returned array) should not be modified, or the code may not work as expected. These are the colors players can choose between in their profile.

    /\*\*
     \* Returns an array of user preference colors to game colors.
     \* Game colors must be among those which are passed to reattributeColorsBasedOnPreferences()
     \* Each game color can be an array of suitable colors, or a single color:
     \* \[
     \*    // The first available color chosen:
     \*    'ff0000' => \['990000', 'aa1122'\],
     \*    // This color is chosen, if available
     \*    '0000ff' => '000099',
     \* \]
     \* If no color can be matched from this array, then the default implementation is used.
     \*/
    function getSpecificColorPairings(): array {
        return array(
            "ff0000" /\* Red \*/         => null,
            "008000" /\* Green \*/       => null,
            "0000ff" /\* Blue \*/        => null,
            "ffa500" /\* Yellow \*/      => null,
            "000000" /\* Black \*/       => null,
            "ffffff" /\* White \*/       => null,
            "e94190" /\* Pink \*/        => null,
            "982fff" /\* Purple \*/      => null,
            "72c3b1" /\* Cyan \*/        => null,
            "f07f16" /\* Orange \*/      => null,
            "bdd002" /\* Khaki green \*/ => null,
            "7b7b7b" /\* Gray \*/        => null,
        );
    }

## Legacy games API

For some very specific games ("legacy", "campaign"), you need to keep some informations from a game to another.

This should be an exceptional situation: the legacy API is costing resources on Board Game Arena databases, and is slowing down the game setup process + game end of game process. Please do not use it for things like:

-   keeping a player preference/settings (=> player preferences and game options should be used instead)
-   keeping a statistics, a score, or a ranking, while it is not planned in the physical board game, or while there is no added value compared to BGA statistics / rankings.

You should use it for:

-   legacy games: when some components of the game has been altered in a previous game and should be kept as it is.
-   "campaign style" games: when a player is getting a "reward" at the end of a game, and should be able to use it in further games.

Important: you cannot store more than 64k of data (serialized as JSON) per player per game. If you go over 64k, storeLegacyData function is going to FAIL, and there is a risk to create a major bug (= players blocked) in your game. You MUST make sure that no more than 64k of data is used for each player for your game. For example, if you are implementing a "campaign style" game and if you allow a player to start multiple campaign, you must LIMIT the number of different campaign so that the total data size to not go over the limit. We strongly recommend you to use this:

 try 
 {
 	$this->legacy->setTeam( $my\_data );
 }
 catch( \\feException $e ) // \\feException is a base class of \\BgaSystemException and others...
 {
 	if( $e->getCode() == FEX\_legacy\_size\_exceeded )
 	{
 		// Do something here to free some space in Legacy data (ex: by removing some variables)
 	}
 	else
 		throw $e;
 }

The keys may only contain letters and numbers, underscore seems not to be allowed.

$this->legacy->set(string $key, int $playerId, mixed $value, int $ttl = 365)

( deprecated alias: $this->storeLegacyData($player\_id, $key, $data, $ttl = 365) )

Store some data associated with $key for the given user / current game

In the opposite of all other game data, this data will PERSIST after the end of this table, and can be re-used

in a future table with the same game.

IMPORTANT: The only possible place where you can use this method is when the game is over at your table (last game action). Otherwise, there is a risk of conflicts between ongoing games.

TTL is a time-to-live: the maximum, and default, is 365 days.

In any way, the total data (= all keys) you can store for a given user+game is 64k (note: data is store serialized as JSON data)

NOTICE: You can store some persistant data across all tables from your game using the specific player\_id 0 which is unused. In such case, it's even more important to manage correctly the size of your data to avoid any exception or issue while storing updated data (ie. you can use this for some kind of leaderbord for solo game or contest)

Note: This function cannot be called during game setup (will throw an error).

$this->legacy->get(string $key, int $playerId, mixed $defaultValue = null)

( deprecated alias: $this->retrieveLegacyData($player\_id, $key) ⚠️ this alias was returning a JSON-encoded value, while the get function returns the real value )

Get data associated with $key for the current game

This data is common to ALL tables from the same game for this player, and persist from one table to another.

Note: calling this function has an important cost => please call it few times (possibly: only ONCE) for each player for 1 game if possible

Note: you can use '%' in $key to retrieve all keys matching the given patterns

$this->legacy->delete(string $key, int $playerId)

( deprecated alias: $this->removeLegacyData($player\_id, $key) )

Remove some legacy data with the given key

(useful to free some data to avoid going over 64k)

  

$this->legacy->setTeam(mixed $value, int $ttl = 365)

( deprecated alias: storeLegacyTeamData( $data, $ttl = 365 ) )

Same as storeLegacyData, except that it stores some data for the whole team within the current table and does not use a key

Ie: if players A, B and C are at a table, the legacy data will be saved for future table with (exactly) A, B and C on the table.

This is useful for games which are intended to be played several time by the same team.

Note: the data total size is still limited, so you must implement catch the FEX\_legacy\_size\_exceeded exception if it happens

$this->legacy->getTeam(mixed $defaultValue = null)

( deprecated alias: $this->retrieveLegacyData() ⚠️ this alias was returning a JSON-encoded value, while the getTeam function returns the real value )

Same as retrieveLegacyData, except that it retrieves some data for the whole team within the current table (set by storeLegacyTeamData)

$this->legacy->deleteTeam()

( deprecated alias: $this->removeLegacyTeamData() )

Same as removeLegacyData, except that it retrieves some data for the whole team within the current table (set by storeLegacyTeamData)

## Players text input and moderation

This section concerns only games where the players have to write some words to play: games based on words, like "Just one" or "Codenames".

Some players will use your game to write insults or profanities. As this is part of the game and not in the game chat, these words cannot be reported by players and moderated.

If you met the following situation:

-   You are asking a player to type a text (word(s) or sentence)
-   The player can enter any text (this is not a pre-selection or anything you can control)
-   This text is visible by at least one other player

Then, you must use the following method:

**function logTextForModeration( $player\_id, $text )**

player\_id = player who write the text

text = text that has been written

  
This function will have no visible consequence for your game, but will allow players to report the text to moderators if something happens.

## Language dependent games API

This API is used for games that are heavily language dependent. Two most common use cases are:

-   Games that have a language dependent component that are not necessarily translatable, typically a list of words. (Think of games like Codenames, Decrypto, Just One...)
-   Games with massive communication where players would like to ensure that all participants speak the same language. (Think of games like Werewolf, The Resistance, maybe even dixit...)

If this option is used, the table created will be limited only to users that have specific language in their profile. Player starting the game would be able to chose one of the languages they speak.

There is a new property language\_dependency in gameinfos.inc.php which can be set like this:

 'language\_dependency' => false,  //or if the property is missing, the game is not language dependent
 'language\_dependency' => true, //all players at the table must speak the same language
 'language\_dependency' => array( 1 => 'en', 2 => 'fr', 3 => 'it' ), //1-based list of supported languages

In the gamename.game.php file, you can get the id of selected language with the method **getGameLanguage**.

function getGameLanguage()

Returns an index of the selected language as defined in gameinfos.inc.php.

Languages currently available on BGA are:

 'ar' => array( 'name' => "العربية", 'code' => 'ar\_AE' ),             // Arabic
 'be' => array( 'name' => "беларуская мова", 'code' => 'be\_BY' ),     // Belarusian
 'bn' => array( 'name' => "বাংলা", 'code' => 'bn\_BD' ),                // Bengali
 'bg' => array( 'name' => "български език", 'code' => 'bg\_BG' ),      // Bulgarian
 'ca' => array( 'name' => "català", 'code' => 'ca\_ES' ),              // Catalan
 'cs' => array( 'name' => "čeština", 'code' => 'cs\_CZ' ),             // Czech
 'da' => array( 'name' => "dansk", 'code' => 'da\_DK' ),               // Danish
 'de' => array( 'name' => "deutsch", 'code' => 'de\_DE' ),             // German
 'el' => array( 'name' => "Ελληνικά", 'code' => 'el\_GR' ),            // Greek
 'en' => array( 'name' => "English", 'code' => 'en\_US' ),             // English
 'es' => array( 'name' => "español", 'code' => 'es\_ES' ),             // Spanish
 'et' => array( 'name' => "eesti keel", 'code' => 'et\_EE' ),          // Estonian       
 'fi' => array( 'name' => "suomi", 'code' => 'fi\_FI' ),               // Finnish
 'fr' => array( 'name' => "français", 'code' => 'fr\_FR' ),            // French
 'he' => array( 'name' => "עברית", 'code' => 'he\_IL' ),               // Hebrew       
 'hi' => array( 'name' => "हिन्दी", 'code' => 'hi\_IN' ),                 // Hindi
 'hr' => array( 'name' => "Hrvatski", 'code' => 'hr\_HR' ),            // Croatian
 'hu' => array( 'name' => "magyar", 'code' => 'hu\_HU' ),              // Hungarian
 'id' => array( 'name' => "Bahasa Indonesia", 'code' => 'id\_ID' ),    // Indonesian
 'ms' => array( 'name' => "Bahasa Malaysia", 'code' => 'ms\_MY' ),     // Malaysian
 'it' => array( 'name' => "italiano", 'code' => 'it\_IT' ),            // Italian
 'ja' => array( 'name' => "日本語", 'code' => 'ja\_JP' ),               // Japanese
 'jv' => array( 'name' => "Basa Jawa", 'code' => 'jv\_JV' ),           // Javanese                       
 'ko' => array( 'name' => "한국어", 'code' => 'ko\_KR' ),               // Korean
 'lt' => array( 'name' => "lietuvių", 'code' => 'lt\_LT' ),            // Lithuanian
 'lv' => array( 'name' => "latviešu", 'code' => 'lv\_LV' ),            // Latvian
 'nl' => array( 'name' => "nederlands", 'code' => 'nl\_NL' ),          // Dutch
 'no' => array( 'name' => "norsk", 'code' => 'nb\_NO' ),               // Norwegian
 'oc' => array( 'name' => "occitan", 'code' => 'oc\_FR' ),             // Occitan
 'pl' => array( 'name' => "polski", 'code' => 'pl\_PL' ),              // Polish
 'pt' => array( 'name' => "português",  'code' => 'pt\_PT' ),          // Portuguese
 'ro' => array( 'name' => "română",  'code' => 'ro\_RO'  ),            // Romanian
 'ru' => array( 'name' => "Русский язык", 'code' => 'ru\_RU' ),        // Russian
 'sk' => array( 'name' => "slovenčina", 'code' => 'sk\_SK' ),          // Slovak
 'sl' => array( 'name' => "slovenščina", 'code' => 'sl\_SI' ),         // Slovenian       
 'sr' => array( 'name' => "Српски", 'code' => 'sr\_RS' ),              // Serbian       
 'sv' => array( 'name' => "svenska", 'code' => 'sv\_SE' ),             // Swedish
 'tr' => array( 'name' => "Türkçe", 'code' => 'tr\_TR' ),              // Turkish       
 'uk' => array( 'name' => "Українська мова", 'code' => 'uk\_UA' ),     // Ukrainian
 'zh' => array( 'name' => "中文 (漢)",  'code' => 'zh\_TW' ),           // Traditional Chinese (Hong Kong, Macau, Taiwan)
 'zh-cn' => array( 'name' => "中文 (汉)", 'code' => 'zh\_CN' ),         // Simplified Chinese (Mainland China, Singapore)

## Debugging and Tracing

To debug php code you can use some tracing functions available from the parent class such as debug, trace, error, warn, dump.

 $this->debug("Ahh!");
 $this->dump('my\_var',$my\_var);

See [Practical\_debugging](/Practical_debugging "Practical debugging") section for complete information about debugging interfaces and where to find logs.

## Creating other classes

You can create other classes in the `modules/php` dir, next to your `Game.php` file.

If they are names following the PSR-4, they will be autoloaded by BGA (no require\_once needed). This doesn't work for the "legacy way" (=`YourGameName.game.php` at the game root dir).

For example, writing `new ScoreManager()` in your Game file will try to load the class `ScoreManager` of namespace `Bga\Games\YourGameName` in `modules/php/ScoreManager.php`

Don't forget to add namespace `Bga\Games\YouGameName;` at the beginning of your module.

You can even put these classes in sublevels : the class `Bga\Games\YourGameName\Cards\StandardCard` will be automatically loaded if it is stored in `modules/php/Cards/StandardCard.php`

Retrieved from "[http:///index.php?title=Main\_game\_logic:\_Game.php&oldid=26917](http:///index.php?title=Main_game_logic:_Game.php&oldid=26917)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")