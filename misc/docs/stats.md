# Game statistics: stats.json - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# Game statistics: stats.json

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
-   **stats.json** - statistics
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

In this file, you are describing game statistics, that will be displayed at the end of the game.

After modifying this file, you must use "Reload statistics configuration" in BGA Studio Control Panel -> Manage Games ("Game Configuration" section):

[https://studio.boardgamearena.com/#!studio](https://studio.boardgamearena.com/#!studio)

There are 2 types of statistics:

-   table statistics, that are not associated to a specific player (i.e.: one value for each game).
-   player statistics, that are associated to each players (i.e.: one value for each player in the game).

Statistics types can be "int" for integer, "float" for floating point values, and "bool" for boolean.

Once you defined your statistics there, you can start using "tableStats->init / playerStats->init", "tableStats->set / playerStats->set" and "tableStats->inc / playerStats->inc" methods in your game logic, using statistics names defined below. See API [https://en.doc.boardgamearena.com/Main\_game\_logic:\_yourgamename.game.php#Game\_statistics](https://en.doc.boardgamearena.com/Main_game_logic:_yourgamename.game.php#Game_statistics).

If you want to skip some of your statistics according to game variants, do not init it, or call set or inc on it. It will be displayed as "-" (instead of 0 if you init it and don't update it afterwards)

!! It is not a good idea to modify this file when a game is running !!

If your game is already public on BGA, please read the following before any change: [https://en.doc.boardgamearena.com/Post-release\_phase#Changes\_that\_breaks\_the\_games\_in\_progress](https://en.doc.boardgamearena.com/Post-release_phase#Changes_that_breaks_the_games_in_progress)

Notes:

-   Statistic index is the reference used in set/inc/init PHP method
-   Statistic index must contains alphanumerical characters and no space. Example: 'turn\_played'
-   Statistics IDs must be >=10
-   Two table statistics can't share the same ID, two player statistics can't share the same ID
-   A table statistic can have the same ID as a player statistics, however its not recommended unless it is same conceptually (like turns\_number is same statistic in both per table and per player)
-   Statistics ID is the reference used by BGA website. If you change the ID, you lost all historical statistic data. Do NOT re-use an ID of a deleted statistic.
-   Statistic name is the English description of the statistic as shown to players
-   The order in which the stats will appear in the endscreen is determined by the order in the array, NOT by the ID. That is helpful for stats which getting added later but need to be higher up in the list.
-   Statistic names and labels are automatically added to the translations system, so there is no need to wrap them in totranslate() calls
-   Previously, this was a PHP file (stats.inc.php). You can continue to use this format for old projects. (**Note**: once a game has been committed with a `stats.json` file, it is not possible to go back without admin intervention.
-   (Number of table statistics) + (Number of player statistics × Maximum number of players) should not exceed ~930, or the game will crash. (Note: the maximum limit is not precise - Seems having stats around 980 broke the game, but reducing to around 930 solved this)

  

{
  "table": {
    "turns\_number": {
      "id": 10,
      "name": "Number of turns",
      "type": "int"
    }
  },
  "player": {
    "turns\_number": {
      "id": 10,
      "name": "Number of turns",
      "type": "int"
    },
    "player\_teststat1": {
      "id": 11,
      "name": "player test stat 1",
      "type": "int"
    },
    "player\_teststat2": {
      "id": 12,
      "name": "player test stat 2",
      "type": "float"
    }
  }
}

Sometimes you may want to display a label instead of a number (for instance if you want to indicate the winning faction as a table statistic, and the faction chosen by each player as a player statistic in a game like Terra Mystica).

You can do this by adding a "value\_labels" key following the "table" and "players" keys. Please note that the labels apply to both table and player statistics, so you should pay attention to use the same statistic number for the same type of statistic (or to skip one number if labelling should not be applied for both)

{
  "table": {
    "winning\_race": {
      "id": 11,
      "name": "Winning race",
      "type": "int"
    },
  },
  "value\_labels": {
    "11": \[
      "None (or tied)",
      "Auren",
      "Witches",
      "Fakirs",
      "Nomads",
      "Chaos Magicians",
      "Giants",
      "Swarmlings",
      "Mermaids",
      "Dwarves",
      "Engineers",
      "Halflings",
      "Cultists",
      "Alchemists",
      "Darklings"
    \]
  }
}

If you want to consider some internal game statistics for game developer, publisher or admins's purpose only, simply add the following extra property : "display" => "limited". For instance :

{
  "player\_teststat2": {
    "id": 12,
    "name": "player test stat 2",
    "type": "float",
    "display": "limited"
  }
}

### Translations

Note that any name or value\_labels text in these JSON files are automatically added to the translation system, even though they aren't wrapped in totranslate() calls.

Retrieved from "[http:///index.php?title=Game\_statistics:\_stats.json&oldid=26899](http:///index.php?title=Game_statistics:_stats.json&oldid=26899)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")