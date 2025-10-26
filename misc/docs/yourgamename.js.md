# Game interface logic: yourgamename.js - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# Game interface logic: yourgamename.js

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
-   X.**js** - interface logic
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

-   [1 File structure](#File_structure)
-   [2 Dojo framework](#Dojo_framework)
-   [3 Javascript minimization (after July 2020)](#Javascript_minimization_\(after_July_2020\))
-   [4 Accessing Players Information](#Accessing_Players_Information)
-   [5 Accessing and manipulating the DOM](#Accessing_and_manipulating_the_DOM)
    -   [5.1 Element by Id](#Element_by_Id)
    -   [5.2 Style](#Style)
    -   [5.3 Classes](#Classes)
    -   [5.4 Attributes](#Attributes)
    -   [5.5 Queries](#Queries)
    -   [5.6 Creating and Destroying elements](#Creating_and_Destroying_elements)
    -   [5.7 Moving elements](#Moving_elements)
-   [6 Animations](#Animations)
    -   [6.1 Dojo Animations](#Dojo_Animations)
    -   [6.2 Sliding](#Sliding)
    -   [6.3 Destroy](#Destroy)
    -   [6.4 Rotating elements](#Rotating_elements)
    -   [6.5 Animation Callbacks](#Animation_Callbacks)
-   [7 Players input](#Players_input)
    -   [7.1 Connecting](#Connecting)
    -   [7.2 Actions](#Actions)
-   [8 Notifications](#Notifications)
    -   [8.1 Subscribe to notifications manually](#Subscribe_to_notifications_manually)
    -   [8.2 The notification Object received by client](#The_notification_Object_received_by_client)
    -   [8.3 Ignoring notifications](#Ignoring_notifications)
    -   [8.4 Handle manually synchronous notifications](#Handle_manually_synchronous_notifications)
    -   [8.5 Pre-defined notification types](#Pre-defined_notification_types)
-   [9 Tooltips](#Tooltips)
    -   [9.1 Adding static tooltips](#Adding_static_tooltips)
    -   [9.2 Removing static tooltips](#Removing_static_tooltips)
    -   [9.3 Advanced tooltips](#Advanced_tooltips)
-   [10 Warning messages](#Warning_messages)
-   [11 Dialogs](#Dialogs)
    -   [11.1 Confirmation dialog](#Confirmation_dialog)
    -   [11.2 Multiple choice dialog](#Multiple_choice_dialog)
    -   [11.3 Generic Dialogs](#Generic_Dialogs)
    -   [11.4 Scoring dialogs](#Scoring_dialogs)
    -   [11.5 Scoring animated display](#Scoring_animated_display)
    -   [11.6 Speech bubble](#Speech_bubble)
-   [12 Translations](#Translations)
-   [13 Players panels](#Players_panels)
    -   [13.1 Adding stuff to player's panel](#Adding_stuff_to_player's_panel)
    -   [13.2 Adding a player panel for an automata](#Adding_a_player_panel_for_an_automata)
    -   [13.3 Player's panel disabling/enabling](#Player's_panel_disabling/enabling)
    -   [13.4 Player order](#Player_order)
    -   [13.5 Counters](#Counters)
-   [14 BGA GUI components](#BGA_GUI_components)
-   [15 BGA Buttons](#BGA_Buttons)
    -   [15.1 Basic Button](#Basic_Button)
    -   [15.2 Image Button](#Image_Button)
    -   [15.3 Disabling Button](#Disabling_Button)
    -   [15.4 Custom Buttons](#Custom_Buttons)
    -   [15.5 Button outside of action bar](#Button_outside_of_action_bar)
-   [16 Image loading](#Image_loading)
-   [17 Sounds](#Sounds)
-   [18 Title bar and states](#Title_bar_and_states)
    -   [18.1 Client states](#Client_states)
    -   [18.2 Title bar](#Title_bar)
-   [19 User preferences](#User_preferences)
-   [20 Other useful stuff](#Other_useful_stuff)

This is the main file for your game interface. Here you will define:

-   Which actions on the page will generate calls to the server.
-   What happens when you get a notification for a change from the server and how it will show in the browser.
-   Setup user interface

## File structure

The details of how the file is structured are described below with comments in the code skeleton provided to you.

Here is the basic structure:

-   **constructor**: here you can define global variables for your whole interface.
-   **setup**: this method is called when the page is refreshed, and sets up the game interface.
-   **onEnteringState**: this method is called when entering a new game state. You can use it to customize the view for each game state.
-   **onLeavingState**: this method is called when leaving a game state.
-   **onUpdateActionButtons**: called on state changes, in order to add action buttons to the status bar. Note: in a MULTIPLE\_ACTIVE\_PLAYER state, it will be called when another player has become inactive.
-   _(utility methods)_: this is where you can define your utility methods.
-   _(player's actions)_: this is where you can write your handlers for player actions on the interface (example: click on an item).
-   **setupNotifications**: this method associates notifications with notification handlers. For each game notification, you can trigger a javascript method to handle it and update the game interface.
-   _(notification handlers)_: this is where you define the notifications handlers associated with notifications in **setupNotifications**, above.

  
More details:

**setup(gamedatas: object)** This method must set up the game user interface according to current game situation specified in parameters. The method is called each time the game interface is displayed to a player, ie:

-   when the game starts
-   when a player opens a game in the browser later
-   when a player refreshes the game page (F5)
-   when player does a server side Undo

"gamedatas" argument contains all data retrieved by your "getAllDatas" PHP method and some more.

  

**onEnteringState(stateName: string, args: { args: any } | null): void**

This method is called each time we enter a new game state. You can use this method to perform some user interface changes at this moment. To access state arguments passed via calling php arg\* method use args?.args. Typically you would do something only for active player, using this.isCurrentPlayerActive() check. It is also called (for the current game state only) when doing a browser refresh (after the setup method is called).

**Warning**: for MULTIPLE\_ACTIVE\_PLAYER states: the active players are NOT active yet so you must use onUpdateActionButtons to perform the client side operation which depends on a player active/inactive status. If you are doing initialization of some structures which do not depend on the active player, you can just replace (this.isCurrentPlayerActive()) with (!this.isSpectator) for the main switch in that method.

See more details in [Your\_game\_state\_machine:\_states.inc.php#Difference\_between\_Single\_active\_and\_Multi\_active\_states](/Your_game_state_machine:_states.inc.php#Difference_between_Single_active_and_Multi_active_states "Your game state machine: states.inc.php")

**onLeavingState(stateName: string): void**

This method is called each time we leave a game state. You can use this method to perform some user interface changes at this point (i.e. cleanup).

**onUpdateActionButtons(stateName: string, args: object | null): void**

In this method you can manage "action buttons" that are displayed in the action status bar and highlight active UI elements. To access state arguments passed via calling php arg\* method use **args** parameter. Note: args can be null! For **game** states and when you don't supply state args function - it is null. This method is called when the active or multiactive player changes. In a classic ACTIVE\_PLAYER state this method is called before the onEnteringState state. In MULTIPLE\_ACTIVE\_PLAYER state it is a mess. The sequencing of calls depends on whether you get into that state from transitions OR from reloading the whole game (i.e. F5).

See more details in [Your\_game\_state\_machine:\_states.inc.php#Difference\_between\_Single\_active\_and\_Multi\_active\_states](/Your_game_state_machine:_states.inc.php#Difference_between_Single_active_and_Multi_active_states "Your game state machine: states.inc.php")

  

## Dojo framework

BGA uses the [Dojo Javascript framework](http://dojotoolkit.org/).

The Dojo framework allows us to do complex things more easily. The BGA framework uses Dojo extensively.

To implement a game, you only need to use a few parts of the Dojo framework. All the Dojo methods you need are described on this page.

## Javascript minimization (after July 2020)

For performance reasons, when deploying a game the javascript code is minimized using **terser** ([https://github.com/terser/terser](https://github.com/terser/terser)). This minifier works with modern javascript syntax. From your project "Manage game" page, you can now test a minified version of your javascript on the studio (and revert to the original).

NB: it has been reported that there is an issue with this minifier and percentage values for opacity.

## Accessing Players Information

**this.player\_id: number** id of the player who is looking at the game. The player may not be part of the game (i.e. spectator)

 if (notif.args.player\_id == this.player\_id) {
   ...
 }

**this.isSpectator: boolean** Flag set to true if the user at the table is a spectator (not a player).

Example:

    if (this.isSpectator) {
        this.player\_color = 'ffffff';
    } else {
        this.player\_color = gamedatas.players\[this.player\_id\].color;
    }

Note: If you want to hide an element from spectators, you should use [CSS 'spectatorMode' class](/Game_interface_stylesheet:_yourgamename.css#spectatorMode "Game interface stylesheet: yourgamename.css").

You may consider making a function like this, to detect if the game is in a read-only state (i.e. non-interactive):

 // Returns true for spectators, instant replay (during game), archive mode (after game end)
 isReadOnly: function () {
   return this.isSpectator || typeof g\_replayFrom != 'undefined' || g\_archive\_mode;
 }

**this.gamedatas: object** Contains the initial set of data to init the game, created at game start or by game refresh (F5). You can update it as needed to keep an up-to-date reference of the game on the client side if you need it, however most of the time this is unnecessary.

Note: In hotseat mode, the framework does not keep this.gamedatas of hotseat players and shares the same set as the main player to store data.

Note: be careful when you update this data structurally, many framework functions expect data to be certain way and they will break if they see something else.

Typical example of accessing player's info

for (var player\_id in this.gamedatas.players) { 
    var playerInfo = this.gamedatas.players \[player\_id\];
    var c = playerInfo.color;
    var name = playerInfo.name;
    // do something 
}

**this.isCurrentPlayerActive(): boolean** Returns true if the player on whose browser the code is running is currently active (it's his turn to play). Note: see remarks above about usage of this function inside onEnteringState method.

 if (this.isCurrentPlayerActive()) ...

**this.getActivePlayerId(): number** Return the ID of the active player, or null if we are not in an ACTIVE\_PLAYER type state.

 if (this.player\_id == this.getActivePlayerId()) ...

**this.getActivePlayers(): number\[\]** Return an array with the IDs of players who are currently active (or an empty array if there are none).

  
**this.getFormattedPlayerName(playerId): string** Get the HTML code to display the player name, in bold, with color (and color\_back if needed)

## Accessing and manipulating the DOM

### Element by Id

**$(elementId: ElementOrId)**

The $ function is used to get an HTML element using its "id" attribute.

Example: modify the content of a "span" element:

In your HTML code:
   <span id="a\_value\_in\_the\_game\_interface">1234</span>

In your Javascript code:
   $('a\_value\_in\_the\_game\_interface').innerHTML = "9999";

  
Note: It is safe to use if you don't know if variable is string (id of element) or element itself, i.e.

  foo: function(card) {
       card = $(card); // now its node, no need to write if (typeof card === 'string') ...
       // but its good idea to check for null here
       ...
  }

  
**getElementById(elementId: string)**

Note: $() is the standard method to access some HTML element with the BGA Framework. You can use **getElementById** but it is longer to type and less handy as it does not do some checks.

### Style

**dojo.style(node: ElementOrId, styleName: string, styleValue: any): void**

With dojo.style you can modify the CSS property of any HTML element in your interface.

Examples:

     // Make an element disappear
     dojo.style( 'my\_element', 'display', 'none' );

     // Give an element a 2px border
     dojo.style( 'my\_element', 'borderWidth', '2px' );

     // Change the background position of an element
     // (very practical when you are using CSS sprites to transform an element to another)
     dojo.style( 'my\_element', 'backgroundPosition', '-20px -50px' );

Note: if you have to modify several CSS properties of an element, or if you have a complex CSS transformation to do, you should consider using dojo.addClass/dojo.removeClass (see below).

You can also use object to set multiple values

dojo.setStyle("thinger", {
  "opacity": 0.5,
  "border": "3px solid black",
  "height": "300px"
});

**this.addStyleToClass(cssClassName: string, styleName: string, styleValue: any):** **void**

Same as dojo.style(), but for all the nodes set with the specified cssClassName Equivalent of

 dojo.query(\`.${aclass}\`).style(styleName, styleValue)

dojo.query("#baz > div").style({
  opacity:0.75,
  fontSize:"13pt"
});

**Vanilla JS style**

 $('my\_element').style.display='none'; // set
 var display = $('my\_element').style.display; // get
 $('my\_element').style.removeProperty('display'); // remove

### Classes

**dojo.addClass(node: ElementOrId, classes: string): void**

**dojo.removeClass(node: ElementOrId, classes: string): void**

**dojo.hasClass(node: ElementOrId, aclass: string): void**

**dojo.toggleClass(node: ElementOrId, aclass: string): void**

In many situations, many small CSS property updates can be replaced by a CSS class change (i.e., you add a CSS class to your element instead of applying all modifications manually).

Advantages are:

-   All your CSS stuff remains in your CSS file.
-   You can add/remove a list of CSS modifications with a simple function and without error.
-   You can test whether you applied the CSS to an element with the **dojo.hasClass** method.

Example from _Reversi_:

    // We add "possibleMove" to an element
    dojo.addClass( 'square\_'+x+'\_'+y, 'possibleMove' );

    // In our CSS file, the class is defined as:
    .possibleMove {
      background-color: white;
      opacity: 0.2;
      filter:alpha(opacity=20); /\* For IE8 and earlier \*/  
      cursor: pointer;  
     }

     // So we've applied 4 CSS property changes in one line of code.

     // ... and when we need to check if a square is a possible move on the client side:
     if( dojo.hasClass( 'square\_'+x+'\_'+y, 'possibleMove' ) )
     { ... }

     // ... and if we want to remove all possible moves in one line of code (see "dojo.query" method):
     dojo.query( '.possibleMove' ).removeClass( 'possibleMove' );

  
**Vanilla JS classList**

This is the only exception where dojo versions are better

 // add class
 $(token\_id).classList.addClass('possibleMove');
 // remove class
 $(token\_id).classList.removeClass('possibleMove');
 // add 2 classes
 const myclasses = \['a','b'\];
 $(token\_id).classList.addClass(...myclasses);
 // add classes to query result
 document.querySelectorAll(".hand .card").forEach((node)=>node.classList.addClass('possibleMove'));

### Attributes

dojo.attr

With dojo.attr you can access or change the value of an attribute or property of any HTML element in your interface.

Exemple:

     // Get the title of a node
     var title = dojo.attr( id, 'title' );
     // Change the height of a node
     dojo.attr( 'img\_growing\_tree', 'height', 100 );

**Vanilla JS attr**

  $(token).id=new\_id; // set attr for "id"
  var id = $(token).id; // get

### Queries

**dojo.query(cssSelector: string): Element\[\]**

With dojo.query, you can query a bunch of HTML elements with a single function, with a "CSS selector" style.

Example:

     // All elements with class "possibleMove":
     var elements = dojo.query( '.possibleMove' );

     // Count number of tokens (i.e., elements of class "token") on the board (i.e., the element with id "board"):
     dojo.query( '#board .token' ).length;

But what is really cool with dojo.query is that you can combine it with almost all methods above.

Examples:

     // Trigger a method when the mouse enter in any element with class "meeple":
     dojo.query( '.meeple' ).connect( 'onmouseenter', this, 'myMethodToTrigger' );

     // Hide all meeples who are on the board
     dojo.query( '#board .meeple' ).style( 'display', 'none' );

**Vanilla JS query**

 var cards=document.querySelectorAll(".hand .card");// all cards in all hands
 var cards=$('hand').querySelectorAll(".card");// all cards in specific hand
 var card=document.querySelector(".hand .card");// first card or null if none (super handy)

### Creating and Destroying elements

**dojo.empty(node: ElementOrId)**

Remove all children of the node element

  dojo.empty('my\_hand');

**dojo.destroy(node: ElementOrId)**

Remove the element

  dojo.destroy('my\_token');

  dojo.query(".green", mynode).forEach(dojo.destroy); // this remove all subnode of class green from mynode

**dojo.create(tag: string, attributes?: obj, parent?: ElementOrId): Element**

Create element

   dojo.create("div", { class: "yellow\_arrow" }, parent); // this creates div with class yellow\_array and places it in "parent"

  

**this.format\_block(name: string, args: object): string**

This bga function that takes global var from template file and substitute variables, typical use would be

               var player = gamedatas.players\[player\_id\];
               var div = this.format\_block('jstpl\_player\_board', player ); // var jstpl\_player\_board = ... is defined in .tpl file 

Note: result is trimmed

**this.format\_string(name: string, args: object): string**

This bga function just substitute variables in a string, i.e.

     var div = this.format\_string('<div color="${player\_color}"></div>', {player\_color: '#ff0000'} );

Note: result is trimmed

Note: this can be replaced by using backquoted string now:

    const player\_color =  '#ff0000';

   const div = \`\`;

**this.format\_string\_recursive**

This bga function is similar to this.format\_string but is capable of processing recursive argument structures and translations. It is used to format server notifications.

TODO: find better place for these function docs

### Moving elements

**dojo.place(node: string | Element, refNode: ElementOrId, pos?: string | number): Element**

dojo.place is the best function to insert HTML code somewhere in your game interface without breaking something. It is much better to use than the **innerHTML=** method if you must insert HTML tags and not only values.

node: can be a string or a DOM node. If it is a string starting with “<”, it is assumed to be an HTML fragment, which will be created. Otherwise it is assumed to be an id of a DOM node.

     // Insert your HTML code as a child of a container element
     dojo.place( "<div class='foo'></div>", "your\_container\_element\_id" );

pos: optional argument. Can be a number or one of the following strings: “before”, “after”, “replace”, “only”, “first”, or “last”. If omitted, “last” is assumed.

-   "replace": replace the container element with my\_node element
-   "first": places the node as a child of the reference node. The node is placed as the first child.
-   "last" (default): places the node as a child of the reference node. The node is placed as the last child.
-   "before": places the node right before the reference node.
-   "after": places the node right after the reference node.
-   "only": replaces all children of the reference node with the node.

this parameter can be a positive integer. In this case, the node will be placed as a child of the reference node with this number (counting from 0). If the number is more than number of children, the node will be appended to the reference node making it the last child.

     // Replace all children of container with my\_node 
     dojo.place( $('my\_node'), "your\_container\_element\_id", "only" );

See also full doc on dojo.place: [\[1\]](https://dojotoolkit.org/reference-guide/1.7/dojo/place.html)

Usually, when you want to insert some piece of HTML in your game interface, you should use "[Javascript templates](/Game_layout:_view_and_template:_yourgamename.view.php_and_yourgamename_yourgamename.tpl#Javascript_templates "Game layout: view and template: yourgamename.view.php and yourgamename yourgamename.tpl")".

But you can also relocate elements like that. Note: it won't animate if you do that.

  

**this.placeOnObject(mobile\_obj: ElementOrId, target\_obj: ElementOrId): void**

places mobile\_obj on target\_obj, set the absolute positions and centers the mobile\_obj on target\_obj, effect is immediate

This is not really an animation, but placeOnObject is frequently used before starting an animation.

Example:

  // (We just created an object "my\_new\_token")

  // Place the new token on current player board
  this.placeOnObject( "my\_new\_token", "overall\_player\_board\_"+this.player\_id );
  
  // Then slide it to its position on the board
  this.slideToObject( "my\_new\_token", "a\_place\_on\_board" ).play();

**this.placeOnObjectPos(mobile\_obj: ElementOrId, target\_obj: ElementOrId, target\_x: number, target\_y: number): void**

This method works exactly like placeOnObject, except than you can specify some (x,y) coordinates (in px). This way, the center of "mobile\_obj" will be placed to the specified x,y position relatively to the center of "target\_obj".

_Note: the placement works differently from this.slideToObjectPos_, since coordinates are calculated based on the center of objects.

**this.attachToNewParent(mobile\_obj: ElementOrId, target\_obj: ElementOrId): void**

With this method, you change the HTML parent of "mobile\_obj" element without moving it. "target\_obj" is the new parent of this element. The beauty of attachToNewParent is that the mobile\_obj element DOES NOT MOVE during this process.

What happens is that the method calculate a relative position of mobile\_obj to make sure it does not move after the HTML parent changes.

Why using this method?

Changing the HTML parent of an element can be useful for the following reasons:

-   When the HTML parent moves, all its child are moving with them. If some game elements is no more linked with a parent HTML object, you may want to attach it to another place.
-   The z\_order (vertical order of display) depends on the position in the DOM, so you may need to change the parent of some game elements when they are moving in your game area.

**CAREFUL**: this function destroys original object and places a clone onto a new parent, this will break all references to this HTML element (ex: dojo.connect). If you need version that does not destroy the object but the same otherwise see [BGA\_Studio\_Cookbook#Attach\_to\_new\_parent\_without\_destroying\_the\_object](/BGA_Studio_Cookbook#Attach_to_new_parent_without_destroying_the_object "BGA Studio Cookbook")

  

## Animations

A new lib for animations has been added to the framework: **[BgaAnimations](/BgaAnimations "BgaAnimations")**. _We recommend to use it instead of the animations listed above._

  
**bgaAnimationsActive()**

Function to know if animations should be played. Animations should not be played in instantaneousMode (fast-replay mode), or if the tab is not displayed in the browser. Returns a boolean saying if animations should be played.

  if (this.bgaAnimationsActive()) {
    // play an animation
  } else {
    // just apply the end situation of the animation
  }

Note: if you use framework animation functions listed above, they already handle this check so you don't need it. It's useful if you write custom animations.

### Dojo Animations

BGA animations is based on Dojo Animation ([see tutorial here](http://dojotoolkit.org/documentation/tutorials/1.8/animation/)).

However, most of the time, you can just use methods below, which are built on top of Dojo Animation.

Note: one interesting method from Dojo that could be useful from time to time is "Dojo.Animation". It allows you to make any CSS property "slide" from one value to another.

Note 2: the slideTo methods are not compatible with CSS transform (scale, zoom, rotate...). If possible, avoid using CSS transform on nodes that are being slided. Eventually, the only possible solution to make these 2 compatible is to disable all CSS transform properties, use slideToObjectPos/placeOnObjectPos, and then apply them again.

  

### Sliding

**this.slideToObject(mobile\_obj: ElementOrId, target\_obj: ElementOrId, duration?: number, delay?: number): Animation**

You can use slideToObject to "slide" an element to a target position.

Sliding element on the game area is the recommended and the most used way to animate your game interface. Using slides allow players to figure out what is happening on the game, as if they were playing with the real boardgame.

The parameters are:

-   mobile\_obj: the ID of the object to move. This object must be "relative" or "absolute" positioned.
-   target\_obj: the ID of the target object. This object must be "relative" or "absolute" positioned. Note that it is not mandatory that mobile\_obj and target\_obj have the same size. If their size are different, the system slides the center of mobile\_obj to the center of target\_obj.
-   duration: (optional) defines the duration in millisecond of the slide. The default is 500 milliseconds.
-   delay: (optional). If you defines a delay, the slide will start only after this delay. This is particularly useful when you want to slide several object from the same position to the same position: you can give a 0ms delay to the first object, a 100ms delay to the second one, a 200ms delay to the third one, ... this way they won't be superposed during the slide.

BE CAREFUL: The method returns an dojo.fx animation, so you can combine it with other animation if you want to. It means that you have to call the "play()" method, otherwise the animation WON'T START.

Example:

   this.slideToObject( "some\_token", "some\_place\_on\_board" ).play();

  

**this.slideToObjectPos(mobile\_obj: ElementOrId, target\_obj: ElementOrId, target\_x: number, target\_y: number, duration?: number, delay?: number): Animation**

This method does exactly the same as "slideToObject", except than you can specify some (x,y) coordinates. This way, "mobile\_obj" will slide to the specified x,y position relatively to "target\_obj".

Example: slide a token to some place on the board, 10 pixels from the top:

   this.slideToObjectPos( "some\_token", "some\_place\_on\_board", 0, 10 ).play();

  

  

**this.slideTemporaryObject(mobile\_obj\_html: string, parent: ElementOrId, from: ElementOrId, to: ElementOrId, duration?: number, delay?: number): Animation**

This method is useful when you want to slide a temporary HTML object from one place to another. As this object does not exists before the animation and won't remain after, it could be complex to create this object (with dojo.place), to place it at its origin (with placeOnObject) to slide it (with slideToObject) and to make it disappear at the end.

slideTemporaryObject does all of this for you:

-   mobile\_obj\_html is a piece of HTML code that represent the object to slide.
-   parent is the ID of an HTML element of your interface that will be the parent of this temporary HTML object.
-   from is the ID of the origin of the slide.
-   to is the ID of the target of the slide.
-   duration/delay works exactly like in "slideToObject"

Example:

this.slideTemporaryObject( '<div class="token\_icon"></div>', 'tokens', 'my\_origin\_div', 'my\_target\_div' );

Note: slideTemporaryObject triggers the animation, you don't have to call `.play()` on it. It returns an Dojo animation with an extra \`promise\` field, allowing you to do `await this.slideTemporaryObject(...).promise`.

### Destroy

**this.slideToObjectAndDestroy(mobile\_obj: ElementOrId, target\_obj: ElementOrId, duration?: number, delay?: number): Animation**

This method is a handy shortcut to slide an existing HTML object to some place then destroy it upon arrival. It can be used for example to move a victory token or a card from the board to the player panel to show that the player earns it, then destroy it when we don't need to keep it visible on the player panel.

It works the same as this.slideToObject and takes the same arguments, but it starts the animation.

CAREFUL: Make sure nothing is creating the same object at the same time the animation is running, because this will cause some random disappearing effects

Example:

this.slideToObjectAndDestroy( "some\_token", "some\_place\_on\_board", 1000, 0 );

Note: slideToObjectAndDestroy triggers the animation, you don't have to call `.play()` on it. It returns an Dojo animation with an extra \`promise\` field, allowing you to do `await this.slideToObjectAndDestroy(...).promise`.

**this.fadeOutAndDestroy( node: string | Element, duration?: number, delay?: number):** **Animation**

This function fade out the target node, then destroy it. Its starts the animation.

-   duration/delay works exactly like in "slideToObject"

Example:

   this.fadeOutAndDestroy( "a\_card\_that\_must\_disappear" );

CAREFUL: the HTML node still exists until during few milliseconds, until the fadeOut has been completed. Make sure nothing is creating same object at the same time as animation is running, because you will be some random dissapearing effects

Note: fadeOutAndDestroy triggers the animation, you don't have to call `.play()` on it. It returns an Dojo animation with an extra \`promise\` field, allowing you to do `await this.fadeOutAndDestroy(...).promise`.

### Rotating elements

This example combines "Dojo.Animation" method and a CSS property transform that allow you to rotate the element.

// node is Element we rotating
		    var animation = new dojo.Animation({
			    curve: \[fromDegree, toDegree\],
			    onAnimate: (v) => {
				    node.style.transform = 'rotate(' + v + 'deg)';
			    } 
		    });
		    
		    animation.play();  

BGA has its own interface to rotate

**this.rotateTo(node: string | Element, degree: number): Animation**

It starts the animation, and stored the rotation degree in the class, so next time you rotate object - it is additive. There is no animation hooks in this one, if you need to change any parameters use dojo animation above.

There is also **rotateInstantTo** with same signature which does not animate

Note: rotateTo triggers the animation, you don't have to call `.play()` on it. It returns an Dojo animation with an extra \`promise\` field, allowing you to do `await this.rotateTo(...).promise`.

### Animation Callbacks

If you wish to run some code only after an animation has completed you can do this by linking a callback method to 'onEnd'.

var animation\_id = this.slideToObject( mobile\_obj, target\_obj, 500 );
dojo.connect(animation\_id, 'onEnd', () => {
   // do something here
});
animation\_id.play();

If you wish to call a second animation after the first (rather than general code) then you can use a dojo animation chain (see tutorial referenced above).

**bgaPlayDojoAnimation(anim)**

-   anim - the dojo animation

Play a dojo animation and returns a promise resolved when it ends.

Examples:

  const anim = this.slideToObject(\`disc\_${x}${y}\`, \`square${x}\_${y}\`);
  await this.bgaPlayDojoAnimation(anim);

  const anim = dojo.fx.chain(\[
    dojo.fadeOut( { node: discDiv } ),
    dojo.fadeIn( { node: discDiv } ),
  \]);
  await this.bgaPlayDojoAnimation(anim);

## Players input

### Connecting

**dojo.connect(element: Element, event: string, context: object, method: eventHandler): any**

**dojo.connect(element: Element, event: string, handler: eventHandler): any**

Used to associate a player event with one of your notification methods.

Example: associate a click on an element ("my\_element") with one of our methods ("onClickOnMyElement"):

      dojo.connect( $('my\_element'), 'onclick', this, 'onClickOnMyElement' );

Same idea but based on query (i.e. all element of 'pet' class)

     dojo.query(".pet").connect('onclick', this, 'onPet');

Note: if you need to disconnect the handler you have to store handler returned from this method, i.e.

    var handler = dojo.connect(...);
    ...
    dojo.disconnect(handler);

If you don't store the handler - you have to destroy the object to disconnect it

Typical function that implements the input handler will look like this

onPet: function(event) {
    var id = event.currentTarget.id;
    console.log('onPet ' + id);
    dojo.stopEvent(event);
    if (this.gamedatas.gamestate.name == 'playerTurnPet') {
          this.bgaPerformAction('actPlayPet', {card: id});
    } else {
          this.showMoveUnauthorized();
    }
}

**this.connect(element: ElementOrId, event: string, method: eventHandler): void**

Used to associate a player event with one of your notification methods.

     this.connect( $('my\_element'), 'onclick', 'onClickOnMyElement' );

Or you can use an in-place handler

     this.connect( $('my\_element'), 'onclick', (e) => { console.log('boo'); } );

Note that this function stores the connection handler. That is the only real difference between **this.connect** and **dojo.connect**. If you plan to destroy the element you connected, you **must** call this.disconnect() to prevent memory leaks. This function is mainly for permanent objects - if you just want to connect the temp object you should probably not use this method but use dojo.connect which won't require any clean-up.

**this.connectClass(cssClassName: string, event: string, method: eventHandler): void**

Same as connect(), but for all the nodes set with the specified cssClassName.

this.connectClass('pet', 'onclick', 'onPet');

**this.disconnect(element: ElementOrId, event: string): void**

Disconnect event handler (previously registered with this.connect or this.connectClass).

  this.disconnect( $('my\_element'), 'onclick');

Note: dynamic connect/disconnect is for advanced cases ONLY, you should always connect elements statically if possible, i.e. in setup() method.

**this.disconnectAll(): void**

Disconnect all previously registed event handlers (registered via this.connect or this.connectClass)

 this.disconnectAll();

### Actions

**this.bgaPerformAction(action: string, args?: object, options: { lock: boolean, checkAction: boolean}): Promise<void>**

Triggers an asynchronous action call in the php backend. Check more of what actions and arguments are possible in [Main\_game\_logic:\_Game.php#Actions\_(autowired)](/Main_game_logic:_Game.php#Actions_\(autowired\) "Main game logic: Game.php") docs.

This method must be used to send a player's input to the game server. **It should not be triggered programmatically**, especially not in loops, in callbacks, in notifications, or in onEnteringState/onUpdateActionButtons/onLeavingState, in order not to create race conditions or break replay game and tutorial features. It should be used only in reaction to a user action in the interface.

Parameters:

-   action: name of the action, as it is written in "possibleactions" of the current state.
-   args: an object containing the call parameters to send to the action, can be undefined/omitted if action has no parameters.
-   options: options to tweak the call with some defaults. Default is `{ lock: true, checkAction: true }`.
    -   lock: (true by default) locks the user interface before any other action can be executed, that prevents user clicking on more buttons while this action is in progress. Set to false if you want to handle locking by yourself.
    -   checkAction: (true by default) check that action specified by "action" parameter in list of possible actions and user is active, only set to false in rare cases when some special out of turn actions are allowed.

Important: this is asynchronous action, this means you should not be doing anything after this line of code except returning; If you want to do something after the call is resolved, use promise handlers - catch and then, see examples below.

Example of a standard call without args:

 this.bgaPerformAction('pass');

Example of a standard call with action args:

 this.bgaPerformAction('actPlayCard', { id: this.selectedCardId });

Example of a call without checking action (because player is inactive in a multiactive state):

this.bgaPerformAction('actChangeMind', {}, { checkAction: false, checkPossibleActions: true });

Example of a call without lock (because of a special action not directly related to the game flow):

this.bgaPerformAction('actSetAutoBid', { alwaysBidUntil: 500 }, { lock: false, checkAction: false });

Example of call with reaction to exception:

 this.bgaPerformAction('actPlayCard', { id: this.selectedCardId }).catch(()=>{ this.selectedCardId = undefined; });

Example of call with reaction to success:

 this.bgaPerformAction('actPlayCard', { id: this.selectedCardId }).then(()=>{ this.unselectAll(); });

  
Technical note:

-   This is a combination of checkAction and ajaxcall, returning a Promise which resolves when ajaxcall ends.
-   The function return void promise - the php callback cannot return any result, any results must be handled via the notification mechanism if needed
-   In case there is an error - error message itself handled by framework, you can use error in catch(e) but you should not be showing it - this is done already

  
**this.ajaxcall(url, parameters, obj\_callback, callback, callback\_anycase?, ajax\_method?: string)**

_Note: bgaPerformAction is a simpler way to use ajax calls, ajaxcall stays in the doc for legacy reasons only and should not be used in new projects._

Same warning as **this.bgaPerformAction** about using on user action only.

-   url: the url of the action to perform. For a game, it must be: "/<mygame>/<mygame>/myAction.html"
-   parameters: an array of parameter to send to the game server.
    -   Note that "lock: true" must always be specified in this list of parameters in order the interface can be locked during the server call. Cannot use lock: false - to not lock it has to be undefined.
    -   Note: Restricted parameter names (please don't use them):
        -   "action"
        -   "module"
        -   "class"
-   obj\_callback: must be set to "this".
-   callback (non-optional but rarely used): a function to trigger when the server returns result and everything went fine (not used, as all data handling is done via notifications).
-   callback\_anycase: (optional) a function to trigger when the server returns ok OR error. If no error this function is called with parameter value false. If an error occurred, the first parameter will be set to true, the second will contain the error message sent by the PHP back-end, and the third will contain an error code.
-   ajax\_method: (optional and rarely used) if you need to send large amounts of data (over 2048 bytes), you can set this parameter to 'post' (all lower-case) to send a POST request as opposed to the default GET. This works, but was not officially documented, so only use if you really need to.

Usage:

this.ajaxcall( '/mygame/mygame/actMyAction.html', { lock: true, 
   arg1: myarg1, 
   arg2: myarg2
}, this, (result)=>{} );

  
**this.checkAction(action: string, nomessage?: boolean): boolean**

Check if player can do the specified action by taking into account:

-   if interface is locked it will return false and show message "An action is already in progress", unless nomessage set to true
-   if player is not active it will return false and show message "This is not your turn", unless nomessage set to true
-   if action is not in list of possible actions (defined by "possibleaction" in current game state) it will return false and show "This move is not authorized now" error (unconditionally).
-   otherwise returns true

Example:

  function onClickOnGameElement( evt )  {
     if( this.checkAction( "actMyAction" ) ) {
        // Do the action
     }
  }

**this.checkPossibleActions(action: string): boolean**

-   this is independent of the player being active, so can be used instead of this.checkAction(). This is particularly useful for multiplayer states when the player is not active in a 'player may like to change their mind' scenario. Unlike this.checkAction, this function does NOT take interface locking into account

-   if action is not in list of possible actions (defined by "possibleaction" in current game state) it will return false and show "This move is not authorized now" error (unconditionally).
-   otherwise returns true

 function onChangeMyMind( evt )  {
    if( this.checkPossibleActions( "actMyAction" ) ) {
       // Do the action
    }
 }

**this.checkLock(nomessage?: boolean): boolean**

When using "lock: true" in ajax call you can use this function to check if the interface is in lock state (it will be locked during server call and notification processing). This check can be used to block some other interactions which do not result in ajaxcall or if you want to suppress errors. Note: normally you only need to use this.checkAction(...), this is for advanced cases.

It will also show error unless nomessage is set to true

 function onChangeMyMind( evt )  {
    if( this.checkLock() ) {
       // Do the action
    }
 }

## Notifications

When something happens on the server side, your game interface Javascript logic received a notification. If you have not done so yet check what notification are in [Main\_game\_logic:\_yourgamename.game.php#Notifications](/Main_game_logic:_yourgamename.game.php#Notifications "Main game logic: yourgamename.game.php")

Here's how you can handle these notifications on the client side.

**bgaSetupPromiseNotifications(params = undefined)**

-   params - the call parameters, by default { prefix: 'notif\_', minDuration: 500, minDurationNoText: 1, logger: null, ignoreNotifications: \[\], onStart: undefined, onEnd: undefined, }.

Auto-detect all notifications declared on the game object (functions starting with \`notif\_\`) and register them with dojo.subscribe.

Registered notifications will be synchronous and will have a minimum duration (if animations are active, by default 500ms with text and 1ms without).

If the notification function returns a Promise, the notification will end when the promise AND the minimum durations are over. If the notification function does not return a promise, it is considered as already resolved as soon as the minimum durations are over.

In case of a notification function returning a Promise, the dev is responsible to make it resolve instantaneously if animations are not active.

See \`bgaAnimationsActive\` to know if animations are active : [https://en.doc.boardgamearena.com/Game\_interface\_logic:\_yourgamename.js#Animations](https://en.doc.boardgamearena.com/Game_interface_logic:_yourgamename.js#Animations)

See \`bgaPlayDojoAnimation\` to handle dojo animations with promises : [https://en.doc.boardgamearena.com/Game\_interface\_logic:\_yourgamename.js#Animation\_Callbacks](https://en.doc.boardgamearena.com/Game_interface_logic:_yourgamename.js#Animation_Callbacks)

  setupNotifications: function() {
    this.bgaSetupPromiseNotifications();
  },    
  notif\_playedCard: async function(args) {
    await this.getPlayerTable(args.playerId).playCard(args.card);
  }

Example of setting custom values for all params :

  setupNotifications: function() {
    this.bgaSetupPromiseNotifications({ 
      prefix: 'notif\_', // default is 'notif\_'
      minDuration: 1200, // for longer animations (500 by default)
      minDurationNoText: 1,
      handlers: \[this.notificationsManager\], // if you write your notif function in a subclass instead of this (default this)
      logger: console.log, // show notif debug informations on console. Could be console.warn or any custom debug function (default null = no logs)
      ignoreNotifications: \['updateAutoPlay'\], // the notif\_updateAutoPlay function will be ignored by bgaSetupPromiseNotifications. You'll need to subscribe to it manually
      onStart: (notifName, msg, args) => $('pagemaintitletext').innerHTML = \`${\_('Animation for:')} ${msg}\`, 
      onEnd: (notifName, msg, args) => $('pagemaintitletext').innerHTML = '',
    });
  }

  
**wait(delay)**

-   delay - the time to wait, in milliseconds

Return a Promise that resolves at the end of a given number of ms. If animations are not active, resolve instantaneously.

  await this.wait(500); // wait 500ms before continuing in an async function

### Subscribe to notifications manually

**dojo.subscribe(notif\_type: string, callback\_obj: Object, handler: string|handler)**

-   notif\_type - notification type/name send by php server
-   callback\_obj - usually this
-   handler - if string method of callback\_obj with name name is called, when notification is called, with notification object as parameter (see below)

Your Javascript "setupNotifications" method is the place where you can subscribe to notifications from your PHP code.

Here's how you associate one of your Javascript method to a notification "playDisc" (from Reversi example):

   setupNotifications: function() {
      ...
      dojo.subscribe('playDisc', this, "notif\_playDisc");
   },

Note: the "playDisc" corresponds to the name of the notification you define it in your PHP code, in your "notify->all" or "notify->player" method.

Then, you have to define your "notif\_playDisc" method:

   notif\_playDisc: function(notif) {
     // Remove current possible moves (makes the board more clear)
     dojo.query( '.possibleMove' ).removeClass( 'possibleMove' );          
     this.addDiscOnBoard( notif.args.x, notif.args.y, notif.args.player\_id );
   },

In a notification handler like our "notif\_playDisc" method, you can access all notifications arguments with "notif.args".

Example:

PHP

    $this->notify->all( "apples", clienttranslate('player takes ${count} apples'), \[ "count" => 3 \] );

JavaScript

    setupNotifications: function() {
       dojo.subscribe( 'apples', this, 'notif\_apples' );
    },

    notif\_apples: function(notif) {
      //You can access the "count" like this:
       alert("count = " + notif.args.count);
    }

### The notification Object received by client

When sending a notification on your PHP, the client side will receive an Object with the following attributes:

-   type - type of the notification (as passed by php function)
-   log - the log string passed from php notification
-   args - This is the arguments that you passed on your notification method on php
-   bIsTableMsg - is true when you use [Notify->all](/Main_game_logic:_yourgamename.game.php#NotifyAllPlayers "Main game logic: yourgamename.game.php") method (false otherwise)
-   channelorig - information about table ID (formatted as : "/table/t\[TABLE\_NUMBER\]")
-   gamenameorig - name of the game
-   move\_id - ID of the move associated with the notification
-   table\_id - ID of the table (comes as string)
-   time - UNIX GMT timestamp
-   uid - unique identifier of the notification
-   h - unknown

_Note that those information were inferred from observation on console log. If an Admin can confirm/correct (and remove this line), you're welcome :)_

### Ignoring notifications

Sometimes you need to ignore some notification on client side. You don't want them to be shown in game log and you don't want them to be handled.

The most common use case is when a player gets private information. They will receive a specific notification (such as "You received Ace of Heart"), while other players would receive more generic notification ("Player received a card").

In X.game.php

        $this->notify->all("dealCard", clienttranslate('${player\_name} received a card'), \[
            'player\_id' => $playerId,
            'player\_name' => $this->getActivePlayerName()
        \]);

        $this->notify->player($playerId, "dealCardPrivate", clienttranslate('You received ${cardName}'), \[
            "type" => $card\["type"\],
            "cardName" => $this->getCardName($card\["type"\])
        \]);

The problem with this approach is that the active player will receive two notifications:

-   Player1 received a card
-   You received Ace of Hearts

Hence, notification ignoring.

NOTE: You can think that it would be possible to send such notification to all players except active just by using notify->player and it seems to work. The problem however is that table spectators would miss such notification and their user interface (and game log) wouldn't be updated. Since there is no way to send notification just to spectators, ignoring the notification (or "filtering") is the only reasonable solution.

**setIgnoreNotificationCheck(notif\_type: string, predicate: ((notif: Notif)=>boolean))**

This method will set a check whether any of notifications of specific type should be ignored.

The parameters are:

-   notif\_type: type of the notification
-   predicate (notif => boolean): a function that will receive notif object and will return true if this specific notification should be ignored

Before dispatching any notification of this type, the framework will call predicate to check whether notification should be ignored, if it return true - the notification will be dispatched, i.e. logged or handled.

    this.notifqueue.setIgnoreNotificationCheck( 'dealCard', (notif) => (notif.args.player\_id == this.player\_id) );

  
IMPORTANT: Remember that this notification is ignored on the client side, but was still received by the client. Therefore it shouldn't contain any private information as cheaters can get it. In other words this is not a way to hide information.

IMPORTANT: When a game is reloaded with F5 or when opening a turn based game, old notifications are replayed as history notification. They are used just to update the game log and are stripped of all arguments except player\_id, i18n and any argument present in message. If you use and other argument in your predicate you should **preserve** it as explained [here](/Main_game_logic:_yourgamename.game.php#Notify_players "Main game logic: yourgamename.game.php").

### Handle manually synchronous notifications

When several notifications are received by your game interface, these notifications are processed immediately, one after the other, in the same exact order they have been generated in your PHP game logic.

However, sometimes, you need to give some time to the players to figure out what happened on the game before jumping to the next notification. Indeed, in many games, there are a lot of automatic actions, and the computer is going to resolve all these actions very fast if you don't tell it not to do so.

As an example, for Reversi, when someone is playing a disc, we want to wait 500 milliseconds before doing anything else in order the opponent player can figure out what move has been played.

Here's how we do this, right after our subscription:

    dojo.subscribe( 'playDisc', this, "notif\_playDisc" );
    this.notifqueue.setSynchronous( 'playDisc', 500 );   // Wait 500 milliseconds after executing the playDisc handler

---

It is also possible to control the delay timing dynamically (e.g., using notification args). As an example, maybe your notification 'cardPlayed' should pause for a different amount of time depending on the number or type of cards played.

For this case, use **setSynchronous** without specifying the duration and use **setSynchronousDuration** within the notification callback.

-   NOTE: If you forget to invoke **setSynchronousDuration**, the game will remain paused forever!

setupNotifications: function () {
    dojo.subscribe( 'cardPlayed', this, 'notif\_cardPlayed' );
    this.notifqueue.setSynchronous( 'cardPlayed' ); // wait time is dynamic
    ...
},

notif\_cardPlayed: function (notif) {
    // MUST call setSynchronousDuration

    // Example 1: From notification args (PHP)
    this.notifqueue.setSynchronousDuration(notif.args.duration);
    ...

    // Or, example 2: Match the duration to a Dojo animation
    var anim = dojo.fx.combine(\[
        ...
    \]);
    anim.play();
    this.notifqueue.setSynchronousDuration(anim.duration);
},

You can also manually call this.notifqueue.setSynchronousDuration(0) once client operations are finished, but be careful that even fast replay still has a path to call it.

**WARNING: combining synchronous and ignored notifications** You must be careful when combining dynamic synchronous durations (as described above) with ignored notifications. If you have a conditionally ignored notification like this (see below section):

this.notifqueue.setIgnoreNotificationCheck( 'myNotif', (notif) => (notif.args.player\_id == this.player\_id) /\* or any other condition \*/ )

then you CANNOT do

this.notifqueue.setSynchronous('myNotif');

as, when the ignored check passes, the notification handler, in which \`this.notifqueue.setSychronousDuration\` is called, is never called and so the duration is never set and interface locking results.

The workaround is to set a "dummy" time:

this.notifqueue.setSynchronous('myNotif', 5000);

whose value is irrelevant but must be large enough to cover the time before the notification handler is called. The large value never actually comes into play because the notification is either ignored, or the synchronous duration is reset to a sensible value inside the handler.

### Pre-defined notification types

**tableWindow** - This defines notification to display [Scoring Dialogs](/Game_interface_logic:_yourgamename.js#Scoring_dialogs "Game interface logic: yourgamename.js"), see below.

**message** - This defines notification that shows on players log and have no other effect (technically any unhandled notification will do the same but its recommended to use this keyword for consistency)

  // You can call this on php side without doing anything on client side
   $this->notify->all( 'message', clienttranslate('hello'), \[\] );

  
**simplePause** - This notification will just delay other notifications, maybe useful if you know you need some extra time for animation or something. Requires a time parameter.

    $this->notify->all( 'simplePause', '', \[ 'time' => 500\] ); // time is in milliseconds

Note: the following types are RESERVED by framework, do not use:

gameStateChange gameStateChangePrivateArg gameStateMultipleActiveUpdate newActivePlayer playerstatus yourturnack clockalert tableInfosChanged playerEliminated tableDecision archivewaitingdelay end\_archivewaitingdelay replaywaitingdelay end\_replaywaitingdelay replayinitialwaitingdelay end\_replayinitialwaitingdelay aiPlayerWaitingDelay replay\_has\_ended updateSpectatorList wouldlikethink updateReflexionTime undoRestorePoint resetInterfaceWithAllDatas zombieModeFail zombieModeFailWarning aiError skipTurnOfPlayer zombieBack allPlayersAreZombie gameResultNeutralized playerConcedeGame showTutorial showCursor showCursorClick skipTurnOfPlayerWarning banFromTable resultsAvailable switchToTurnbased newPrivateState infomsg

## Tooltips

### Adding static tooltips

**this.addTooltip(nodeId: string, helpStringTranslated: string, actionStringTranslated: string, delay?: number): void**

Add a simple text tooltip to the DOM node.

Specify 'helpStringTranslated' to display some information about "what is this game element?". Specify 'actionStringTranslated' to display some information about "what happens when I click on this element?".

You must specify both of the strings. You can only use one and specify an empty string (_) for the other one._

When you pass text directly function \_() must be used for the text to be marked for translation! Except for empty string.

Parameter "delay" is optional. It is primarily used to specify a zero delay for some game element when the tooltip gives really important information for the game - but remember: no essential information must be placed in tooltips as they won't be displayed in some browsers (see [Guidelines](/BGA_Studio_Guidelines "BGA Studio Guidelines")).

Example:

   this.addTooltip( 'cardcount', \_('Number of cards in hand'), '' );

Note: this generates static tooltip and attaches to existing dom element, if you need to generate tooltip more dynamically you have to call that method every time information about object is updated or use completely different tehnique, see dynamic tooltips below.

**this.addTooltipHtml(nodeId: string, html: string, delay?: number): void**

Add an HTML tooltip to the DOM node (for more elaborate content such as presenting a bigger version of a card).

**this.addTooltipToClass(cssClass: string, helpStringTranslated: string, actionStringTranslated: string, delay?: number ): void**

Add a simple text tooltip to all the DOM nodes set with this cssClass. See more details above for this.addTooltip.

    this.addTooltipToClass( 'meeple', \_('This is A Meeple'), \_('Click to tickle') );

IMPORTANT: all concerned nodes must exist and have IDs to get tooltips.

**this.addTooltipHtmlToClass(cssClass: string, html: string, delay?: number): void**

Add an HTML tooltip to to all the DOM nodes set with this cssClass (for more elaborate content such as presenting a bigger version of a card).

IMPORTANT: all concerned nodes must exist and have IDs to get tooltips.

### Removing static tooltips

**this.removeTooltip(nodeId: string): void**

Remove a tooltip from the DOM node with given id.

### Advanced tooltips

**force tooltip to open**

If you want to force tooltip to open in reaction to some other action, i.e. click you can do this

  this.tooltips\[id\].open(id)

where id is the id of the tooltip node where tooltip was installed.

**dynamic tooltips**

See [BGA\_Studio\_Cookbook#Dynamic\_tooltips](/BGA_Studio_Cookbook#Dynamic_tooltips "BGA Studio Cookbook")

**tooltips on mobile**

Tooltips is very unreliable on mobile, it is recommended to implement some other method to obtaining same information, such as simple click handler in dedicated "Help" mode or provide dedicated clickable areas such as corner of card.

## Warning messages

Sometimes, there is something important that is happening in the game and you have to make sure all players get the message. Most of the time, the evolution of the game situation or the game log is enough, but sometimes you need something more visible.

Ex: someone fulfills one of the end of the game conditions, so this is the last turn.

**this.showMessage(msg: string, type: string): void**

showMessage shows a message in a big rectangular area on the top of the screen of the current player, and it dissapears after few seconds (also it will be in the log in some cases).

-   "msg" is the string to display. It should be translated.
-   "type" can be set to "info", "error", "only\_to\_log" or custom string. If set to "info", the message will be an informative message on a white background. If set to "error", the message will be an error message on a red background and it will be added to log. If set to "only\_to\_log", the message will be added to the game log but will not popup at the top of the screen.

If set to custom string, it will be transparent, to use custom type define "head\_xxx" in css, where xxx is the type. For example if you want yellow warning, use "warning" as type and add this to css:

.head\_warning {
   background-color: #e6c66e;
}

Important: the normal way to inform players about the progression of the game is the game log. The "showMessage" is intrusive and should not be used often.

    notif\_messageinfo: function(notif) {
	if (!g\_archive\_mode) {
        	var message = this.format\_string\_recursive(notif.log, notif.args);
		this.showMessage(\_('Announcement:') + " " + message, 'info');		
         }
    },

Show message could be used on the client side to prevent user wrong moves before it is send to server. Example from 'battleship':

onGrid: function(event) {
     if (checkIfPlayerTriesToFireOnThemselves(event)) {
        this.showMessage(\_('This is your own board silly!'), 'error');
        return;
     }
     ...
},

**this.showMoveUnauthorized(): void**

Shows predefined user error that move is unauthorized now

onPet: function(event) {
     if (checkPet(event)==false) {
        this.showMoveUnauthorized();
        return;
     }
     ...
},

## Dialogs

### Confirmation dialog

When an important action with a lot of consequences is triggered by the player, you may want to propose a confirmation dialog.

CAREFUL: the general guideline of BGA is to AVOID the use of confirmation dialogs. Confirmation dialogs slow down the game and bother players. The players know that they have to pay attention to each move when they are playing online.

The situations where you should use a confirmation dialog are the following:

-   It must not happen very often during a game.
-   It must be linked to an action that can really "kill a game" if the player does not pay attention.
-   It must be something that can be done by mistake (ex: a link on the action status bar).

**this.confirmationDialog(message: string, yesHandler: (param: any) => void, noHandler?: (param: any) => void, param?: any): void**

-   message - message will be shown to user, use \_() to translate
-   yesHandler - non-optional handler to be called on yes
-   noHandler - optional handler to called on no
-   param - if specified, it will be passed to both handlers

NOTE: this is async function, it does not return anything and you should not do anything after, you must do everything in handlers

How to display a confirmation dialog:

    this.confirmationDialog(\_("Are you sure you want to bake a pie?"), () => {
      this.bakeThePie();
    });
    return; // nothing should be called or done after calling this, all action must be done in the handler

With param and both handlers

    this.confirmationDialog(\_("Are you sure you want to bake a pie?"), 
           (ingredient) => this.bakeThePie(ingredient), 
           (ingredient) => console.log(\`cancelled baking of ${ingredient}\`), 
           'apple');

### Multiple choice dialog

You can use this dialog to give user a choice with small amount of options

**this.multipleChoiceDialog(message: string, choices: string\[\], callback: (choice: number) => void): void**

-   message - message will be shown to user, use \_() to translate
-   choices - array of choices
-   callback - non-optional handler to be called on choice made, the choice parameter is the INDEX of the choice from the array of choices

NOTE: this is async function, it does not return anything and you should not do anything after, you must do everything in handlers NOTE: there is no cancel handler, so make sure you gave user a choice to get out of it

Example:

    const keys = \["0", "1", "5", "10"\];
    this.multipleChoiceDialog(\_("How many bugs to fix?"), keys, (choice) => {
      if (choice==0) return; // cancel operation, do not call server action
      var bugchoice = keys\[choice\]; // choice will be 0,1,2,3 here
      this.bgaPerformAction("fixBugs", { number: bugchoice });
    });
    return; // must return here

### Generic Dialogs

As a general rule, you shouldn't use dialogs windows.

BGA guidelines specify that all game elements should be displayed on the main screen. Players can eventually scroll down to see game elements they don't need to see anytime, and you may eventually create anchors to move between game area section. Of course dialogs windows are very practical, but the thing is: all players know how to scroll down, and not all players know how to show up your dialog window. In addition, when the dialog shows up, players can't access the other game components.

Sometimes although, you need to display a dialog window. Here is how you do this:

 // Create the new dialog over the play zone. You should store the handler in a member variable to access it later
 this.myDlg = new ebg.popindialog();
 this.myDlg.create( 'myDialogUniqueId' );
 this.myDlg.setTitle( \_("my dialog title to translate") );
 this.myDlg.setMaxWidth( 500 ); // Optional
 
 // Create the HTML of my dialog. 
 // The best practice here is to use [Javascript templates](/Game_layout:_view_and_template:_yourgamename.view.php_and_yourgamename_yourgamename.tpl#Javascript_templates "Game layout: view and template: yourgamename.view.php and yourgamename yourgamename.tpl")
 var html = this.format\_block( 'jstpl\_myDialogTemplate', { 
               arg1: myArg1,
               arg2: myArg2,
               ...
           } );  
 
 // Show the dialog
 this.myDlg.setContent( html ); // Must be set before calling show() so that the size of the content is defined before positioning the dialog
 this.myDlg.show();
 
 // Now that the dialog has been displayed, you can connect your method to some dialog elements
 // Example, if you have an "OK" button in the HTML of your dialog:
 dojo.connect($('my\_ok\_button'), 'onclick', this, (event) => {
               event.preventDefault();
               this.myDlg.destroy();
           });

If necessary, you can remove the default top right corner 'close' icon, or replace the function called when it is clicked:

 // Removes the default close icon
 this.myDlg.hideCloseIcon();

 // Replace the function call when it's clicked
 this.myDlg.replaceCloseCallback((event) => { ... });

### Scoring dialogs

Sometimes at the end of a round you want to display a big table that details the points wins in each section of the game.

Example: in Hearts game, we display at the end of each round the number of "heart" cards collected by each player, the player who collected the Queen of Spades, and the total number of points loose by each player.

Scoring dialogs are managed entirely on **PHP side**, but they are described here as their effects are visible only on client side.

Displaying a scoring dialog is quite simple and is using a special notification type: "tableWindow":

  // on PHP side:
  $this->notify->all( "tableWindow", '', array(
            "id" => 'finalScoring',
            "title" => clienttranslate("Title of the scoring dialog"),
            "table" => $table
        )); 

The "table" argument is a 2 dimensional PHP array that describes the table you want to display, line by line and column by column.

Example: display an 3x3 array of strings

   $table = \[
      \[ "one", "two", "three" \],    // This is my first line
      \[ "four", "five", "six" \],    // This is my second line
      \[ "seven", "height", "nine" \]    // This is my third line
   \];

As you can see above, in each "cell" of your array you can display a simple string value. But you can also display a complex value with a template and associated arguments like this:

   $table = \[
      \[ "one", "two", \[ "str" => clienttranslate("a string with an ${argument}"), "args" => \[ 'argument' => 'argument\_value' \] \] \],
      \[ "four", "five", "six" \], 
      \[ "seven", "height", "nine" \]
   \];

This is especially useful when you want to display player names with colors. Example from "Hearts":

        $firstRow = \[ '' \];
        foreach( $players as $player\_id => $player )    {
            $cell = \[ 'str' => '${player\_name}',
                      'args' => \[ 'player\_name' => $player\['player\_name'\] \],
                      'type' => 'header'
                    \];
            $firstRow\[\] = $cell;
        }
        $table\[\] = $firstRow;
        ...

You can also use three extra attributes in the parameter array for the notification:

-   **header**: the content for this parameter displays before the table (also, the html will be parsed and player names will be colored according to the current game colors).
-   **footer**: the content for this parameter displays after the table (no parsing for coloring the player names)
-   **closing**: if this parameter is used, a button will be displayed with this label at the bottom of the popup and will allow players to close it (more easily than by clicking the top right 'cross' icon).

   $this->notify->all( "tableWindow", '', \[
            "id" => 'finalScoring',
            "title" => clienttranslate("Title of the scoring dialog"),
            "table" => $table,
            "header" => \['str' => clienttranslate('Table header with parameter ${number}'),
                                 'args' => \[ 'number' => 3 \],
                               \],
            "footer" => '<div class="myfoot"></div>',
            "closing" => clienttranslate( "Closing button label" )
        \] ); 

Note: currently id is not used - so you cannot access resulting div by id on js side

Note: any traslatable string have to be wrapped by clienttranslate() on top level OR it has to be recursive template.

DO NOT DO THIS:

   "footer" => '<div>'.clienttranslate( "The end" ).'</div>', // this will not work for translations!!!

### Scoring animated display

Sometimes, you may want to display a score value over an element to make the scoring easier to follow for the players (Terra Mystica final scoring for example). You can do it with:

**this.displayScoring(anchor\_id: string, color: string, score: number | string, duration?: number, offset\_x?: number, offset\_y?: number): void**

  
**anchor\_id**: ID of the html element to place the animated score onto (without the '#')

**color**: hexadecimal RGB representation of the color (should be the color of the scoring player), but without a leading '#'. For instance, 'ff0000' for red.

**score**: numeric score to display, prefixed by a '+' or '-'

**duration**: animation duration in milliseconds (optional, default is 1000)

**offset\_x** and **offset\_y**: if both offset\_x and offset\_y are defined and not null, apply the following offset (in pixels) to the scoring animation. Note that the score is centered in the anchor, so the offsets might have to be negative if you calculate the position.

  
Note: if you want to display successively each score, you can use _this.notifqueue.setSynchronous()_ function.

    setupNotifications: function()   {
           dojo.subscribe( 'displayScoring', this, "notif\_displayScoring" );
           ...
    }
...

    notif\_displayScoring: function(notif) {
            const duration = notif.args.duration?notif.args.duration:1000;
            this.notifqueue.setSynchronous('displayScoring', duration );
	    this.displayScoring( notif.args.target, notif.args.color, notif.args.score, duration);
    },

### Speech bubble

For better interactivity in some games (Love Letter for example), you may use comic book style speech bubbles to express the players voices. This is done with showBubble:

**this.showBubble(anchor\_id: string, text: string, delay?: number, duration?: number, custom\_class?: string): void**

-   anchor\_id - where to attach the bubble
-   text - what to put in bubble, can be html
-   delay - delay in milliseconds (optional, default 0)
-   duration - duration of animation in milliseconds (optional, default 3000)
-   custom\_class - extra class to add to bubble (optional), if you need to override the default bubble style

   this.showBubble('meeple\_2', \_('Hello'), 0, 1000, 'pink\_bubble');

  notif\_speechBubble(notif) {
    var html = this.format\_string\_recursive(notif.args.text, notif.args.args);
    this.showBubble(notif.args.target, html, notif.args.delay ?? 0, notif.args.duration ?? 1000);
  },

**Warning**: if your bubble could overlap other active elements of the interface (buttons in particular), as it stays in place even after disappearing, you should use a custom class to give it the style "pointer-events: none;" in order to intercept click events.

Note: If you want this visually, but want to take complete control over this bubble and its animation (for example to make it permanent) you can just use div with 'discussion\_bubble' class on it, and content of div is what will be shown.

## Translations

See [Translations](/Translations "Translations")

## Players panels

### Adding stuff to player's panel

**this.getPlayerPanelElement(player\_id: number): HTMLElement**

Returns the div in the player panel you can put your counters & other indicators in.

**Example**

At first, create a new "JS template" string in your JS file (example based on Gomoku project) :

const jstpl\_player\_board = (id, color) => \`<div class="cp\_board">
    <div id="stoneicon\_p${id}" class="gmk\_stoneicon gmk\_stoneicon\_${color}"></div><span id="stonecount\_p${id}">0</span>
</div>\`;

Then, you add this piece of code in your JS file to add this template to each player panel:

            // Setting up player boards
            for( var player\_id in gamedatas.players )
            {
                var player = gamedatas.players\[player\_id\];
                         
                // Setting up players boards if needed
                this.getPlayerPanelElement(player\_id).innerHTML = jstpl\_player\_board(player.id, player.color);
            }

(Note: the code above is of course from your "setup" function in your Javascript).

Very often, you have to distinguish current player and others players. In this case, you just have to create another JS template (ex: jstpl\_otherplayer\_board) and use it when "player\_id" is different than "this.player\_id".

### Adding a player panel for an automata

**this.addAutomataPlayerPanel(id: number, name: string, params: Object): void**

The id is the automata id, used to setup scoreCtrl and getPlayerPanelElement. 0 or negative value is recommended, to avoid conflict with real player ids.

Parameters:

-   id: the automata id, used to setup scoreCtrl and getPlayerPanelElement. 0 or negative value is recommended, to avoid conflict with real player ids.
-   name: the name of the automata
-   params: object optionally containing one or more of the following:
    -   color: string - the automata player color (default black)
    -   iconClass: string - the class, or list of classes separated by spaces, to apply to the player picture.
    -   score: number - the automata score (default undefined, will display '-')

Example from Glow, with the automata Tom when playing solo :

this.addAutomataPlayerPanel(0, 'Tom', {
    iconClass: 'tom-avatar',
    score: gamedatas.tom.score,
});

### Player's panel disabling/enabling

**this.disablePlayerPanel(player\_id: number): void**

Disable given player panel (the panel background become gray).

Usually, this is used to signal that this played passes, or will be inactive during a while.

Note that the only effect of this is visual. There are no consequences on the behaviour of the panel itself.

**this.enablePlayerPanel(player\_id:number): void**

Enable a player panel that has been disabled before.

**this.enableAllPlayerPanels(): void**

Enable all player panels that has been disabled before.

### Player order

**this.updatePlayerOrdering(): void**

This function makes sure that player order in player's panel matches this.gamedatas.playerorder and its normally called by framework. You can call it yoursel if you change this.gamedatas.playerorder from notification. Also you can override this function to change defaults OR insert a non-player panel [BGA\_Studio\_Cookbook#Inserting\_non-player\_panel](/BGA_Studio_Cookbook#Inserting_non-player_panel "BGA Studio Cookbook").

  

### Counters

Please use the "ebg/counter" library, documented at [Counter](/Counter "Counter").

## BGA GUI components

BGA framework provides some useful ready-to-use components for the game interface:

[Studio#BGA\_Studio\_game\_components\_reference](/Studio#BGA_Studio_game_components_reference "Studio")

Note that each time you are using an additional component, you must declare it at the top of your Javascript file in the list of modules used.

Example if you are using "ebg.stock":

define(\[
    "dojo","dojo/\_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock"  /// <=== we are using ebg.stock module
\],

## BGA Buttons

### Basic Button

**this.addActionButton(id: string, label: string, method: string | eventhandler, destination?: string, blinking?: boolean, color?: string): void**

_**Deprecated, use t[his.statusBar.addActionButton](https://en.doc.boardgamearena.com/Game_interface_logic:_yourgamename.js#Title_bar) instead**_

You can use this method to add an action button in the main action status bar (or other places).

Arguments:

-   id: an element ID that should be unique in your HTML DOM document.
-   label: the text of the button. Should be translatable (use \_() function). Note: this can also be any html, such as "
    
    ", see example below on how to make image action buttons.
-   method: the name of your method that must be triggered when the player clicks on this button (can be name of the method on game class or handler).
-   destination (optional): id of parent on where to add button, ONLY use in rare cases if location is not action bar. Use **null** as value if you need to specify other arguments.
-   blinking (optional): if set to **true**, the button is going blink to catch player's attention. Please DO NOT abuse blinking button. If you need button to blink after some time passed add class 'blinking' to the button later.
-   color: could be **blue** (default), **red**,**gray** or **none**.

You should only use this method in your "onUpdateActionButtons" method. Usually, you use it like this:

        onUpdateActionButtons: function( stateName, args ) {                      
            if (this.isCurrentPlayerActive()) {            
                switch( stateName ) {
                case 'giveCards':
                    this.addActionButton( 'giveCards\_button', \_('Give selected cards'), () => this.onGiveCards() ); 
                    this.addActionButton( 'pass\_button', \_('Pass'), () => this.bgaPerformAction('actPass') ); 
                    break;
                }
            }
        },   

In the example above, we are adding a "Give selected cards" button in the case we are on game state "giveCards". When player clicks on this button, it triggers our "onGiveCards" method.

Example using blinking red button:

    this.addActionButton( 'button\_confirm', \_('Confirm?'), () => this.onConfirm(), null, true, 'red'); 

If you want to call the handler with arguments, you can use arrow functions, like this:

     this.addActionButton( 'commit\_button', \_('Confirm'), () => this.onConfirm(this.selectedCardId), null, false, 'red'); 

### Image Button

You can use the same method, but add extra class to a button to disable the padding and style it, i.e.

this.addActionButton( 'button\_brick', '<div class="brick"></div>', ()=>{... on brick ...}, null, null, 'gray'); 
dojo.addClass('button\_brick','bgaimagebutton');

where

.bgaimagebutton {
  padding: 0px 12px;
  min-height: 28px;
  border: none;
}

If you use this a lot, you can define a helper function, i.e.

/\*\*
 \* This method can be used instead of addActionButton, to add a button which is an image (i.e. resource). Can be useful when player
 \* need to make a choice of resources or tokens.
 \*/
addImageActionButton: function(id, div, handler, bcolor, tooltip) {
	if (typeof bcolor == "undefined") {
		bcolor = "gray";
	}
	// this will actually make a transparent button id color = gray
	this.addActionButton(id, div, handler, null, false, bcolor);
	// remove border, for images it better without
	dojo.style(id, "border", "none");
	// but add shadow style (box-shadow, see css)
	dojo.addClass(id, "shadow bgaimagebutton");
	// you can also add additional styles, such as background
	if (tooltip) {
		dojo.attr(id, "title", tooltip);
	}
	return $(id);
},

### Disabling Button

You can disable the **bgabutton** by adding the css class **disabled** in you js. The disabled button is still visible but is grey and not clickable. For example in the **onUpdateActionButtons** :

this.addActionButton('play\_button\_id', \_('Play 1 to 3 cards'), () => this.playFunctionButton()); 
if (condition) {
  dojo.addClass('play\_button\_id', 'disabled');
}

  

### Custom Buttons

You can create a custom button, but the BGA framework provides a standard button that requires only .css classes: **bgabutton** and **bgabutton\_${color}**.

**Examples:**

<a href="#" id="my\_button\_id" class="bgabutton bgabutton\_blue"><span>My blue button</span></a>

<a href="#" id="my\_button\_id" class="bgabutton bgabutton\_red bgabutton\_big"><span>My big red button</span></a>

  
**Note**: To see it in action, check out _Coloretto_.

### Button outside of action bar

Use addActionButton() method with destination argument set

this.addActionButton( 'commit\_button', \_('Confirm'), () => this.onConfirm(), 'player\_board', true, 'red'); 

in example above the button will be place on object with id 'player\_board'

  

## Image loading

See also [Game\_art:\_img\_directory](/Game_art:_img_directory "Game art: img directory").

**Be careful**: by default, ALL images of your img directory are loaded on a player's browser when he loads the game. For this reason, don't let in your img directory images that are not useful, otherwise it's going to slowdown the game load.

**this.dontPreloadImage(image\_file\_name: string)**

Using dontPreloadImage, you tell the interface to not preload a specific image in your img directory.

Example of use:

this.dontPreloadImage( 'cards.png' );

This is particularly useful if for example you have 2 different themes for a game. To accelerate the loading of the game, you can specify to not preload images corresponding to the other theme.

Another example of use: in "Gosu" game with Kamakor extension, you play with 5 sets of cards among 10 available. Cards images are organized by sets, and we only preload the images corresponding to the 5 current sets with **ensureSpecificGameImageLoading( image\_file\_names\_array )**.

// By default, do not preload anything
this.dontPreloadImage( 'cards.png' );
this.dontPreloadImage( 'clan1.png' );
this.dontPreloadImage( 'clan2.png' );
this.dontPreloadImage( 'clan3.png' );
this.dontPreloadImage( 'clan4.png' );
this.dontPreloadImage( 'clan5.png' );
this.dontPreloadImage( 'clan6.png' );
this.dontPreloadImage( 'clan7.png' );
this.dontPreloadImage( 'clan8.png' );
this.dontPreloadImage( 'clan9.png' );
this.dontPreloadImage( 'clan10.png' );
var to\_preload = \[\];
for( i in this.gamedatas.clans )
{
	var clan\_id = this.gamedatas.clans\[i\];
	to\_preload.push( 'clan'+clan\_id+'.png' );
}


this.ensureSpecificGameImageLoading( to\_preload );

**Note:** You don't need to specify to not preload game box images (game\_box.png, game\_box75.png...) since they are not preloaded by default.

**this.ensureSpecificGameImageLoading(list: string\[\])**

This is oppostive of dontPreloadImage - its ensure specific images is loaded. Note: only makes sense if preload list is empty, otherwise everything is loaded anyway

  
**load specific images** All images that will be preloaded stored in g\_img\_preload. If you want to override it directly - there is no API, but you can do this in GAME constructor

         g\_img\_preload = \['tokens.png', 'trains.png', 'loc\_plan.png', 'eng.png', 'eng-back.png'\];

You can also set it to empty array and call ensureSpecificGameImageLoading() on specific images

  

## Sounds

**this.sounds.load(id: string, label: string, fileName: string = undefined): void**

Load a sound and register its id. Filename is without the extension. If fileName is unset, it will use the same as the id.

In any case, the sound should be placed on your img folder and exist bothe with the mp3 and ogg format/extension.

Examples :

this.sounds.load('play'); // will load play.ogg / play.mp3 in the img dir, and will be playable with id \`play\`
this.sounds.load('claw', 'smash'); // will load smash.ogg / smash.mp3 in the img dir, and will be playable with id \`claw\`

**this.sounds.play(id: string): void** Play a sound by its id, loaded in the setup with _this.sounds.load_

Examples :

this.sounds.play('play'); 
this.sounds.play('claw'); 

  
**this.disableNextMoveSound(): void** Disable the standard "move" sound for this move (to replace it with your custom sound):

Add this to your notification handler:

this.disableNextMoveSound();

Note: it only disable the sound for the next move.

## Title bar and states

### Client states

Client states is a way to simulate the state transition but without actually going to server. It is usefull when you need to ask user multiple questions before you send things to server

**this.setClientState(newState: string, args: object)**

Example:

    this.setClientState("client\_playerPicksLocation", {
                              descriptionmyturn : \_("${you} must select location"),
                          });

For more information see [BGA\_Studio\_Cookbook#Multi\_Step\_Interactions:\_Select\_Worker.2FPlace\_Worker\_-\_Using\_Client\_States](/BGA_Studio_Cookbook#Multi_Step_Interactions:_Select_Worker.2FPlace_Worker_-_Using_Client_States "BGA Studio Cookbook")

To add custom string interpolation for the state title, you can add them by using the args object like so:

    this.setClientState("client\_playerPicksLocation", {
                             descriptionmyturn : \_("${you} must select location for card {$card\_number}"),
                             args: { card\_number: 5 },
                         });

**this.restoreServerGameState()** If you are in client state it will restore the current server state (cheap undo)

**this.on\_client\_state** Boolean indicating that we are in client state

### Title bar

**this.statusBar.setTitle(title: string, args?: object)**

Update the page title (aka status bar prompt). Can handle ${you} and ${actplayer} and any other var you would pass as args.

if (args.mandatory\_card\_name) {
    const mandatoryCardTitle = this.isCurrentPlayerActive() ? \_('${you} must take ${mandatory\_card\_name}') : \_('${actplayer} must take ${mandatory\_card\_name}');
    this.statusBar.setTitle(mandatoryCardTitle, args);
}

As opposed to `updatePageTitle`, it doesn't trigger the call `onUpdateActionButtons`.

**this.statusBar.addActionButton(label: string, callback: Function, params?: object): HTMLButtonElement**

Parameters:

-   label: the label to be shown, can be html. If label if used pass traslated string, i.e. \_('Yes')
-   callback: function to call on click, cannot be method name it has to be function
-   params: object optionally containing one of more of the following:
    -   color: string - can be `primary` (default) (blue), `secondary` (gray), `alert` (red)
    -   id: string - is the dom element id to set. If null/undefined, the button will not have an id, but you can still manipulate it by storing the reference to the DOM Element returned by the function
    -   classes: string|string\[\] - i.e `'disabled blinking'` or `['disabled', 'blinking']`.
    -   destination: ElementOrId - the DOM Element to add the button to. If not specified, will add it to the status bar.
    -   title: string - plain text description of the label. Should be set when the button label is an icon, for accessibility.
    -   disabled: boolean - makes the button disabled. Will prevent the callback to be executed
    -   tooltip: string - the tooltip of the button
    -   confirm: string | Function - the confirm message to display before triggering the callback, if set (or function handler, see example below).
    -   autoclick: boolean if the button should be auto clicked after a small delay (for Confirmation buttons).

Example of a standard call without params:

 this.statusBar.addActionButton(\_('Pass'), () => this.bgaPerformAction('actPass'));

Example of a standard call with params:

 this.statusBar.addActionButton(\_('Pass'), () => this.bgaPerformAction('actPass'), {
   id: 'my\_button',
   color: 'secondary',
   classes: 'my-outline-class'
   disabled: true,
   tooltip: \_('You cannot pass'),
   confirm: \_('Are you sure to pass?'),
 });

Note: the confirm parameter can be a string or a function that returns a string or null

Example of a standard call with params:

 this.statusBar.addActionButton(\_('Discard Selected Cards'), () => this.bgaPerformAction('actDiscard', { cardIds: this.getSelectedCardIds().join(',') }), {
   confirm: () => {
     if (this.getSelectedCardIds().length === 0) { // no card is selected, show the warning
       return \_('Are you sure you don't want to discard any cards?');
     } else { // cards are selected, do not show the warning
       return null;
     }
   }
 });

Example of confirm button with autoclick animation:

 this.statusBar.addActionButton(\_('Confirm'), () => this.bgaPerformAction('actConfirm'), {
   autoclick: true
 });
 // if you have a user preference to auto-confirm
 this.statusBar.addActionButton(\_('Confirm'), () => this.bgaPerformAction('actConfirm'), {
   autoclick: this.getGameUserPreference(100) == 1, // adapt to your user preference id and values
 });

  

**this.statusBar.removeActionButtons()**

Removes all buttons from title bar

**this.updatePageTitle()**

This function allows to update the current page title and turn description according to the game state arguments. If the current game state description this.gamedatas.gamestate.descriptionmyturn is modified before calling this function it allows to update the turn description without changing state. This will handle arguments substitutions properly.

Note: this functional also will calls this.onUpdateActionButtons, if you want different buttons then state defaults, use method in example to replace them, if it becomes too clumsy use client states (see above)

Example from Terra Mystica:

onClickFavorTile: function( evt ) {
    ...
    if ( ... ) {
        this.gamedatas.gamestate.descriptionmyturn = \_('Special action: ') + \_('Advance 1 space	on a Cult track');
        this.updatePageTitle();
        this.removeActionButtons();
        this.addActionButton( ... );
         ...
        return;
    }
    ...

}

## User preferences

**this.getGameUserPreference(pref\_id)**

Return the value of a user preference. It will return the value currently selected in the select combo box, in the top-right menu.

**this.setGameUserPreference(pref\_id, value)**

Programmatically change a user preference. It will have the same effect as if the player changed the value in the select combo box, in the top-right menu.

**this.onGameUserPreferenceChanged**

A callback you can define if you want to be notified when a user changes a user preference.

Example:

onGameUserPreferenceChanged: function(pref\_id, pref\_value) {
  switch (pref\_id) {
    case 201: 
      document.getElementsByTagName('html')\[0\].classList.toggle('dark-background', pref\_value == 2);
      break;
  }
}

## Other useful stuff

**dojo.hitch**

With dojo.hitch, you can create a callback function that will run with your game object context whatever happen.

Typical example: display a BGA confirmation dialog with a callback function created with dojo.hitch:

        this.confirmationDialog( \_('Are you sure you want to make this?'), dojo.hitch( this, function() {
            this.bgaPerformAction('makeThis');
        } ) );   

In the example above, using dojo.hitch, we ensure that the "this" object will be set when the callback is called.

NOTE: In modern JS there are lambdas that eliminate need for that, the example above will look like this

        this.confirmationDialog( \_('Are you sure you want to make this?'), () => this.bgaPerformAction( 'makeThis') );   

  

  

**onScreenWidthChange()**

This function can be overridden in your game to manage some resizing on the client side when the browser window is resized. This function is also triggered at load time, so it can be used to adapt to the :viewport size at the start of the game too.

  

  

this.bRealtime

Return true if the game is in realtime. Note that having a distinct behavior in realtime and turn-based should be exceptional.

g\_replayFrom

Global contains replay number in live game, it is set to undefined (i.e. not set) when it is not a replay mode, so consequentially the good check is **typeof g\_replayFrom != 'undefined'** which returns true if the game is in replay mode _during the game_ (the game is ongoing but the user clicked "replay from this move" in the log)

g\_archive\_mode

Returns true if the game is in archive mode _after the game_ (the game has ended)

  

this.instantaneousMode

Returns true during replay/archive mode if animations should be skipped. Only needed if you are doing custom animations. (The BGA-provided animation functions like _this.slideToObject()_ automatically handle instantaneous mode.)

Technically, when you click "replay from move #20", the system replays the game from the very beginning with moves 0 - 19 happening in instantaneous mode and moves 20+ happening in normal mode.

g\_tutorialwritten

Returns an object like the below if the game is in tutorial mode, or undefined otherwise. Tutorial mode is a special case of archive mode where comments have been added to a previous game to teach new players the rules.

   {
       author: "91577332",
       id: "576",
       mode: "view"
       status: "alpha"
       version\_override: null
       viewer\_id: "84554161"
   }

**getBgaEnvironment(): string**

Returns "studio" for studio and "prod" for production environment (i.e. where games current runs). Only useful for debbugging hooks.

Note: alpha server is also "prod"

Retrieved from "[http:///index.php?title=Game\_interface\_logic:\_yourgamename.js&oldid=26702](http:///index.php?title=Game_interface_logic:_yourgamename.js&oldid=26702)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")