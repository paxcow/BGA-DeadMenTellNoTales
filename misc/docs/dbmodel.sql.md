# Game database model: dbmodel.sql - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# Game database model: dbmodel.sql

From Board Game Arena

[Jump to navigation](#mw-head) [Jump to search](#searchInput)

  

**Game File Reference**

**[Overview](/Studio_file_reference "Studio file reference")**

-   **dbmodel.sql** - database model
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

In this file, you specify the database schema of your game.

This file contains SQL queries that will be executed during the creation of your game table.

**Note:** you can't change the database schema during the game.

## Contents

-   [1 Create your schema](#Create_your_schema)
-   [2 Default tables](#Default_tables)
    -   [2.1 The **player** table](#The_player_table)
-   [3 CREATE TABLES](#CREATE_TABLES)
-   [4 PHP](#PHP)
-   [5 Errors Log](#Errors_Log)
-   [6 Post-release database modification](#Post-release_database_modification)
-   [7 Uses and misuses of AUTO\_INCREMENT](#Uses_and_misuses_of_AUTO_INCREMENT)

## Create your schema

To build this file, we recommend you build the tables you need with the PhpMyAdmin tool (see the BGA user guide), and then export them and copy/paste the content to this file.

**Note:** the name of a column must not be the same as the name of the table, as the framework replay function relies on regexp substitution to save/restore the previous state in a clone table with another name.

Example: Deck component, see [Deck](/Deck "Deck")

CREATE TABLE IF NOT EXISTS \`card\` (
  \`card\_id\` int(10) unsigned NOT NULL AUTO\_INCREMENT,
  \`card\_type\` varchar(16) NOT NULL,
  \`card\_type\_arg\` int(11) NOT NULL,
  \`card\_location\` varchar(16) NOT NULL,
  \`card\_location\_arg\` int(11) NOT NULL,
  PRIMARY KEY (\`card\_id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO\_INCREMENT=1 ;

Example: Euro Game. See details on database design for euro game at [BGA\_Studio\_Cookbook#Database\_for\_The\_euro\_game](/BGA_Studio_Cookbook#Database_for_The_euro_game "BGA Studio Cookbook")

CREATE TABLE IF NOT EXISTS \`token\` (
  \`token\_key\` varchar(32) NOT NULL,
  \`token\_location\` varchar(32) NOT NULL,
  \`token\_state\` int(10),
  PRIMARY KEY (\`token\_key\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Rules you should follow:

-   Do not overcomplicate, you are dealing with games with 50-500 pieces!
    -   If you have 5 tables for a card game with 30 cards - it is overkill
-   The database should only be storing dynamic data, all static data should be stored in material.php.inc
    -   Example: if you have cards that have power, color, and special abilities. In the database, you only need to store the card type, all the other properties should not be there. The only exception is if it changes during the game.
-   Columns should be permanent, independent of the game data, i.e. location, type, position, row, etc.
    -   Example: do not create columns such as Country1, Country2, Country3, etc.
-   Do not store translatable strings in the database, use an integer number of "ident" to access properties
    -   i.e. card type should be "22" or "bob\_cat", instead of "Bob's cat"
-   Create a separate module in PHP to handle all database queries, do a lot of type checking to prevent SQL injections

Example of method to handle a database query:

    // Set token state
    function setTokenState($token\_key, $state) {
        self::checkState($state); // ensure state is number
        self::checkKey($token\_key); // ensure key is alphanum
        $sql = "UPDATE \`{$this->table}\`";
        $sql .= " SET \`token\_state\` = '$state'";
        $sql .= " WHERE \`token\_key\` = '$token\_key'"; // don't need to escape anymore since we checked key before
        self::DbQuery($sql);
        return $state;
    }

## Default tables

By default, BGA creates four tables for your game: **global**, **stats**, **gamelog**, and **player**.

You **MUST NOT MODIFY** the schemas of the **global**, **stats**, or **gamelog** tables (and you must not access them directly with SQL queries in your PHP code).

### The **player** table

You may add columns to the **player** table. It is very practical to add simple values associated with players. **NB:** you must not alter existing columns created by the framework.

Example:

ALTER TABLE \`player\` ADD \`player\_reserve\_size\` SMALLINT UNSIGNED NOT NULL DEFAULT '7';

The commonly used columns of the default "player" table are:

-   **player\_no**: the index of a player in natural playing order (starting with 1)
-   **player\_id** (int)
-   **player\_name**: (note: it is better to access this data with the getActivePlayerName() or loadPlayersBasicInfos() methods)
-   **player\_score**: the current score of a player (displayed in the player panel). Update this field with $this->playerScore, to update a player's score.
-   **player\_score\_aux**: the secondary score, used as a tie breaker. Update this field with $this->playerScoreAux, according to tie breaking rules of the game (see also: [Manage\_player\_scores\_and\_Tie\_breaker](/Main_game_logic:_yourgamename.game.php#Manage_player_scores_and_Tie_breaker "Main game logic: yourgamename.game.php"))

-   **player\_table\_order**: gives an indication of the rank of the player by order of arrival in the lobby (starting with 1). It is not the same as player\_no (which is the player order within the game). player\_table\_order is useful for setting custom teams if desired in a game option (for instance, 1st-2nd vs 3rd-4th).  
    **Note:** player\_table\_order _only exists during game initialization_ (in the **setupNewGame** function). It is not added as a column in the **players** Db table.

**CAUTION:** **player\_table\_order** is not guaranteed to be equal to the rank of the player in the table. For example, in a four-player game, if the table was full but the third player leaves before the game starts, the fourth player becomes the third on this table but their player\_table\_no is still equal to four! If another player then joins, their player\_table\_no will then be 5.  
Thus, it is essential to normalize these values first in the game setup if you wish to use them to prevent bugs at game launch. For example, if the set of player\_table\_order are <player A>: 3, <player B>: 2, <player C>: 5, <player D>: 7, you see that you can't read those values as ranks directly, but you can still deduce that <player B> was first on the table, then <player A>, then <player C>, and then <player D> 😉

See [Assigning Player Order](https://en.doc.boardgamearena.com/BGA_Studio_Cookbook#Assigning_Player_Order) in the **BGA Studio Cookbook** for an example.

## CREATE TABLES

You can create tables, using engine InnoDB. See examples above on how to create tables. Pay attention to the following:

-   Always add IF NOT EXISTS
-   Always set ENGINE=InnoDB DEFAULT CHARSET=utf8
-   Define primary keys
-   If you create NOT NULL fields after the game has gone into production, make sure you took care of the database migration

  

**Note**: if you use comments, then you must not do it in the same line as the code.

Example:

\`activated\` BOOL NOT NULL DEFAULT 1, --  activated or not

will also comment out the whole column \`activated\` BOOL, and that code will not be executed.

## PHP

Database initialization should be in the function setupNewGame() in 'gamename.game.php'.

Database schema migration should be in the function upgradeTableDb(), see below.

Warning: all CREATE/ALTER tables and views should be in dbmodel.sql. Do not call these queries from PHP to avoid implicit commits [https://dev.mysql.com/doc/refman/5.7/en/implicit-commit.html](https://dev.mysql.com/doc/refman/5.7/en/implicit-commit.html) which will cause issues. The only time you can do such queries from PHP is in the upgradeTableDb() method.

## Errors Log

To trace Database creation, check the logs that you can access in /admin/studio.

## Post-release database modification

If you want to modify your database schema after the first release of your game to production, then you should implement the upgradeTableDb() method, see [Updating the database schema after release](/Post-release_phase#Updating_the_database_schema "Post-release phase")

## Uses and misuses of AUTO\_INCREMENT

AUTO\_INCREMENT is not particularly well-named; it's best to think of it as simply guaranteeing uniqueness of a column value. Try to design your schema and model so you don't require strictly-increasing-by-one identifiers; do not assume that the next row inserted will have a value one larger than the current maximum.

While it does generally produce consecutive values, it does not guarantee contiguous generation (i.e. you can end up with "skipped" numbers). Technically you can even end up with later-number rows being inserted earlier (you'd likely only notice or care about that if you also had a "creation\_timestamp" column, though, and wanted it to be "in sync" with the auto\_incremented column). More details can be found [here](https://dev.mysql.com/doc/refman/8.4/en/innodb-auto-increment-handling.html#innodb-auto-increment-configurable)

Retrieved from "[http:///index.php?title=Game\_database\_model:\_dbmodel.sql&oldid=26698](http:///index.php?title=Game_database_model:_dbmodel.sql&oldid=26698)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")