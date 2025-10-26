# Options and preferences: gameoptions.json, gamepreferences.json - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# Options and preferences: gameoptions.json, gamepreferences.json

From Board Game Arena

[Jump to navigation](#mw-head) [Jump to search](#searchInput)

  

**Game File Reference**

**[Overview](/Studio_file_reference "Studio file reference")**

-   [**dbmodel.sql**](/Game_database_model:_dbmodel.sql "Game database model: dbmodel.sql") - database model
-   [**gameinfos.inc.php**](/Game_meta-information:_gameinfos.inc.php "Game meta-information: gameinfos.inc.php") - meta-information
-   **gameoptions.json** - game options & user preferences
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

In `gameoptions.json`, you can define your game options (i.e. game variants).

In `gamepreferences.json`, you can define user preferences.

Note: If your game has no variants or preferences, you don't have to modify these files.

**IMPORTANT:** after edits to these files, you have to go to the control panel and press "Reload game options configuration" on Studio for your changes **to take effect**.

Make sure you understand difference between options and preferences:

-   Game options - something usually in the rule book defined as "variant" (except for player count, which is automatically handled). For example, whether to include _The River_ in Carcassonne.
-   User preferences - personal choices of each player only visible to that specific player - i.e. layout, whether or not to prompt for action, whether or not auto-opt in in some actions, etc.

## Contents

-   [1 Game Options](#Game_Options)
    -   [1.1 Details of game options format](#Details_of_game_options_format)
        -   [1.1.1 displaycondition vs startcondition](#displaycondition_vs_startcondition)
        -   [1.1.2 gamestartonly](#gamestartonly)
        -   [1.1.3 level](#level)
        -   [1.1.4 option presentation](#option_presentation)
-   [2 User Preferences](#User_Preferences)
    -   [2.1 Listening for preference changes and updating preference from code](#Listening_for_preference_changes_and_updating_preference_from_code)
    -   [2.2 Accessing User Preferences on the server](#Accessing_User_Preferences_on_the_server)
-   [3 Translations](#Translations)
-   [4 Migration](#Migration)

## Game Options

Game options are selected by the table creator and usually correspond to game variants, for example if the game includes expansions or certain special rules.

These variants are defined in `gameoptions.json` as an object:

 {
    "100": { "name": "Game Setup", ... },
    "101": { "name": "Draft", ... }
 }

Each key corresponds to the option id, and each value is that option definition, which is described below.

**Note:** the file must be valid [JSON](https://www.json.org/). That is, trailing commas and comments are not allowed. However you can use still standard json hack to add a field like "$comment": "Here we go"

**Note 2:** the key in json file must be a string type, even its correspond to a number in other places (such as php, or other references in same json).

  
**$this->tableOptions->get(int $optionId): int**

To get the value of a table option from the Game class, using the JSON key.

  
To access json data (the metadata only) can use

  $game\_options = $this->getTableOptions();

### Details of game options format

The following are the values of the option definition object:

-   **name** - **mandatory**. The name of the option visible for table creator. This is automatically marked for translation.
-   **values** - **mandatory**. The map representing possible values of this option. The key of the map is possible value of this option, its a number but has to be string in json. The value is an object descring it.
    -   **name** - **mandatory**. String representation of the numeric value visible to table creator. This is automatically marked for translation.
    -   **description** - String description of this value to use when the name of the option is not self-explanatory. Displayed at the table under the option when this value is selected. Note: if there is no description, this should be omitted.
    -   **tmdisplay** - String representation of the option visible in the table description in the lobby. Usually if a variant values are On and Off (default), the tmdisplay would be same as description name when On, and nothing (empty string) when Off. (**Warning**: due to some caching, a change in tmdisplay may not be effective immediately in the studio, even after forcing a reload of options.) **Pro Tip:** You can use this as a pre-game communication by adding fake options that just do nothing in the game but make it easier to find other player wanted the same game configuration (see the crew deep sea for example).
    -   **nobeginner** - Set to true if not recommended for beginners
    -   **firstgameonly** - Set to true if this option is recommended only for the first game (discovery option)
    -   **beta** - Set to true to indicate that this option is in "beta" development stage (there will be a warning for players starting the game)
    -   **alpha** - Set to true to indicate that this option is in "alpha" development stage (there will be a warning, and starting the game will be allowed only in training mode except for the developer)
    -   **premium** - Option can be only used by premium members
-   **default** - indicates the default value to use for this option (optional, if not present the first value listed is the default)
-   **displaycondition** - (array of conditions) - checks the conditions before displaying the option for selection. All (or any) conditions must be true for the option to be displayed. All or any depends on value of displayconditionoperand

Supported display condition types:

-   -   _minplayers_ condition ensures at least this many players (Note: if your game works with a disjoint interval of player counts, you can supply an array of valid counts instead of a single value)
    -   _maxplayers_ condition ensures at most this many players
    -   _otheroption_ condition ensures another option is set to given values.
    -   _otheroptionisnot_ condition ensure another option is NOT set to this given values
-   **displayconditionoperand** - can be 'and' (this is the default) or 'or'. Allows to change the behaviour to display the option if one of the conditions is true instead of all of them.
-   **startcondition** - (map from value to conditions array) - checks the conditions (on options VALUES) before starting the game. All conditions must be true for the game to start, otherwise players will get a red error message when attempting to begin the game.

Supported start condition types:

-   -   _minplayers_ condition ensures at least this many players (an array of values is not supported here)
    -   _maxplayers_ condition ensures at most this many players
    -   _otheroption_ conditions ensure another option is set to this given values. That works the same as in **displaycondition**.
    -   _otheroptionisnot_ conditions ensure another option is NOT set to this given value. That works the same as in **displaycondition**.
    
    For all these condition types, a _gamestartonly_ boolean option can be added, if you have options that are exclusive. Setting this boolean to _true_ will defer the evaluation of the startcondition to the game creation, instead of preventing the player to select options that are exclusive at all. See [below](#gamestartonly) for an example. But this should never be used -- see the warning below
    
-   **notdisplayedmessage** - if option is not suppose to be visible because of displaycondition but this is set, the text will be visible instead of combo drop down
-   **level** - what kind of option it is: _base_, _major_, or _additional_. See [below](#level) for more informations.

  
Common (reserved) options for all tables (reserved range 200-299):

-   201 (const GAMESTATE\_RATING\_MODE) - ELO OFF (aka Training mode),
    -   0 = Normal
    -   1 = Training
    -   2 = Arena
-   200 (const GAMESTATE\_CLOCK\_MODE) - game speed profile
    -   \[0, 1, 2\] - realtime (technically <10 realtime but you cannot define range in php)
    -   values >=10 - turn based (currently 10..21)
    -   Note there are two similar values, 9 = realtime "no time limit with friends only" vs. 20 = turn-based "no time limit with friends only"

**Example:**

{
  "100": {
    "name": "Game Variant",
    "values": {
      "1": {
        "name": "Learning",
        "firstgameonly": true,
        "tmdisplay": "Learning"
      },
      "2": {
        "name": "Base",
      },
      "3": {
        "name": "Extended",
        "nobeginner": true,
        "beta": true,
        "tmdisplay": "Extended"
      }
    },
    "default": 2
  },
  "101": {
    "name": "Draft variant",
    "values": {
      "1": {
        "name": "No draft"
      },
      "2": {
        "name": "Draft",
        "tmdisplay": "Draft",
        "premium": true,
        "nobeginner": true
      }
    },
    "displaycondition": \[
      {
        "type": "otheroption",
        "id": 100,
        "value": \[ 2, 3 \]
      },
      {
        "type": "otheroption",
        "id": 201,
        "value": 1
      }
    \],
    "startcondition": {
      "1": \[\],
      "2": \[
        {
          "type": "maxplayers",
          "value": 3,
          "message": "Draft option is available for 3 players maximum."
        }
      \]
    }
  },
  "102": {
    "name": "Takeovers",
    "values": {
      "2": {
        "name": "No takeover"
      },
      "1": {
        "name": "Allow takeovers",
        "tmdisplay": "Takeovers",
        "premium": true,
        "nobeginner": true
      }
    },
    "displaycondition": \[
      {
        "type": "otheroption",
        "id": 100,
        "value": \[ 3 \]
      }
    \],
    "startcondition": {
      "2": \[\],
      "1": \[
        {
          "type": "maxplayers",
          "value": 2,
          "message": "Rebel vs Imperium Takeover Scenario is available for 2 players only."
        }
      \]
    }
  }
}

**Example of option that condition on ELO off:**

{
  "100": {
    "name": "Learning Game (No Research)",
    "values": {
      "1": {
        "name": "Off",
        "tmdisplay": ""
      },
      "2": {
        "name": "On",
        "tmdisplay": "Learning Game"
      }
    },
    "startcondition": {
      "2": {
        "type": "otheroption",
        "id": 201,
        "value": 1,
        "message": "Learning variant available only in friendly mode"
      }
    }
  }
}

**Example of condition that is only available for REALTIME game mode:**

"displaycondition": \[
    { "type": "otheroption", "id": 200, "value": \[0, 1, 2\] }
\],

**Example of using condition on your own option:**

{
  "102": {
    "name": "Scenarios",
    "values": {
      "1": {
        "name": "Off",
        "nobeginner": false
      },
      "2": {
        "name": "On",
        "tmdisplay": "Scenarios",
        "nobeginner": true
      }
    },
    "displaycondition": \[
      {
        "type": "otheroptionisnot",
        "id": 100,
        "value": 1
      }
    \],
    "notdisplayedmessage": "Scenarios variant is not available if Learning variant is chosen"
  }
}

**Example of handling solo vs multiplayer options:**

{
  "100": {
    "name": "Board setup",
    "values": {
      "1": {
        "name": "Mirror setup",
        "description": "The starting player shuffles their 6 Farm Cards and randomly lays a card face up in each of the round spaces of their Fruit Island Board. The other players then lay their cards in exactly the same way, copying the order of the starting player.",
        "tmdisplay": "Mirror setup"
      },
      "2": {
        "name": "Random setup",
        "description": "Instead of every player copying the same card configuration as the starting player, every player shuffles their Farm Cards and lays down the cards randomly on their Fruit Island Board.",
        "tmdisplay": "Random setup"
      }
    },
    "displaycondition": \[
      {
        "type": "minplayers",
        "value": \[
          2,
          3,
          4
        \]
      }
    \]
  },
  "102": {
    "name": "Solo difficulty",
    "values": \[
      {
        "name": "Banana-apprentice",
        "description": "Do not remove any seed before starting the game.",
        "tmdisplay": "Banana-apprentice"
      },
      {
        "name": "Pear to the Throne",
        "description": "Remove 1 seed before starting the game.",
        "tmdisplay": "Pear to the Throne"
      }
    \],
    "displaycondition": \[
      {
        "type": "maxplayers",
        "value": 1
      }
    \]
  }
}

#### displaycondition vs startcondition

`displaycondition` should be used when an option should not be present in the list under certain conditions.

For example, displaying the option which is consistent with the player count.

-   2 player maps: A B C D
-   3 player maps: E F G H
-   4 player maps: I J K L

`startcondition` should be used when a specific combination of option values is invalid, but the option itself still makes sense to show.

For example:

-   Player 1 faction: A, B, C, D, E, F, G
-   Player 2 faction: A, B, C, D, E, F, G
-   startcondition: both players can't be the same faction

These options should both be displayed at all times, but there are some invalid configurations.

The other difference is displaycondition affect option itself, while startcondition affect specific values selected

#### gamestartonly

**WARNING:** [See studio bug #104](https://studio.boardgamearena.com/bug?id=104). You should **NEVER** use `gamestartonly`! Otherwise, you allow players to (unknowingly!) create a turn-based table with impossible options that can never start. Players receive no indication that the option combination they selected is invalid until the last player joins the table (hours or days later) and the game attempts to auto-start. The table won't start because of the `startcondition`, and the table options cannot be changed because it's a turn-based table, so the table must be abandoned. This is a horrible user experience. Please don't subject players to this.

On the table configuration page, it won't let you select a combination which is invalid according to `startcondition`. Doing so will show a red warning, and will revert the option to the previous value. This means you could end up in a situation where you can't easily change between certain options (requiring you to set or unset options in a specific order).

The consequence of the `gamestartonly` flag is that the player \_can\_ select an invalid combination. However, when clicking "Start", an invalid combination will produce an error.

Probably the easiest way to see what the difference is is with an example.

With a \_Clans of Caledonia\_ table:

-   set to training mode
-   set player count to 1
-   try setting "Clan Auction" to one of the "On" options
-   try starting the game, there's an error! Ie. `"gamestartonly" => true`

Then, with a \_Sushi Go Party!\_ table:

-   set number of players to 7
-   try setting "Sushi Go! / Sushi Go Party!" to "Sushi Go!"
-   you can't set the option! Ie. `"gamestartonly" => false`

In code:

{
  "100": {
    "name": "Option name",
    "values": \[\],
    "startcondition": {
      "1": \[
        {
          "type": "maxplayers",
          "value": 2,
          "message": "This option is available for 2 players only.",
          "gamestartonly": true
        }
      \]
    }
  }
}

#### level

Note: At this moment this only has an impact in fancy lobby mode; The presentation of level or checkboxes is not available via normal table creation UI (such as in studio, or when you click Play button in control panel).

{
    "100": {
        "name": "Option name",
        "values": \[...\],
        "level": "major"
    }
}

-   **base** is the default value (you don't need to specify it).
-   **major** denotes a major option, which will always be displayed on top, and with a specific UI.
-   **additional** means this option will not be displayed by default, to unclutter the option panel.

About major options:

-   a game can only have **1 major option**, and of course, it must be a important game changer.
-   the pictures will default to the standard Game Box, and can be set independently for each value, from the [Game metadata manager](/Game_metadata_manager "Game metadata manager") (in the "Major Variants" section). Note: this cannot be tested from Studio as there is not studio-only Game metadata manager, to see Major Variants option - the game has to be deployed at least as alpha.
-   the major option can have more than 2 values (there will then be an arrow to slide to the other values, carousel-like)
-   the naming of major variant values should be concise and the values should have descriptive text (not just "enabled" or "disabled")
    -   👍 Option name "Expansion", option values "Base game", "Bigger is Better" => Displayed as _"Expansion: Base game"_ or _"Expansion: Bigger is Better"_
    -   👎 Option name "Bigger is Better", option values "Enabled", "Disabled => Displayed as _"Bigger is Better: Enabled"_ or _"Bigger is Better: Disabled"_

About additional options:

-   Please set each option that concerns small details in this category: Advanced players will find this option anyway, and it will simplify the interface of the page of your game.

[![Option levels](/images/thumb/0/0c/Option-levels.png/800px-Option-levels.png)](/File:Option-levels.png "Option levels")

#### option presentation

An option WILL be displayed as a **Checkbox** instead of a selector if certain conditions are met:

-   option has only **2 values**
-   values are either (case insensitive):
    -   _yes_ and _no_
    -   _on_ and _off_
    -   _enabled_ and _disabled_

The "default" value still has to be specified, and can be "on" or "off" (it's actually just a difference in display).

  
[![Example of checkbox display](/images/thumb/b/b0/Option-display.png/800px-Option-display.png)](/File:Option-display.png "Example of checkbox display")

  

## User Preferences

User preferences is something cosmetic about the game interface which however can create user wars, so you can satisfy all users by giving them individual preferences. You should use this only if it significantly improves the interface for a large proportion of users. These preferences appear in the three-line hamburger menu in the top corner of the bga menu (and also at the bottom on the page in the Options tab). "Display game logs" and "Display tooltips" are baked-in by default, but you can extend this list as below.

The preference json slightly resembles options, but these are conceptually different. The numbers comes from a different space and do not correspond or conflict with options, i.e. preference 100 has nothing to do with option 100. You can use range 100-199 with gaps.

[![The user preferences menu for the game Century](/images/thumb/c/c1/Century_preferences_menu.PNG/400px-Century_preferences_menu.PNG)](/File:Century_preferences_menu.PNG "The user preferences menu for the game Century")

  

> _These preferences are a good place to put accessibility options - as Century did for its Colorblind Support._

{
  "100": {
    "name": "Colorblind Support",
    "needReload": true,
    "values": {
      "1": {
        "name": "None",
        "cssPref": "colorblind\_off"
      },
      "2": {
        "name": "Numbers",
        "cssPref": "colorblind\_on"
      },
      "3": {
        "name": "Shapes",
        "cssPref": "colorblind\_shapes"
      }
    },
    "default": 1
  }
}

There is two ways to check/apply this. In javascript

 if (this.getGameUserPreference(100) == 2) ...

This checks if preferences 100 has selected value 2.

Second, if cssPref is specified, it will be applied to the **<html>** tag. So you can use different css styling for the preference. Note: you also need to set needReload to true for that class change to be effective.

As user you have to select them from the Gear menu when game is started. On studio only dev0 account will have it actually working (bug?).

The following are the parameters of preferences description array:

-   **name** - **mandatory**. The name of the preference. This string is marked for translation.
-   **needReload** - If set to true, the game interface will auto-reload after a change of the preference.
-   **values** - **mandatory**. The map of values with additional parameters per value.
    -   **name** - **mandatory**. String representation of the numeric value. This string is marked for translation.
    -   **cssPref** - CSS class to add to the **<html>** tag. Currently it is added or removed only after a reload (see needReload).
-   **default** - Indicates the default value to use for this preference (optional, if not present the first value listed is the default).

### Listening for preference changes and updating preference from code

The BGA framework offers read/write and callback for user preference changes. See [Game interface logic: yourgamename.js#User preferences](/Game_interface_logic:_yourgamename.js#User_preferences "Game interface logic: yourgamename.js")

### Accessing User Preferences on the server

PHP has an object called **$this->userPreferences**, and you simply have to use its method get (**get($playerId, $prefId): ?$int**) to know the preference value of a specific player :

if ($this->userPreferences->get($playerId, $prefId) == $prefValue) {
....
}

Note: it is stored in database table bga\_user\_preferences

See also: [Main game logic: Game.php#User preferences](/Main_game_logic:_Game.php#User_preferences "Main game logic: Game.php")

## Translations

Note that any name or description values in these JSON files are automatically added to the translation system.

## Migration

Previously, options and preferences were specified in a single `gameoptions.inc.php` file.

BGA has switched to a JSON format to make parsing easier on the server-side, and to avoid a reliance on PHP for static config.

The PHP format will continue to work for _existing_ games, and although preferred, there is no need to migrate unless you want to. However, newly created games must use the new format.

**Note**: once a game has been committed with a `gameoptions.json` file, however, it is not possible to go back without admin intervention.

If you want to migrate:

Simple method:

-   Go to studio and press Reload game options configuration
-   It will dump json on the log window - you can take this, format it and save into 2 files (if you have both). For gamepreference.json remove option "200" - this is default common option, it should not be in your file.

Manual method:

-   Remove all calls to `totranslate()`, and replace with the plain string
-   Remove any references to BGA's PHP constants, such as GAMESTATE\_RATING\_MODE, and replace with the plain value (in this case, 201)
-   You can populate `gameoptions.json` with the result of `json_encode($game_options, JSON_PRETTY_PRINT)`
-   You can populate `gamepreferences.json` with the result of `json_encode($game_preferences, JSON_PRETTY_PRINT)`
-   If you include gameoptions.inc.php directly, to read the values, then replace those calls with `$this->getTableOptions()` or `$this->getTablePreferences()` as appropriate which return arrays parsed from the JSON files

This message was posted to the developer Discord channel:

> **Game options change (Optional!)**
> 
> `gameoptions.inc.php` is now considered legacy, and `gameoptions.json` and `gamepreferences.json` are recommended for new projects:
> 
> -   The wiki and the template project have been updated: [https://en.doc.boardgamearena.com/Options\_and\_preferences:\_gameoptions.json,\_gamepreferences.json](/Options_and_preferences:_gameoptions.json,_gamepreferences.json#User_Preferences "Options and preferences: gameoptions.json, gamepreferences.json")
> -   Any questions/problems can come to me, or can be asked here.
> -   The format of the json files matches the format of the old php files
> 
> We realise there are some drawbacks (sorry!):
> 
> -   No comments in the JSON file, meaning you have to check the wiki for examples
> -   No PHP constants in the JSON file, meaning magic numbers
> 
> However, we hope it's good for BGA because:
> 
> -   Simpler to parse (for robots and humans)
> -   Easier to check for errors (we could perhaps use an XSLT one day)
> -   No need to run game-specific PHP code on the metasite
> 
> **NOTE**: Switching is totally optional, and the legacy files will still work for existing projects, and for those who know about them.

Retrieved from "[http:///index.php?title=Options\_and\_preferences:\_gameoptions.json,\_gamepreferences.json&oldid=25407](http:///index.php?title=Options_and_preferences:_gameoptions.json,_gamepreferences.json&oldid=25407)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")