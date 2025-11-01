# Game material description: material.inc.php - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# Game material description: material.inc.php

From Board Game Arena

[Jump to navigation](#mw-head) [Jump to search](#searchInput)

  

**Game File Reference**

**[Overview](/Studio_file_reference "Studio file reference")**

-   [**dbmodel.sql**](/Game_database_model:_dbmodel.sql "Game database model: dbmodel.sql") - database model
-   [**gameinfos.inc.php**](/Game_meta-information:_gameinfos.inc.php "Game meta-information: gameinfos.inc.php") - meta-information
-   [**gameoptions.json**](/Options_and_preferences:_gameoptions.json,_gamepreferences.json "Options and preferences: gameoptions.json, gamepreferences.json") - game options & user preferences
-   [**img/**](/Game_art:_img_directory "Game art: img directory") - game art
-   [**Game Metadata Manager**](/Game_metadata_manager "Game metadata manager") - tags and metadata media
-   **material.inc.php** - static data
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

## Contents

-   [1 Overview](#Overview)
-   [2 Definition](#Definition)
-   [3 Access](#Access)
    -   [3.1 Data fields](#Data_fields)
    -   [3.2 PHP constants](#PHP_constants)
-   [4 Testing](#Testing)
-   [5 Adjusting material](#Adjusting_material)
-   [6 Move it to modules/php](#Move_it_to_modules/php)

## Overview

**IMPORTANT NOTE**: This file is not generated anymore by default, but you can still create it (the name is important as only a `material.inc.php` file at the root of your project will be automatically loaded) to describe the material of your game.

If you want to put all your material.inc.php in modules/php instead, please read [Move it to modules/php](https://en.doc.boardgamearena.com/Game_material_description:_material.inc.php#Move_it_to_modules/php)

This file is included by the constructor of your main game logic (yourgame.game.php), and then the variables defined here are accessible everywhere in your game logic file (and also view.php file).

Using material.inc.php makes your PHP logic file smaller and clean. Normally you put ALL static information about your cards, tokens, tiles, etc in that file which do not change. Do not store static info in database.

## Definition

Example from "Eminent Domain":

$this->token\_types = \[
 'card\_role\_survey' => \[
   'type' => 'card\_role',
   'name' => clienttranslate("Survey"),
   'tooltip' => clienttranslate("ACTION: Draw 2 Cards<br/><br/>ROLE: Look at <div class='icon survey'></div> - 1 planet cards, keep 1<br/> <span class='yellow'>Leader:</span> Look at 1 additional card"),
   'b'=>0,
   'p'=>'',
   'i'=>'S',
   'v'=>0,
   'a'=>'dd',
   'r'=>'S', 
   'l'=>'v',
  \],

 'card\_tech\_1\_51' => \[
   'type' => 'tech',
   'name' => clienttranslate("Improved Trade"),
   'b' => 3,
   'p' => 'E',
   'i' => 'TP',
   'v' => 0,
   'a' => 'i',
   'side' => 0,
   'tooltip' => clienttranslate("Collect 1 Influence from the supply."),
 \],

...
\];

So this defines all info about cards, including types, names, tooltips (to be show on client), rules, payment cost, etc.

You can also define PHP constants that can be used in material file and game.php file:

if (!defined('TAPESTRY')) { // guard since this included multiple times
    define("TAPESTRY", 0);
    define("TRACK\_EXPLORATION", 1);
    define("TRACK\_SCIENCE", 2);
    define("TRACK\_MILITARY", 3);
    define("TRACK\_TECHNOLOGY", 4);
}

## Access

### Data fields

To access this in PHP side:

 $type = $this->token\_types\['card\_tech\_1\_51'\]\['type'\];

To access on JS side you have to send all variables from material file via getAllDatas first:

   protected function getAllDatas() {
       $result = array ();
       $result \['token\_types'\] = $this->token\_types;
       ...
       return $result;
   }
 

Then you can access it in similar way:

 var type = this.gamedatas.token\_types\['card\_tech\_1\_51'\].type; // not translatable
 var name = \_(this.gamedatas.token\_types\['card\_tech\_1\_51'\].name); // to be shown to user (NOI18N)

To send this in notification from PHP side:

 $this->notifyAllUsers('gainCard',clienttranslate('player gains ${card\_name}'), \[
      'i18n'=>\['card\_name'\],
      'card\_id' => $card\_id,
      'card\_name' => $this->token\_types\[$card\_id\]\['name'\]
 \]);

### PHP constants

If you also want to access constants in JS side, you can send them via getAllData like this

       $cc = get\_defined\_constants(true)\['user'\];
       $result\['constants'\]=$cc; // this will be all constants though, you may have to filter some stuff out for security reasons

Alternately you can include a material.inc.php in local php file and call this method to print constants in JS format, then include this file in JS, you may have to synchronize this manually, but its better for auto-complete also.

       // this needs to be run locally after including materal file (see example in testing below)
       $cc = get\_defined\_constants(true)\['user'\];
       foreach ($cc as $key => $value) {          
           print ("const $key = $value;\\n");
       }

## Testing

If you screw up you material file such as miss some brackets it is very hard to diagnose. But you can test it locally like this

misc/material\_test.php:

<?php
class material\_test {
    function \_\_construct() {
        include '../material.inc.php';
        var\_dump($this->token\_types); // whatever your var
    }
}
// stub
function clienttranslate($x) { return $x; }

new material\_test();

## Adjusting material

In rare cases expansions of the game change the materials of the original game, in such cases same card for example was re-printed with a different text/rules. Can you keep same card and have material adjust based on selected game options? It is possible with some trickery.

You need to modify the data in that file AFTER constructor when database access is initialized, and have it available for all php entry points. This is possible if you override function initTable() in your game.php file:

    /\*\*
     \* This is called before every action, unlike constructor this method has initialized state of the table so it can
     \* access db
     \*
     \* @Override
     \*/
    protected function initTable() {
        // this fiddles with material file depending on the extension selected
        $this->adjustMaterial();
    }

    function adjustMaterial($force = false) {
        if ( !$force && $this->token\_types\_adjusted)
            return;
        $this->token\_types\_adjusted = true;
        ... // fiddle with data in material file
    }

To adjust material itself - you can do it in any number of ways I personally use this method: for modified values I specify key posfix that match my game option, and adjustment function re-write my keys, for example

 'card\_tech\_1\_51' => \[
   'name' => clienttranslate("Improved Trade"),
   'name@e3' => clienttranslate("Much Improved Trade"),
   'cost' => 3,
   'cost@p2' => 2

In this example if player selects game expansion 3, the key name@e2 would override key name, and if there is 2 players, cost@p2 will override cost. If you need exact code, check adjustMaterial in Ultimate Railroads

  
Note: if you need to add some data to material.inc.php programmatically which does not require database, you can just do it right in that file. Keep in mind will run multiple time for each php call back, so it should be very light

$this->decks = array (
        'g' => array ('id' => 'green','name' => clienttranslate('Green'),'num' => 1 ),
        'r' => array ('id' => 'red','name' => clienttranslate('Red'),'num' => 3 ),
        'v' => array ('id' => 'violet','name' => clienttranslate('Violet'),'num' => 4 ),
        'y' => array ('id' => 'yellow','name' => clienttranslate('Yellow'),'num' => 2 ) )
;
    
$this->dcolor\_map = array();
// reverse map
foreach ($this->decks as $info) {
    $this->dcolor\_map\[$info\['num'\]\]=$info\['id'\];
}

## Move it to modules/php

You can move the file to any modules/php folder/subfolder, but it will not be included automatically, you'll need to import it.

class Game extends \\Table {
    private array $DECKS; // so the IDE knows you define an array called DECKS on the material file
    
	function \_\_construct()
	{
        parent::\_\_construct();

        require 'material.inc.php'; // your material.inc.php in modules/php folder contains $this->DECKS = \[...\]

        $this->initGameStateLabels(\[\]);
	}

    protected function getAllDatas(): array
    {
        // ...
        // use it the same way
        $result\['DECKS'\] = $this->DECKS;
  
        return $result;
    }

Note: this way, you can use any file name and even split you material in multiple files if you want to.

Retrieved from "[http:///index.php?title=Game\_material\_description:\_material.inc.php&oldid=23328](http:///index.php?title=Game_material_description:_material.inc.php&oldid=23328)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")