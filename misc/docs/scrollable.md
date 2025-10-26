# Scrollmap - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# Scrollmap

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
-   Scrollmap: a JS component to manage a scrollable game area (useful when the game area can be infinite. Examples: Saboteur or Takenoko games).
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

In some games, players are building the main game area with tiles or cards and it has no boundaries. This causes an additional difficulty for the adaptation, because we have to display an infinite game area into a finite space on the screen. This is where Scrollmap component can help you.

Scrollmap is a BGA client side component to display an infinite game area. It supports Scrolling and Panning. Scrolling - allows user to scroll area inside the view port using the buttons drawn on the top/bottom/left/right. Panning - allows user to drag the surface area (using mouse).

  

## Contents

-   [1 Scrollmap in action](#Scrollmap_in_action)
-   [2 How to use Scrollmap](#How_to_use_Scrollmap)
-   [3 Scrollable area layers](#Scrollable_area_layers)
-   [4 Customizations](#Customizations)
    -   [4.1 Disable move arrows](#Disable_move_arrows)
    -   [4.2 Enable scrollmap zone extension](#Enable_scrollmap_zone_extension)
    -   [4.3 Fall-through mouse events on oversurface](#Fall-through_mouse_events_on_oversurface)
    -   [4.4 Avoid scrolling on touch devices](#Avoid_scrolling_on_touch_devices)
    -   [4.5 Zooming](#Zooming)
-   [5 API Reference](#API_Reference)
    -   [5.1 Constructor](#Constructor)
    -   [5.2 Properties](#Properties)
    -   [5.3 Methods](#Methods)

## Scrollmap in action

Examples of game that use Scrollmap (try on BGA or watch):

-   Carcassonne
-   Saboteur
-   Taluva

In these games, you can see that there are arrow controls around the main game area, so that players can use them to scroll the view. You can also use Panning (i.e drag'n'drop the game area to scroll).

  
**⚠ Note**: Some games are using an advanced scrollmap module which replaces the basic scrollmap described in this document.

Check **Alternative implementations of BGA modules** of **[BGA Code Sharing](/BGA_Code_Sharing "BGA Code Sharing")** for more information.

## How to use Scrollmap

Open your template (TPL) file and add this HTML code:

    <div id="map\_container">
    	<div id="map\_scrollable"></div>
        <div id="map\_surface"></div>
        <div id="map\_scrollable\_oversurface"></div>

        <div class="movetop"></div> 
	<div class="movedown"></div> 
	<div class="moveleft"></div> 
	<div class="moveright"></div> 
    </div>

There are also some lines to add to your CSS stylesheet. Please note that you can adapt it to your needs, especially the default width of the scrollable area. Do not change position and width/height of map\_surface - it suppose to fill be exactly as container size.

/\*\* Scrollable area \*\*/

#map\_container {
    position: relative;
    overflow: hidden;

    width: 100%;
    height: 400px;
}
#map\_scrollable, #map\_scrollable\_oversurface {
    position: absolute;
}
#map\_surface {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    cursor: move;
}

/\*\* This is some extra stuff to extend the container \*\*/

#map\_footer {
    text-align: center;
}

/\*\* Move arrows \*\*/

.movetop,.moveleft,.moveright,.movedown {
    display: block;
    position: absolute;
    background-image: url('../../../img/common/arrows.png');
    width: 32px;
    height: 32px;
}

.movetop {
    top: 0px;
    left: 50%;
    background-position: 0px 32px;
}
.moveleft {
    top: 50%;
    left: 0px;
    background-position: 32px 0px;
}
.moveright {
    top: 50%;
    right: 0px;
    background-position: 0px 0px;
}
.movedown {
    bottom: 0px;
    left: 50%;
    background-position: 32px 32px;
}

Now in the Javascript file, add "ebg/scrollmap" as a dependency:

define(\[
    "dojo","dojo/\_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/scrollmap"     /// <==== HERE
\],

  
Finally, to link your HTML code with your Javascript, place this in your Javascript "Setup" method:

        this.scrollmap = new ebg.scrollmap(); // declare an object (this can also go in constructor)
   	// Make map scrollable        	
        this.scrollmap.create( $('map\_container'),$('map\_scrollable'),$('map\_surface'),$('map\_scrollable\_oversurface') ); // use ids from template
        this.scrollmap.setupOnScreenArrows( 150 ); // this will hook buttons to onclick functions with 150px scroll step

This is it! Now, you should see on your game interface a scrollable game area. This is not really impressive though, because you didn't add anything on the game area yet. This is the next step.

## Scrollable area layers

There are two and only two places where you should place your elements in your scrollable area:

-   inside "map\_scrollable" div
-   inside "map\_scrollable\_oversurface" div

The difference is very important: "map\_scrollable" is beneath the surface that is used to pan scroll ("map\_surface"), and "map\_scrollable\_oversurface" is above this surface. In practice:

-   If some element on the game area need to be clicked (or any kind of user interaction), you should place it in map\_scrollable\_oversurface, otherwise no click can reach it.
-   If some element on the game area don't need to be clicked, you'd better place it in "map\_scrollable", so it is possible pan scroll from a point on this element.

Of course, all layers are scrolled synchronously.

Tips: in some situation, it's also useful to place a game element on map\_scrollable and a corresponding invisible element over the surface to manage the interactions. Example: when an interactive element must be placed beneath a non interactive element for display reason.

The other layer is 'map\_surface' - it is what user would use to pan scroll. If this div is not visible because 'map\_scrollable\_oversurface' covering it completely - panning will be impossible (unless you apply some css trickery, see 'Fall-through mouse events on oversurface' below).

  
By default, the game area (i.e. map\_scrollable\_oversurface) is centered on 0,0 coordinates.

Simple example of placing element (normally you would use slideObject and such...):

  dojo.create("div",{innerHTML: "hello" },"map\_scrollable\_oversurface");

This is the layers of the scrollmap, it is not how it looks like, its just to visually demonstrate what is what:

[![Scrollmap.png](/images/2/20/Scrollmap.png)](/File:Scrollmap.png)

If you wondering on how to make this picture - I added transform: translateZ to layers to visually split them and removed visibily: hidden from the container to show that "map\_scrollable" is the one that can grow without bounds and parts of it can be hidden because view port (the map\_container) can be much smaller. Also you can see that map\_scrollable\_oversurface and map\_scrollable are the layers that syncronized on position and size, and map\_surface is same size as map\_container.

  

## Customizations

### Disable move arrows

The buttons already defined in css and the buttons handler already defined in the scrollmap control just make sure you call function setupOnScreenArrows (and in .tpl file buttons have to be defined with exact these classes verbatim). If you don't want the buttons, do not call function setupOnScreenArrows() and remove them from template (you can also remove just some of them, i.e. only leave left and right).

  

### Enable scrollmap zone extension

This is optional, when there can be unused screen space under the scrollmap that a player might want to use. Add this in your .tpl after the scrollmap div (the matching css rules has already been defined):

<div id="map\_footer" class="whiteblock">
    <a href="#" id="enlargedisplay">↓  {LABEL\_ENLARGE\_DISPLAY}  ↓</a>
</div>

In your javascript, define the following function:

        onIncreaseDisplayHeight: function(evt) {
            console.log('Event: onIncreaseDisplayHeight');
            evt.preventDefault();
        	
            var cur\_h = toint(dojo.style( $('map\_container'), 'height'));
            dojo.style($('map\_container'), 'height', (cur\_h + 300) + 'px');
        },

and connect them to the 'enlargedisplay' link in your setup:

dojo.connect( $('enlargedisplay'), 'onclick', this, 'onIncreaseDisplayHeight' );

In your view.php file, define the template variable LABEL\_ENLARGE\_DISPLAY so that it gets substituted with appropriate translatable text:

$this->tpl\['LABEL\_ENLARGE\_DISPLAY'\] = self::\_("Enlarge display");

  

### Fall-through mouse events on oversurface

What if you have some non-square shapes on the surface and there is a lot of space in between the elemenets that can be used to pan scrolling but it currently not possible to grab on to map\_scrollable\_oversurface? To do that you have to make mouse click "fall-though" that layer, but stick on children. Add this to your css to solve this problem:

#map\_scrollable\_oversurface {
	pointer-events: none;
}
#map\_scrollable\_oversurface > \*{
	pointer-events: initial;
}

  

### Avoid scrolling on touch devices

When a scrollmap is used on touch devices (such as a smartphone), by default scrolling will scroll the entire page (and the elements in the scrollmap won't move). To avoid this, simply add the following in your CSS file:

#map\_container {
    touch-action: none;
}

  

### Zooming

**Ingredients:** ggg\_ggg.tpl, ggg.js

Zooming elements on the scrollmap can be difficult because the different layers need to stay aligned (and there are many layers, due to BGA's zoom feature, screen resolution, ...)

**ggg\_ggg.tpl**

Add 2 elements for zooming in and out

        <div id="zoomplus"></div>
        <div id="zoomminus"></div>

  
**ggg\_ggg.js**

Bind it to a zoom function

setup: function(gamedatas) {
     \[...\]
     this.trl\_zoom = 1;
     dojo.connect($('zoomplus'), 'onclick', () => this.onZoomButton(0.1));
     dojo.connect($('zoomminus'), 'onclick', () => this.onZoomButton(-0.1));
     \[...\]
},

onZoomButton: function(deltaZoom) {
    zoom = this.trl\_zoom + deltaZoom;
    this.trl\_zoom = zoom <= 0.2 ? 0.2 : zoom >= 2 ? 2 : zoom;
    dojo.style($('map\_scrollable'), 'transform', 'scale(' + this.trl\_zoom + ')');
    dojo.style($('map\_scrollable\_oversurface'), 'transform', 'scale(' + this.trl\_zoom + ')');
},

Note: you may also link this to a user preference (see [this page](/Game_options_and_preferences:_gameoptions.inc.php#Updating_preference_from_code "Game options and preferences: gameoptions.inc.php") for more information).

## API Reference

### Constructor

ebg.scrollmap()

return newly created scrollmap object

### Properties

no public properties

### Methods

create( container\_div, undersurface\_div, surface\_div, onsurface\_div )

Object initializer, must be called before usage.

Parameters:

\[HTMLElement\] container\_div - dom node of the container

\[HTMLElement\] undersurface\_div - dom node of the layer under the scrollable surface, called map\_scrollable in the template example

\[HTMLElement\] surface\_div - dom node of the area inside the container that is used for panning

\[HTMLElement\] onsurface\_div - dom node of the layer above the scrollable surface, called map\_scrollable\_oversurface in the template example

Note: this method also calles scrollto(0,0) which has animation, if you want to play with positioning you have to wait for it to finish (i.e. 350 ms for now).

scroll( dx, dy \[, duration \[, delay \]\])

Scroll content using relative offset (animated)

Parameters:

\[Number\] dx - x offset

\[Number\] dy - y offset

\[Number\] duration - animation duration, default is 350

\[Number\] delay - animation duration, default is 0

  

scrollto( x, y \[, duration \[, delay \]\])

Scroll the board to make it centered on given position (0,0 will scroll to center)

Parameters:

\[Number\] x - position relative to center

\[Number\] y - position relative to center

\[Number\] duration - animation duration, default is 350

\[Number\] delay - animation duration, default is 0

disableScrolling()

Disable scrolling (that disable both scrolling and panning)

enableScrolling()

Enable scrolling (re-enable scrolling)

Retrieved from "[http:///index.php?title=Scrollmap&oldid=23072](http:///index.php?title=Scrollmap&oldid=23072)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")