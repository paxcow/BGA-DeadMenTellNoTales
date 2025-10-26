# BgaCards - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# BgaCards

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
-   bga-cards : a JS component for cards.
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

**[Demo](https://x.boardgamearena.net/data/game-libs/bga-cards/1.x/demo/index.html)**

**[Doc](https://x.boardgamearena.net/data/game-libs/bga-cards/1.x/docs/index.html)**

## Contents

-   [1 Overview](#Overview)
-   [2 Usage](#Usage)
-   [3 Versioning](#Versioning)
-   [4 Using with TypeScript](#Using_with_TypeScript)
-   [5 Changelog](#Changelog)

## Overview

**bga-cards** is a javascript component to display cards.

The lib will handle associated animations (moving between stocks, flipping or rotating the cards).

The game Frenchtarot is an example of usage (with JS), or Verso (with TypeScript)

## Usage

Load the lib:

define(\[
    "dojo","dojo/\_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    getLibUrl('bga-animations', '1.x'), // the lib uses bga-animations so this is required!
    getLibUrl('bga-cards', '1.x'),
\],
function (dojo, declare, gamegui, counter, BgaAnimations, BgaCards) { // note that the index of \`BgaAnimations\` must match the index of the define array

In your game setup:

        // create the animation manager, and bind it to the \`game.bgaAnimationsActive()\` function
        this.animationManager = new BgaAnimations.Manager({
            animationsActive: () => this.bgaAnimationsActive(),
        });

        // create the card manager
        this.cardsManager = new BgaCards.Manager({
            animationManager: this.animationManager,
            type: 'mygame-card',
            getId: (card) => card.id,
            setupFrontDiv: (card, div) => {
                div.style.background = 'blue';
                this.addTooltipHtml(div.id, \`tooltip of ${card.type}\`);
            },
        });

Only setup an animation manager if you don't already have one, else re-use the same one.

Example of usage:

        // create the stock, in the game setup
        this.cardStock = new BgaCards.LineStock(this.cardsManager, document.getElementById('card-stock'));
        this.cardStock.addCards(gamedatas.cards); // cards should be something like \[{ id: 1, type: 3, type\_arg: 2, location: 'table', location\_arg: 0 }\] 

notif\_revealNewCards: async function(args) {
    await this.cardStock.addCards(args.newCards); // similar form as above
}

Look at the demo page and the demo source code for a list of all possibilities!

## Versioning

The lib is using semver, so you can require 1.x to be sure to have the last fixes without risking a breaking change. Any breaking change will be noted on the Changelog section.

## Using with TypeScript

If you use TypeScript and this lib, you can download the **[d.ts](https://x.boardgamearena.net/data/game-libs/bga-cards/1.x/dist/bga-cards.d.ts)** file to put in on your game folder to benefit from auto-completion. Depending on the way you build, you might need to remove the last line (the export instruction) to be able to use it.

If your game class is not declared on the define callback, you will need to modify it with this trick (to avoid a "ReferenceError: BgaAnimations is not defined" error) :

define(\[
        "dojo",
        "dojo/\_base/declare",
        "ebg/core/gamegui",
        "ebg/counter",
        "ebg/stock",
        getLibUrl('bga-animations', '1.x'),
    \],
function (dojo, declare, gamegui, counter, stock, BgaAnimations) {
    (window as any).BgaAnimations = BgaAnimations; //trick
    return declare("bgagame.reforest", ebg.core.gamegui, new Reforest());
});

## Changelog

**1.0.8**: fix slot & card selection override

**1.0.7**: fix HandStock blinking for loose width container / fix wrong deck number on constructor / fix removeCard.fadeOut without slideTo

**1.0.6**: fix autoPlace with VoidStock

**1.0.5**: fix selection of parent card if it contains a child card

**1.0.4**: fix error on emptyHandMessage not defined

**1.0.3**: fix emptyHandMessage conflicting with sort

**1.0.2**: fix selection style override

**1.0.1**: Fix documentation

**1.0.0**: Initial version

Retrieved from "[http:///index.php?title=BgaCards&oldid=26941](http:///index.php?title=BgaCards&oldid=26941)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")