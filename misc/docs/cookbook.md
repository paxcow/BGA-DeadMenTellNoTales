# BGA Studio Cookbook - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# BGA Studio Cookbook

From Board Game Arena

[Jump to navigation](#mw-head) [Jump to search](#searchInput)

  
This page is a cookbook of design and implementation recipes for BGA Studio framework. For tooling and usage recipes see [Tools and tips of BGA Studio](/Tools_and_tips_of_BGA_Studio "Tools and tips of BGA Studio"). If you have your own recipes feel free to edit this page.

## Contents

-   [1 Visual Effects, Layout and Animation](#Visual_Effects,_Layout_and_Animation)
    -   [1.1 DOM manipulatons](#DOM_manipulatons)
        -   [1.1.1 Create pieces dynamically (using template)](#Create_pieces_dynamically_\(using_template\))
        -   [1.1.2 Create pieces dynamically (using string concatenation)](#Create_pieces_dynamically_\(using_string_concatenation\))
        -   [1.1.3 Create all pieces statically](#Create_all_pieces_statically)
        -   [1.1.4 Use player color in template](#Use_player_color_in_template)
    -   [1.2 Status bar](#Status_bar)
        -   [1.2.1 Changing state prompt](#Changing_state_prompt)
    -   [1.3 Animation](#Animation)
        -   [1.3.1 Attach to new parent without destroying the object](#Attach_to_new_parent_without_destroying_the_object)
        -   [1.3.2 Animation on oversurface](#Animation_on_oversurface)
        -   [1.3.3 Scroll element into view](#Scroll_element_into_view)
        -   [1.3.4 Set Auto-click timer for buttons (setAutoClick)](#Set_Auto-click_timer_for_buttons_\(setAutoClick\))
    -   [1.4 Logs](#Logs)
        -   [1.4.1 Inject icon images in the log](#Inject_icon_images_in_the_log)
        -   [1.4.2 Inject images and styled html in the log](#Inject_images_and_styled_html_in_the_log)
            -   [1.4.2.1 Define this.bgaFormatText() method](#Define_this.bgaFormatText\(\)_method)
            -   [1.4.2.2 Use :formatFunction option provided by dojo.string.substitute](#Use_:formatFunction_option_provided_by_dojo.string.substitute)
            -   [1.4.2.3 Use transform argument of dojo.string.substitute](#Use_transform_argument_of_dojo.string.substitute)
        -   [1.4.3 Processing logs on re-loading](#Processing_logs_on_re-loading)
        -   [1.4.4 Overriding format\_string\_recursive to inject HTML into log, including adding tooltips to log](#Overriding_format_string_recursive_to_inject_HTML_into_log,_including_adding_tooltips_to_log)
    -   [1.5 Player Panel](#Player_Panel)
        -   [1.5.1 Inserting non-player panel](#Inserting_non-player_panel)
    -   [1.6 Images and Icons](#Images_and_Icons)
        -   [1.6.1 Accessing images from js](#Accessing_images_from_js)
        -   [1.6.2 High-Definition Graphics](#High-Definition_Graphics)
        -   [1.6.3 Using CSS to create different colors of game pieces if you have only white piece](#Using_CSS_to_create_different_colors_of_game_pieces_if_you_have_only_white_piece)
        -   [1.6.4 Accessing player avatar URLs](#Accessing_player_avatar_URLs)
        -   [1.6.5 Adding Image buttons](#Adding_Image_buttons)
    -   [1.7 Other Fluff](#Other_Fluff)
        -   [1.7.1 Use thematic fonts](#Use_thematic_fonts)
    -   [1.8 Scale to fit for big boards](#Scale_to_fit_for_big_boards)
    -   [1.9 Dynamic tooltips](#Dynamic_tooltips)
    -   [1.10 Rendering text with players color and proper background](#Rendering_text_with_players_color_and_proper_background)
    -   [1.11 Cool realistic shadow effect with CSS](#Cool_realistic_shadow_effect_with_CSS)
        -   [1.11.1 Rectangles and circles](#Rectangles_and_circles)
        -   [1.11.2 Irregular Shapes](#Irregular_Shapes)
        -   [1.11.3 Shadows with clip-path](#Shadows_with_clip-path)
    -   [1.12 Using the CSS classes from the state machine](#Using_the_CSS_classes_from_the_state_machine)
-   [2 Game Model and Database design](#Game_Model_and_Database_design)
    -   [2.1 Database for The euro game](#Database_for_The_euro_game)
    -   [2.2 Database for The card game](#Database_for_The_card_game)
-   [3 Game Elements](#Game_Elements)
    -   [3.1 Resource](#Resource)
        -   [3.1.1 Representation in Database](#Representation_in_Database)
        -   [3.1.2 Representation in Material File (material.inc.php)](#Representation_in_Material_File_\(material.inc.php\))
        -   [3.1.3 HTML Representation](#HTML_Representation)
        -   [3.1.4 JavaScript Handling](#JavaScript_Handling)
        -   [3.1.5 Graphic Representation (.css)](#Graphic_Representation_\(.css\))
        -   [3.1.6 Selection and Actions](#Selection_and_Actions)
    -   [3.2 Meeple](#Meeple)
        -   [3.2.1 Representation in Database](#Representation_in_Database_2)
        -   [3.2.2 Representation in Material File (material.inc.php)](#Representation_in_Material_File_\(material.inc.php\)_2)
        -   [3.2.3 HTML Representation](#HTML_Representation_2)
        -   [3.2.4 JavaScript Handling](#JavaScript_Handling_2)
        -   [3.2.5 Graphic Representation (.css)](#Graphic_Representation_\(.css\)_2)
        -   [3.2.6 Selection and Actions](#Selection_and_Actions_2)
    -   [3.3 Dice](#Dice)
        -   [3.3.1 Representation in Database](#Representation_in_Database_3)
        -   [3.3.2 Representation in Material File (material.inc.php)](#Representation_in_Material_File_\(material.inc.php\)_3)
        -   [3.3.3 HTML Representation](#HTML_Representation_3)
        -   [3.3.4 JavaScript Handling (.js)](#JavaScript_Handling_\(.js\))
        -   [3.3.5 Graphic Representation (.css)](#Graphic_Representation_\(.css\)_3)
        -   [3.3.6 Selection and Actions](#Selection_and_Actions_3)
    -   [3.4 Card](#Card)
        -   [3.4.1 Representation in Database](#Representation_in_Database_4)
        -   [3.4.2 Representation in Material](#Representation_in_Material)
        -   [3.4.3 HTML Representation](#HTML_Representation_4)
        -   [3.4.4 JavaScript Handling (.js)](#JavaScript_Handling_\(.js\)_2)
        -   [3.4.5 Graphic Representation (.css)](#Graphic_Representation_\(.css\)_4)
        -   [3.4.6 Selection and Actions](#Selection_and_Actions_4)
        -   [3.4.7 Card Layouts](#Card_Layouts)
    -   [3.5 Hex Tiles](#Hex_Tiles)
    -   [3.6 Tetris Tiles](#Tetris_Tiles)
    -   [3.7 Track](#Track)
-   [4 Code Organization](#Code_Organization)
    -   [4.1 Including your own JavaScript module](#Including_your_own_JavaScript_module)
    -   [4.2 Including your own JavaScript module (II)](#Including_your_own_JavaScript_module_\(II\))
    -   [4.3 Including your own PHP module](#Including_your_own_PHP_module)
    -   [4.4 Creating a test class to run PHP locally](#Creating_a_test_class_to_run_PHP_locally)
    -   [4.5 Avoiding code in dojo declare style](#Avoiding_code_in_dojo_declare_style)
    -   [4.6 More readable JS: onEnteringState](#More_readable_JS:_onEnteringState)
    -   [4.7 Frameworks and Preprocessors](#Frameworks_and_Preprocessors)
    -   [4.8 PHP Migration](#PHP_Migration)
-   [5 Backend](#Backend)
    -   [5.1 Assigning Player Order](#Assigning_Player_Order)
    -   [5.2 Send different notifications to active player vs everybody else](#Send_different_notifications_to_active_player_vs_everybody_else)
    -   [5.3 Send transient notifications without incrementing move ID](#Send_transient_notifications_without_incrementing_move_ID)
-   [6 Assorted Stuff](#Assorted_Stuff)
    -   [6.1 Out-of-turn actions: Un-pass](#Out-of-turn_actions:_Un-pass)
    -   [6.2 Multi Step Interactions: Select Worker/Place Worker - Using Selection](#Multi_Step_Interactions:_Select_Worker/Place_Worker_-_Using_Selection)
    -   [6.3 Multi Step Interactions: Select Worker/Place Worker - Using Client States](#Multi_Step_Interactions:_Select_Worker/Place_Worker_-_Using_Client_States)
    -   [6.4 Action Stack - Using Client States](#Action_Stack_-_Using_Client_States)
    -   [6.5 Action Stack - Using Server States](#Action_Stack_-_Using_Server_States)
    -   [6.6 Custom error/exception handling in JavaScript](#Custom_error/exception_handling_in_JavaScript)
    -   [6.7 Force players to refresh after new deploy](#Force_players_to_refresh_after_new_deploy)
    -   [6.8 Disable / lock table creation for new deploy](#Disable_/_lock_table_creation_for_new_deploy)
    -   [6.9 Local Storage](#Local_Storage)
    -   [6.10 Capture client JavaScript errors in the "unexpected error" log](#Capture_client_JavaScript_errors_in_the_"unexpected_error"_log)
-   [7 Algorithms](#Algorithms)
    -   [7.1 Generate permutations in lexicographic order](#Generate_permutations_in_lexicographic_order)

## Visual Effects, Layout and Animation

### DOM manipulatons

#### Create pieces dynamically (using template)

**Ingredients:** ggg\_ggg.tpl, ggg.js

Note: this method is recommended by BGA guildlines

Declared js template with variables in .tpl file, like this

<script type="text/javascript">
    // Javascript HTML templates
    var jstpl\_ipiece = '<div class="${type} ${type}\_${color} inlineblock" aria-label="${name}" title="${name}"></div>';
</script>

Use it like this in .js file

 div = this.format\_block('jstpl\_ipiece', {
                               type : 'meeple',
                               color : 'ff0000',
                               name : 'Bob',
                           });
 

Then you do whatever you need to do with that div, this one specifically design to go to log entries, because it has embedded title (otherwise its a picture only) and no id.

Note: you could have place this variable in js itself, but keeping it in .tpl allows you to have your js code be free of HTML. Normally it never happens but it is good to strive for it. Note: you can also use string concatenation, its less readable. You can also use dojo dom object creation api's but its brutally verbose and its more unreadable.

  

#### Create pieces dynamically (using string concatenation)

**Ingredients:** ggg.js

  

  div = "<div class='meeple\_"+color+"'></div>";

or modern way

  div = \`<div class='meeple\_${color}'></div>\`;

#### Create all pieces statically

**Ingredients:** ggg\_ggg.tpl, ggg.css, ggg.view.php (optional)

-   Create ALL game pieces in html template (.tpl)
-   ALL pieces should have unique id, and it should be meaningful, i.e. meeple\_red\_1
-   Do not use inline styling
-   Id of player's specific pieces should use some sort of 'color' identification, since player id cannot be used in static layout, you can use english color name, hex 6 char value, or color "number" (1,2,3...)
-   Pieces should have separated class for its color, type, etc, so it can be easily styled in groups. In example below you now can style all meeples, all red meeples or all red tokens, or all "first" meeples

ggg.tpl:

 
  <div id="home\_red" class="home\_red home">
     <div id="meeple\_red\_1" class="meeple red n1"></div>
     <div id="meeple\_red\_2" class="meeple red n2"></div>
  </div>

ggg.css:

.meeple {
	width: 32px;
	height: 39px;
	background-image: url(img/78\_64\_stand\_meeples.png);
	background-size: 352px;
}

.meeple.red {
	background-position: 30% 0%;
}

-   There should be straight forward mapping between server id and js id (or 1:1)
-   You place objects in different zones of the layout, and setup css to take care of layout

.home .meeple{
   display: inline-block;
}

-   If you need to have a temporary object that look like original you can use dojo.clone (and change id to some temp id)
-   If there is lots of repetition or zone grid you can use template generator, but inject style declaration in css instead of inline style for flexibility

Note:

-   If you use this model you cannot use premade js components such as Stock and Zone
-   You have to use alternative methods of animation (slightly altered) since default method will leave object with inline style attributes which you don't need

#### Use player color in template

NOTE: view.php is deprecated, its best to generate html from .js

**Ingredients:** ggg\_ggg.tpl, ggg.view.php

.view.php:

    function build\_page($viewArgs) {
        // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players\_nbr = count($players);
        /\*\*
         \* \*\*\*\*\*\*\*\*\* Place your code below: \*\*\*\*\*\*\*\*\*\*\*
         \*/
        
        // Set PCOLOR to the current player color hex
        $cplayer = $this->getCurrentPlayerId();
        if (array\_key\_exists($cplayer, $players)) { // may be not set if spectator
            $player\_color = $players \[$cplayer\] \['player\_color'\];
        } else {
            $player\_color = 'ffffff'; // spectator
        }
        $this->tpl \['PCOLOR'\] = $player\_color;

### Status bar

#### Changing state prompt

State prompt is message displayed for player which usually comes from state description. Sometimes you want to change it without changing state (one way is change state but locally, see client states above).

Simple way just change the html

        setMainTitle: function(text) {
            $('pagemaintitletext').innerHTML = text;
        },
         // usage
        onMeeple: function(event) {
              //... 
              this.setMainTitle(\_('You must select where meeple is going'));
        },

This however will not work with parameters and will not draw You in color, if you want this its more sophisticated:

        setDescriptionOnMyTurn : function(text) {
            this.gamedatas.gamestate.descriptionmyturn = text;
            var tpl = dojo.clone(this.gamedatas.gamestate.args);
            if (tpl === null) {
                tpl = {};
            }
            var title = "";
            if (this.isCurrentPlayerActive() && text !== null) {
                tpl.you = this.divYou(); 
            }
            title = this.format\_string\_recursive(text, tpl);

            if (!title) {
                this.setMainTitle(" ");
            } else {
                this.setMainTitle(title);
            }
        },

Note: this method uses **setMainTitle** defined above and **divYou** defined in another section of this wiki.

  

### Animation

#### Attach to new parent without destroying the object

BGA function attachToNewParent for some reason destroys the original, if you want similar function that does not you can use this ggg.js

        /\*\*
         \* This method will attach mobile to a new\_parent without destroying, unlike original attachToNewParent which destroys mobile and
         \* all its connectors (onClick, etc)
         \*/
        attachToNewParentNoDestroy: function (mobile\_in, new\_parent\_in, relation, place\_position) {

            const mobile = $(mobile\_in);
            const new\_parent = $(new\_parent\_in);

            var src = dojo.position(mobile);
            if (place\_position)
                mobile.style.position = place\_position;
            dojo.place(mobile, new\_parent, relation);
            mobile.offsetTop;//force re-flow
            var tgt = dojo.position(mobile);
            var box = dojo.marginBox(mobile);
            var cbox = dojo.contentBox(mobile);
            var left = box.l + src.x - tgt.x;
            var top = box.t + src.y - tgt.y;

            mobile.style.position = "absolute";
            mobile.style.left = left + "px";
            mobile.style.top = top + "px";
            box.l += box.w - cbox.w;
            box.t += box.h - cbox.h;
            mobile.offsetTop;//force re-flow
            return box;
        },

#### Animation on oversurface

If you use non-absolute position for your game elements (i.e you use layouts) - you cannot really use BGA animation functions. After years of fidding with different options I use techique which I call animation on oversurface that works when parents use different zoom, rotation, etc

-   You need another layer on top of everything - oversurface
-   We create copy of the object on oversurface - to move
-   We move the real object on final position - but make it invisible for now
-   We move the phantom to final position applying required zoom and rotation (using css animation), then destroy it
-   When animation is done we make original object visible in new position

The code is bit complex it can be found here

[https://codepen.io/VictoriaLa/pen/gORvdJo](https://codepen.io/VictoriaLa/pen/gORvdJo)

Game using it: century, ultimaterailroads

#### Scroll element into view

Ingredients: game.js

This function will scroll given node (div) into view and respect replays and archive mode

    scrollIntoViewAfter: function (node, delay) {
      if (this.instantaneousMode || this.inSetup) {
        return;
      }
      if (typeof g\_replayFrom != "undefined") {
        $(node).scrollIntoView();
        return;
      }
      if (!delay) delay = 0;
      setTimeout(() => {
        $(node).scrollIntoView({ behavior: "smooth", block: "center" });
      }, delay);
    },

#### Set Auto-click timer for buttons (setAutoClick)

Sets up auto-click for a button after a timeout, with the new progress-bar animation. Works in both JS and TS. You can pass the optional parameters (see code comments) or simply call as:

this.setAutoClick(document.getElementById('someID');

Note: use it only if this.statusBar.addActionButton(..., { autoclick: true }) doesn't fit your needs! Don't hesitate to tell BGA why it doesn't fit your needs.

**JavaScript**

/\*\*
\* Sets up auto-click functionality for a button after a timeout period
\* @param button - The button HTML element to auto-click
\* @param timeoutDuration - Optional base duration in ms before auto-click occurs (default: 5000)
\* @param randomIncrement - Optional random additional ms to add to timeout (default: 2000)
\* @param autoClickID - Optional ID for the auto-click events, multiple buttons can therefore point to the same autoClick event
\* @param onAnimationEnd - Optional callback that returns boolean to control if click should occur (default: true)
\*/
setAutoClick: function(button, timeoutDuration = 5000, randomIncrement = 2000, autoClickID = null, onAnimationEnd = () => true) {
    const totalDuration = timeoutDuration + Math.random() \* randomIncrement;
    this.setAutoClick.timeouts = this.setAutoClick.timeouts || {};
            
    if(!autoClickID){
        this.setAutoClick.autoClickIncrement = this.setAutoClick.autoClickIncrement || 1;
        autoClickID = 'auto-click-' + this.setAutoClick.autoClickIncrement++;
    }
    this.setAutoClick.timeouts\[autoClickID\] = this.setAutoClick.timeouts\[autoClickID\] || \[\];

    button.style.setProperty('--bga-autoclick-timeout-duration', \`${totalDuration}ms\`);
    button.classList.add('bga-autoclick-button');

    const stopDoubleTrigger = () => {
        if(!this.setAutoClick.timeouts\[autoClickID\]) return;
        this.setAutoClick.timeouts\[autoClickID\].forEach(timeout => clearTimeout(timeout));
        delete this.setAutoClick.timeouts\[autoClickID\];
    }
    button.addEventListener('click', stopDoubleTrigger, true);
               
    this.setAutoClick.timeouts\[autoClickID\].push(
        setTimeout(() => {
            stopDoubleTrigger();
            if (!document.body.contains(button)) return;
            const customEventResult = onAnimationEnd();
            if (customEventResult) button.click();
        }, totalDuration)
    );
},

**TypeScript**

/\*\*
\* Sets up auto-click functionality for a button after a timeout period
\* @param button - The button HTML element to auto-click
\* @param timeoutDuration - Optional base duration in ms before auto-click occurs (default: 5000)
\* @param randomIncrement - Optional random additional ms to add to timeout (default: 2000)
\* @param autoClickID - Optional ID for the auto-click events, multiple buttons can therefore point to the same autoClick event
\* @param onAnimationEnd - Optional callback that returns boolean to control if click should occur (default: true)
\*/
public setAutoClick(button: HTMLDivElement, timeoutDuration: number = 5000, randomIncrement: number = 2000, autoClickID: string = null, onAnimationEnd: () => boolean = () => true){
    const fn = this.setAutoClick as typeof this.setAutoClick & {
        timeouts?: Record<string, number\[\]>;
        autoClickIncrement?: number;
    };
    fn.timeouts = fn.timeouts || {};
        
    const totalDuration = timeoutDuration + Math.random() \* randomIncrement;

    if(!autoClickID){
        fn.autoClickIncrement = fn.autoClickIncrement || 1;
        autoClickID = 'auto-click-' + fn.autoClickIncrement++;
    }

    fn.timeouts\[autoClickID\] = fn.timeouts\[autoClickID\] || \[\];

    button.style.setProperty('--bga-autoclick-timeout-duration', \`${totalDuration}ms\`);
    button.classList.add('bga-autoclick-button');

    const stopDoubleTrigger = () => {
        if(!fn.timeouts\[autoClickID\]) return;
        fn.timeouts\[autoClickID\].forEach(timeout => clearTimeout(timeout));
        delete fn.timeouts\[autoClickID\];
    }
    button.addEventListener('click', stopDoubleTrigger, true);
            
    fn.timeouts\[autoClickID\].push(
        setTimeout(() => {
            stopDoubleTrigger();
            if (!document.body.contains(button)) return;
            const customEventResult = onAnimationEnd();
            if (customEventResult) button.click();
        }, totalDuration)
    );
}

### Logs

#### Inject icon images in the log

Here is an example of what was done for Terra Mystica which is simple and straightforward:

//Define the proper message
		$message = clienttranslate('${player\_name} gets ${power\_income} via Structures');
		if ($price > 0) {
			$this->playerScore->inc($player\_id, -$price);
			$message = clienttranslate('${player\_name} pays ${vp\_price} and gets ${power\_income} via Structures');
		}

// Notify
		$this->notify->all( "powerViaStructures", $message, array(
			'i18n' => array( ),
			'player\_id' => $player\_id,
			'player\_name' => $this->getPlayerNameById($player\_id),
			'power\_tokens' => $power\_tokens,
			'vp\_price' => $this->getLogsVPAmount($price),
			'power\_income' => $this->getLogsPowerAmount($power\_income),
			'newScore' => $this->playerScore->get($player\_id),
			'counters' => $this->getGameCounters(null),
		) );

With some functions to have the needed html added inside the substitution variable, such as:

function getLogsPowerAmount( $amount ) {
		return "<div class='tmlogs\_icon' title='Power'><div class='power\_amount'>$amount</div></div>";
}

Note: injecting html from php is not ideal but easy, if you want more clean solution, use method below but it is a lot more sophisticated.

#### Inject images and styled html in the log

Warning — Translation

**In order to prevent interference with the translation process, keep in mind that you must only apply modifications to the args object, and not try to substitute the keys (the `${player_name}` parts of your string) in the log string.**

So you want nice pictures in the game log. What do you do? The first idea that comes to mind is to send html from php in notifications (see method above).

This is a bad idea for many reasons:

-   It's bad architecture. ui elements leak into the server, and now you have to manage the ui in multiple places.
-   If you decided to change something in the ui in future version, replay logs for old games and tutorials may not work, since they use stored notifications.
-   Log previews for old games become unreadable. (This is the log state before you enter the game replay, which is useful for troubleshooting and game analysis.)
-   It's more data to transfer and store in the db.
-   It's a nightmare for translators.

So what else can you do? You can use client side log injection to intercept log arguments (which come from the server) and replace them with html on the client side. Here are three different method you can use to achieve this.

##### Define `this.bgaFormatText()` method

**Ingredients:** ggg.js, ggg.game.php

I use this recipe for **client side log injection** to intercept log arguments (which come from the server) and replace them with html on the client side.

[![Clientloginjection.png](/images/5/5e/Clientloginjection.png)](/File:Clientloginjection.png)

ggg.js

 

        /\*\* Declare this function to inject html into log items. \*/

        bgaFormatText : function(log, args) {
            try {
                if (log && args && !args.processed) {
                    args.processed = true;
                    

                    // list of special keys we want to replace with images
                    const keys = \['place\_name','token\_name'\];
                    
                  
                    for (let i in keys) {
                        const key = keys\[i\];
                        if (args\[key\]) args\[key\] = this.getTokenDiv(key, args);                            

                    }
                }
            } catch (e) {
                console.error(log,args,"Exception thrown", e.stack);
            }
            return { log, args };
        },

  
**Important:** In the _bgaFormatText_ method, the 'args' parameter will only contain arguments passed to it from the notify method in Game.php (see below).

The 'log' parameter is the actual string that is inserted into the logs. You can perform additional js string manipulation on it.

  

        getTokenDiv : function(key, args) {
            // ... implement whatever html you want here, example from sharedcode.js
            var token\_id = args\[key\];
            var item\_type = getPart(token\_id,0);
            var logid = "log" + (this.globalid++) + "\_" + token\_id;
            switch (item\_type) {
                case 'wcube':
                    var tokenDiv = this.format\_block('jstpl\_resource\_log', {
                        "id" : logid,
                        "type" : "wcube",
                        "color" : getPart(token\_id,1),
                    });
                    return tokenDiv;
             
                case 'meeple':
                    if ($(token\_id)) {
                        var clone = dojo.clone($(token\_id));
    
                        dojo.attr(clone, "id", logid);
                        this.stripPosition(clone);
                        dojo.addClass(clone, "logitem");
                        return clone.outerHTML;
                    }
                    break;
     
                default:
                    break;
            }

            return "'" + this.clienttranslate\_string(this.getTokenName(token\_id)) + "'";
       },
       getTokenName : function(key) {
           return this.gamedatas.token\_types\[key\].name; // get name for the key, from static table for example
       },

Note that in this case the server simply injects token\_id as a name, and the client substitutes it for the translated name or the picture.

  
Game.php:

          $this->notify->all('playerLog', clienttranslate('Game moves ${token\_name}'), \['token\_name'=>$token\_id\]);

**Important:** As noted above, only arguments actually passed by this method are available to the args parameter received in the client-side _bgaFormatText_ method.

Sometimes it is the case that you want to pass arguments that are not actually included in the output message. For example, suppose we have a method like this:

          $this->notify->all('tokenPlaced', clienttranslate('Player placed ${token\_name}'), array(
             'token\_name' => $token\_id,
             'zone\_played' => $zone);

This will output "Player placed ${token\_name}" in the log, and if we subscribe to a notification method activated by the "tokenPlaced" event in the client-side code, that method can make use of the 'zone\_played' argument.

Now if you want to make some really cool things with game log, most probably you would need more arguments than are included in log message. The problem with that, it will work at first, but if you reload game using F5 or when the game loads in turn based mode, you will loose your additional parameters, why? Because when game reloads it does not actually send same notifications, it sends special "hitstorical\_log" notification where all parameters not listed in the message are removed. In example above, field zone\_played would be removed from historical log as it is not included in message of the notification. You can till preserve specific arguments in historical log by adding special field preserve to notification arguments like this:

           $this->notify->all('tokenPlaced', clienttranslate('Player placed ${token\_name}'), array(
              'token\_name' => $token\_id,
              'zone\_played' => $zone,
              'preserve' => \[ 'zone\_played' \]
           );

Now you can use zone\_played in bgaFormatText even in historical logs.

##### Use `:formatFunction` option provided by `dojo.string.substitute`

**Ingredients:** ggg.js, ggg.game.php, ggg\_ggg.tpl, ggg.css

The above method will work in most of the cases, but if you use dotted keys such as `${card.name}` (which is supported by the framework, for private state args), the key won't be substituted because the `key in arg` test will fail. If so you need to rely either on this way, or the one after.

**WARNING:** using this method on an already advanced project will require you to go through all your notifications to change keys !

Under the hood, the **this.format\_string\_recursive()** function calls the **dojo.string.substitute** method which substitutes `${keys}` with the value provided. If you take a look at the [documentation](https://dojotoolkit.org/reference-guide/1.7/dojo/string.html#substitute) and [source code](https://github.com/dojo/dojo/blob/c3ceb017cfa25b703f5662dc83d1c8aae9bc5d81/string.js#L163) you can notice that the key can be suffixed with a colon (`:`) followed by a function name. This will allow you to specify directly in the substitution string which keys need HTML injection.

First of all, you need to define your formatting function in the ggg.js file:

\[\[ggg.js\]\]
        getTokenDiv : function(value, key) {
            //This is only an example implementation, you need to write your own.
            //The method should return HTML code
            switch (key) {
                case 'html\_injected\_argument1':
                    return this.format\_block('jstpl\_HTMLLogElement1',{value: value});
                case 'html\_injected\_argument2':
                    return this.format\_block('jstpl\_HTMLLogElement2',{value: value});
                ...
            }
       }

Obviously you need to define the appropriate templates in the ggg\_ggg.tpl file:

\[\[ggg\_ggg.tpl\]\]
let jstpl\_HTMLLogElement1 = '<div class="log-element log-element-1-${value}"></div>';
let jstpl\_HTMLLogElement2 = '<div class="log-element log-element-2-${value}"></div>';
...

And the appropriate classes in ggg.css.

Then you need to add the `dojo/aspect` module at the top of the ggg.js file:

\[\[ggg.js\]\]
define(\[
    "dojo", "dojo/\_base/declare",
    **"dojo/aspect",                 //MUST BE IN THIRD POSITION** (see [below](#Including_your_own_JavaScript_module_\(II\)))
    "ebg/core/gamegui",
    "ebg/counter",
\], function (dojo, declare, **aspect**) {
...

And you also need to add the following code in your `contructor` method in the ggg.js:

\[\[ggg.js\]\]
        constructor: function(){

            // ... skipped code ...
            let gameObject = this;            //Needed as the this object in aspect.before will not refer to the game object in which the formatting function resides
            aspect.before(dojo.string, "substitute", function(template, map, transform) {      //This allows you to modify the arguments of the dojo.string.substitute method before they're actually passed to it
                return \[template, map, transform, gameObject\];
            });

Now you're all set to inject HTML in your logs. To actually achieve this, you must specify the function name with the key like so:

\[\[ggg.game.php\]\]
$this->notify->all("notificationName", clienttranslate("This log message contains ${plainTextArgument} and the following will receive HTML injection: ${html\_injected\_argument1:getTokenDiv}"), \[
    "plainTextArgument" => "some plain text here",
    "html\_injected\_argument1" => "some value used by getTokenDiv",
\]);

You're not limited writing only one function, you can write as many functions as you like, and have them each inject a specific type of HTML. You just need to specify the relevant function name after the column in the substitution key.

##### Use `transform` argument of `dojo.string.substitute`

**Ingredients:** ggg.js, ggg.game.php, ggg\_ggg.tpl, ggg.css

This method is also relying on the use of `dojo.string.substitute` by the framework, and will use the `transform` argument, which, accordting to [source code](https://github.com/dojo/dojo/blob/c3ceb017cfa25b703f5662dc83d1c8aae9bc5d81/string.js#L163) and [documentation](https://dojotoolkit.org/reference-guide/1.7/dojo/string.html#substitute) will be run on all the messages going through dojo.string.substitute.

**WARNING:** This method will be applied to all strings that go through dojo.string.substitute. As such you must take extra care not to substitute keys that may be used by the framework (i.e. ${id}). In order to do so, a good practise would be to prefix all keys that need substitution with a trigram of the game name.

Since all the keys will be fed to the tranform function, by default, it must return the value, substituted or not per your needs. You can define the function like this in the ggg.js file:

\[\[ggg.js\]\]
        getTokenDiv : function(value, key) {
            //This is only an example implementation, you need to write your own.
            //The method should return HTML code
            switch (key) {
                case 'html\_injected\_argument1':
                    return this.format\_block('jstpl\_HTMLLogElement1',{value: value});
                case 'html\_injected\_argument2':
                    return this.format\_block('jstpl\_HTMLLogElement2',{value: value});
                ...
                default:
                    return value; //Needed otherwise regular strings won't appear since since the value isn't returned by the function
            }
        }

The templates must be defined in the ggg\_ggg.tpl file and the corresponding CSS classes in the ggg.css file.

You need to add the following code at the beginning of the ggg.js file:

\[\[ggg.js\]\]
define(\[
    "dojo", "dojo/\_base/declare",
    **"dojo/aspect",                 //MUST BE IN THIRD POSITION** (see [below](#Including_your_own_JavaScript_module_\(II\)))
    "ebg/core/gamegui",
    "ebg/counter",
\], function (dojo, declare, **aspect**) {
...

And the following code to the `constructor` method in ggg.js:

\[\[ggg.js\]\]
        constructor: function(){
            // ... skipped code ...
            let transformFunction = dojo.hitch(this, "getTokenDiv");          //Needed as the this object in aspect.before will not refer to the game object in which the formatting function resides
            aspect.before(dojo.string, "substitute", function(template, map, transform) {
                if (undefined === transform) {    //Check for a transform function presence, just in case
                    return \[template, map, transformFunction\];
                }
            });

Then you're all set for log injection, no need to change anything on the PHP side.

#### Processing logs on re-loading

You rarely need to process logs when reloading, but if you want to do something fancy you may have to do it after logs are loaded. Logs are loaded asyncronously so you have to listen for logs to be fully loaded. Unfortunately there is no direct way of doing it so this is the hack.

**Hack alert** - this extends undocumented function and may be broken when framework is updated

**Ingredients:** ggg.js

			/\*
  			\* \[Undocumented\] Override BGA framework functions to call onLoadingLogsComplete when loading is done
                        @Override
   			\*/
			setLoader: function(image\_progress, logs\_progress) {
				this.inherited(arguments); // required, this is "super()" call, do not remove
				//console.log("loader", image\_progress, logs\_progress)
				if (!this.isLoadingLogsComplete && logs\_progress >= 100) {
					this.isLoadingLogsComplete = true; // this is to prevent from calling this more then once
					this.onLoadingLogsComplete();
				}
			},

			onLoadingLogsComplete: function() {
				console.log('Loading logs complete');
				// do something here
			},

#### Overriding format\_string\_recursive to inject HTML into log, including adding tooltips to log

**Ingredients:** ggg.js

I'm using cards as an example but this will work with any type of resource or game element. The first step is to override format\_string\_recursive. You can find info about this in this [excellent guide](https://bga-devs.github.io/blog/posts/translations-summary/). We will replace the return line from the guide with this:

return this.logInject(text);

The purpose of logInject() is to catch pre-coded text from your notifications, siphon out the meaningful info so that you can manipulate it on the front end, and then replace that pre-coded text in the log with whatever html you desire, as well as adding a tooltip to the element you're injecting. Here is a simplified version of logInject():

logInject: function (log\_entry) {
    const card\_regex = /\\\[\\w+-\*\\w\* \*\\w\*\\(\\d+\\)\\\]/g;    // this will catch a card name in the log formatted like so: \[card\_name(card\_type\_arg)\] -You may need to adjust the regex to catch your card names
    const cards\_to\_replace = log\_entry.matchAll(card\_regex);
    for (let card of cards\_to\_replace) {
        const match = card\[0\];
        const left\_parenthesis = match.indexOf('(');
        const card\_type\_arg = match.slice(left\_parenthesis+1, match.length-2);
        const card\_span = this.getHTMLForLog(card\_type\_arg, 'card');
        log\_entry = log\_entry.replace(match, card\_span);
    }
    return log\_entry;
}

getHTMLForLog() takes the card\_type\_arg and uses it to create the <span> to be injected into the log that you can then attach a tooltip to:

getHTMLForLog: function (item, type) {   // in this example, item refers to the card\_type\_arg
    switch(type) {
        case 'card':
            this.log\_span\_num++; // adds a unique num to the span id so that duplicate card names in the log have unique ids
            const card\_name = this.gamedatas\['cards'\]\[item\]\['description'\];  // or wherever you store your translated card name
            const item\_type = 'card\_tt';
            return \`<span id="${this.log\_span\_num}\_item\_${item}" class="${item\_type} item\_tooltip">${card\_name}</span>\`;
    }
}

If you only want to add some HTML to your log and don't care about the tooltips, you can remove the item\_tooltip class from the above and stop here. If you want tooltips, you'll need a function to add them:

addTooltipsToLog: function() {
    const item\_elements = dojo.query('.item\_tooltip:not(.tt\_processed)');
    Array.from(item\_elements).forEach(ele => {
        const ele\_id = ele.id;
        ele.classList.add('tt\_processed');  // prevents tooltips being re-added to previous log entries
        if (ele.classList.contains('card\_tt')) {
            const card\_type\_arg = ele\_id.slice(-3).replace(/^\\D+/g, '');  // extracts the card\_type\_arg from the span id
            this.cardTooltip(ele\_id, card\_type\_arg)
        }
    });
}

cardTooltip() is just however you want to create and add your tooltip. Mine is below:

cardTooltip: function (ele, card\_type\_arg) {
    const card = this.gamedatas.cards\[card\_type\_arg\];
    const bg\_pos = card\['x\_y'\];
    const skill = dojo.string.substitute("${skill}", { skill: card\['skill'\] });
    const description = dojo.string.substitute("${description}", { description: card\['description'\] });
    const html = \`<div style="margin-bottom: 5px; display: inline;"><strong>${description}</strong></div>
                  <span style="font-size: 10px; margin-left: 5px;">${skill}</span>
                  <div class="asset asset\_tt" style="background-position: -${bg\_pos\[0\]}% -${bg\_pos\[1\]}%; margin-bottom: 5px;"></div>\`;
    this.addTooltipHTML(ele, html, 1000);
}

And finally, you need to connect addTooltipsToLog. Using the new promise-based notifications you can supply the \`onEnd\` param like

this.bgaSetupPromiseNotifications({ onEnd: this.addTooltipsToLog.bind(this) });

Or, if you're not using them, you can attach to the notifqueue so it is called whenever the log is updated like

dojo.connect(this.notifqueue, 'addToLog', () => {
    this.addTooltipsToLog();
});

You can expand this to cover multiple types of tooltips. For example, I have it set up for cards: formatted in log as \[card\_name(card\_type\_arg)\], hexes: formatted as {pitch\_name(pitch\_type\_arg)}, objectives: formatted as ==objective\_name(objective\_type\_arg)==, etc.

### Player Panel

#### Inserting non-player panel

**This should be avoided. The new guideline is to avoid it in new games and remove it from old games.**

**Ingredients:** ggg.js, ggg\_ggg.tpl

If you want to insert non-player panel on the right side (for example to hold extra preferences, zooming controls, etc)

this can go pretty much anywhere in template it will be moved later

ggg\_ggg.tpl:

	<div class='player\_board\_config' id="player\_board\_config">
        <!-- here is whatever you want, buttons just example -->
		<button id="zoom-out" class=" fa fa-search-minus fa-2x config-control"></button>
		<button id="zoom-in" class=" fa fa-search-plus fa-2x config-control"></button>
		<button id="show-settings" class="fa fa-cog fa-2x config-control "></button>
        </div>

some hackery required in js

ggg.js:

/\* @Override \*/
	updatePlayerOrdering() {
		this.inherited(arguments);
		dojo.place('player\_board\_config', 'player\_boards', 'first');
	},

### Images and Icons

#### Accessing images from js

**Ingredients:** ggg.js

  

 
     // your game resources
     
     var my\_img = '<img src="'+g\_gamethemeurl+'img/cards.jpg"/>';
     
     // shared resources
     var my\_help\_img = "<img class='imgtext' src='" + g\_themeurl + "img/layout/help\_click.png' alt='action' /> <span class='tooltiptext'>" +
                    text + "</span>";

#### High-Definition Graphics

Some users will have screens which can display text and images at a greater resolution than the usual 72 dpi, e.g. the "Retina" screens on the 5k iMac, all iPads, and high-DPI screens on laptops from many manufacturers. If you can get art assets at this size, they will make your game look extra beautiful. You _could_ just use large graphics and scale them down, but that would increase the download time and bandwidth for users who can't display them. Instead, a good way is to prepare a separate graphics file at exactly twice the size you would use otherwise, and add "@2x" at the end of the filename, e.g. if pieces.png is 240x320, then pieces@2x.png is 480x640.

There are two changes required in order to use the separate graphics files. First in your css, where you use a file, add a media query which overrides the original definition and uses the bigger version on devices which can display them. Ensuring that the "background-size" attribute is set means that the size of the displayed object doesn't change, but only is drawn at the improved dot pitch.

.piece {
    position: absolute;
    background-image: url('img/pieces.png');
    background-size:240px 320px;
    z-index: 10;
}
@media (-webkit-min-device-pixel-ratio: 2), (min-device-pixel-ratio: 2), (min-resolution: 192dpi)
{
    .piece {
        background-image: url('img/pieces@2x.png');
    }
}

Secondly, in your setup function in javascript, you must ensure than only the appropriate one version of the file gets pre-loaded (otherwise you more than waste the bandwidth saved by maintaining the standard-resolution file). Note that the media query is the same in both cases:

            var isRetina = "(-webkit-min-device-pixel-ratio: 2), (min-device-pixel-ratio: 2), (min-resolution: 192dpi)";
            if (window.matchMedia(isRetina).matches)
            {
                this.dontPreloadImage( 'pieces.png' );
                this.dontPreloadImage( 'board.jpg' );
            }
            else
            {
                this.dontPreloadImage( 'pieces@2x.png' );
                this.dontPreloadImage( 'board@2x.jpg' );
            }

#### Using CSS to create different colors of game pieces if you have only white piece

background-color: #${color}; 
background-blend-mode: multiply;
background-image: url( 'img/mypiece.png');
mask: url('img/mypiece.png');
-webkit-mask: url('img/mypiece.png');

where ${color} - is color you want

Note: piece has to be white (shades of gray). Sprite can be used too, just add add background-position as usual.

#### Accessing player avatar URLs

      getPlayerAvatar(playerId) {
         let avatarURL = '';

         if (null != $('avatar\_' + playerId)) {
            let smallAvatarURL = dojo.attr('avatar\_' + playerId, 'src');
            avatarURL = smallAvatarURL.replace('\_32.', '\_184.');
         }
         else {
            avatarURL = 'https://x.boardgamearena.net/data/data/avatar/default\_184.jpg';
         }

         return avatarURL;
      },

Note: This gets avatar URLs at 184x184 resolution. You can also use 92, 50, and 32 depending on which resolution you want.

#### Adding Image buttons

Its pretty trivial but just in case you need a working function:

ggg.js:

                addImageActionButton: function (id, div\_html, handler) { // div\_html is string not node
                    this.addActionButton(id, div\_html, handler, '', false, 'gray'); 
                    dojo.style(id, "border", "none"); // remove ugly border
                    dojo.addClass(id, "bgaimagebutton"); // add css class to do more styling
                    return $(id); // return node for chaining
                },

Example of usage:

    this.addImageActionButton('button\_coin',"<div class='coin'></div>", ()=>{ alert('Ha!'); });

### Other Fluff

#### Use thematic fonts

**Ingredients:** ggg.css

Sometime game elements use specific fonts of text, if you want to match it up you can load some specific font (IMPORTANT: from some **free font** source. See notes below).

[![Dragonline font.png](/images/0/00/Dragonline_font.png)](/File:Dragonline_font.png)

.css

/\* latin-ext \*/
@font-face {
  font-family: 'Qwigley';
  font-style: normal;
  font-weight: 400;
  src: local('Qwigley'), local('Qwigley-Regular'), url(https://fonts.gstatic.com/s/qwigley/v6/2Dy1Unur1HJoklbsg4iPJ\_Y6323mHUZFJMgTvxaG2iE.woff2) format('woff2');
  unicode-range: U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF;
}
/\* latin \*/
@font-face {
  font-family: 'Qwigley';
  font-style: normal;
  font-weight: normal;
  src: local('Qwigley'), local('Qwigley-Regular'), url(https://fonts.gstatic.com/s/qwigley/v6/gThgNuQB0o5ITpgpLi4Zpw.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
}
@font-face {
  font-family: 'Qwigley';
  font-style: normal;
  font-weight: normal;
  src: local('Qwigley'), local('Qwigley-Regular'), url(http://ff.static.1001fonts.net/q/w/qwigley.regular.ttf) format('ttf');
}

.zone\_title {
	display: inline-block;
	position: absolute;
	font: italic 32px/32px "Qwigley", cursive;	   
	height: 32px;
	width: auto;
}

**NB:** if you need to include a font that's not available online, an extra action will be needed from an admin. Please include the font file(s) in your img directory, and mention it to admins when requesting your game to be moved to alpha. **Please remember that the font has to be free, and include a .txt with all appropriate license information about the font.** You can look for free fonts (for example) on [https://fonts.google.com](https://fonts.google.com) or [https://www.fontsquirrel.com/](https://www.fontsquirrel.com/))

**Content Security Policy**

BGA runs a Content Security Policy which will limit the origins from which you can load external fonts, in order to prevent license abuse.

The CSP is a whitelist of allowed origins. To see the list, view the response headers of any page on Studio, and look for the "Content-Security-Policy" header.

You will specifically want to check for the font-src token within these headers, and limit any external fonts to these sources.

**This list is subject to change** but as of the time of writing, the only acceptabled external sites are use.typekit.net and fonts.gstatic.com.

  

### Scale to fit for big boards

**Ingredients:** ggg\_ggg.tpl, ggg.js

  

Lets say you have huge game board, and lets say you want it to be 1400px wide. Besides the board there will be side bar which is 240 and trim. My display is 1920 wide so it fits, but there is big chance other people won't have that width. What do you do?

You have to decide:

-   If board does not fit you want scale whole thing down, the best way is probably use viewport (see [https://en.doc.boardgamearena.com/Your\_game\_mobile\_version](https://en.doc.boardgamearena.com/Your_game_mobile_version))
-   You can leave the board as is and make sure it is scrollable horizonatally
-   You add custom scale just for the board (can add user controls - and hook to transform: scale())

I tried to auto-scale but this just does work, too many variables - browser zoom, 3d mode, viewport, custom bga scaling, devicePixelRatio - all create some impossible coctail of zooming... Here is scaling functing for custom user scaling

ggg\_ggg.tpl:

   <div id="thething" class="thething">
            ... everything else you declare ...
   </div>

ggg.js:

    onZoomPlus: function() {
       this.setZoom(this.zoom + 0.1);
    },
    onZoomMinus: function() {
       this.setZoom(this.zoom - 0.1);
    },

    setZoom: function (zoom) {
      zoom = parseInt(zoom) || 0;
      if (zoom === 0 || zoom < 0.1 || zoom > 10) {
        zoom = 1;
      }
      this.zoom = zoom;
      var inner = document.getElementById("thething");

      if (zoom == 1) {
        inner.style.removeProperty("transform");
        inner.style.removeProperty("width");
      } else {
        inner.style.transform = "scale(" + zoom + ")";
        inner.style.transformOrigin = "0 0";
        inner.style.width = 100 / zoom + "%";
      }
      localStorage.setItem(\`${this.game\_name}\_zoom\`, "" + this.zoom);
      this.onScreenWidthChange();
    },

### Dynamic tooltips

If you really need a dynamic tooltip you can use this technique. (Only use it if the static tooltips provided by the BGA framework are not sufficient.)

           new dijit.Tooltip({
               connectId: \["divItemId"\],
               getContent: function(matchedNode){
                   return "... calculated ..."; 
               }
           });

  
This is an out-of-the-box djit.Tooltip. It has a _getContent_ method which is called dynamically.

The string returned by getContent() becomes the innerHTML of the tooltip, so it can be anything. In this example matchedNode is a dojo node representing dom object with id of "divItemId" but there are more parameters which I am not posting here which allows more sophisticated subnode queries (i.e. you can attach tooltip to all nodes with class or whatever).

[dijit.Tooltip](https://dojotoolkit.org/reference-guide/1.10/dijit/Tooltip.html)

It's not part of the BGA API so use at your own risk.

### Rendering text with players color and proper background

**Ingredients:** ggg.js

  

        /\* Implementation of proper colored You with background in case of white or light colors  \*/
 
        divYou: function() {
            var color = this.gamedatas.players\[this.player\_id\].color;
            var color\_bg = "";
            if (this.gamedatas.players\[this.player\_id\] && this.gamedatas.players\[this.player\_id\].color\_back) {
                color\_bg = "background-color:#" + this.gamedatas.players\[this.player\_id\].color\_back + ";";
            }
            var you = "<span style=\\"font-weight:bold;color:#" + color + ";" + color\_bg + "\\">" + \_\_("lang\_mainsite", "You") + "</span>";
            return you;
        },

        /\* Implementation of proper colored player name with background in case of white or light colors  \*/

        divColoredPlayer: function(player\_id) {
            var color = this.gamedatas.players\[player\_id\].color;
            var color\_bg = "";
            if (this.gamedatas.players\[player\_id\] && this.gamedatas.players\[player\_id\].color\_back) {
                color\_bg = "background-color:#" + this.gamedatas.players\[player\_id\].color\_back + ";";
            }
            var div = "<span style=\\"color:#" + color + ";" + color\_bg + "\\">" + this.gamedatas.players\[player\_id\].name + "</span>";
            return div;
        },

### Cool realistic shadow effect with CSS

#### Rectangles and circles

It is often nice to have a drop shadow around tiles and tokens, to separate them from the table visually. It is very easy to add a shadow to rectangular elements, just add this to your css:

.xxx-tile {
    box-shadow: 3px 3px 3px #000000a0;
}

box-shadow obeys **border-radius** of the element, so it will look good for rounded rectangles, and hence also circles (if border-radius is set appropriately).

box-shadow also supports various other parameters and can be used to achieve effects such as glowing, borders, inner shadows etc. If you need to animate a box-shadow, you may be able to get better performance (avoiding redraws) if you attach the shadow to another element (possibly an ::after pseudo-element) and change only the **opacity** of that element.

#### Irregular Shapes

If you wish to make a shadow effect for game pieces that are not a rectangle, but your game pieces are drawn from rectangles in a PNG image, you can apply the shadow to the piece using any art package and save it inside the image. This usually will yield the best performance. Remember to account for the size of the shadow when you lay out images in the sprite sheet.

However that sometimes will not be an option, for example if the image needs to be rotated while the shadow remains offset in the same direction. In this case, one option is to not use box-shadow but use filter, which is supported by recent major browsers. This way, you can use the alpha channel of your element to drop a shadow. This even work for transparent backgrounds, so that if you are using the "CSS-sprite" method, it will work!

For instance:

.xxx-token {
    filter: drop-shadow(0px 0px 1px #000000);
}

Beware that some browsers still do not always draw drop-shadow correctly. In particular, Safari frequently leaves bits of shadow behind when objects move around the screen. In Chrome, shadows sometimes flicker badly if another element is animating close by. Some of these correctness issues can be solved by adding **isolation: isolate; will-change: filter;** to affected elements, but this significantly affects redraw performance.

Beware of performance issues - particularly on Safari (MacOS, iPhone and iPad). Keep in mind that drop-shadow are very GPU intensive. This becomes noticeable once you have about 40 components with drop-shadow filter. If that is your case, you can quite easily implement a user preference to disable shadows for users on slower machines:

gameoptions.inc.php

100 => array(
			'name' => totranslate('Shadows'),
			'needReload' => true, // after user changes this preference game interface would auto-reload
			'values' => array(
					1 => array( 'name' => totranslate( 'Enabled' ), 'cssPref' => '' ),
					2 => array( 'name' => totranslate( 'Disabled' ), 'cssPref' => 'no-shadow' )
			)
	),

\[game\].css

.no-shadow \* {
	filter: none !important; 
} 

For Safari, it is usually better to simply disable drop-shadow completely: [Game interface stylesheet: yourgamename.css#Warning: using drop-shadow](/Game_interface_stylesheet:_yourgamename.css#Warning:_using_drop-shadow "Game interface stylesheet: yourgamename.css").

#### Shadows with clip-path

For some reason, a shadow will not work together with clip-path on one element. To use both clip-path (when for example using .svg to cut out cardboard components from your .jpg spritesheet) and drop-shadow, you need to wrap the element into another one, and apply drop-shadow to the outer one, and clip-path to the inner one.

<div class='my-token-wrap'>
  <div class='my-token'>
  </div>
</div>

.my-token-wrap {
    filter: drop-shadow(0px 0px 1px #000000);
}
.my-token-wrap .my-token {
    clip-path: url(#my-token-path);
}

  

### Using the CSS classes from the state machine

If you need to hide or show stuff depending on the state of your game, you can of course use javascript, but CSS is hand enough for that. The #overall-content element does change class depending on the game state. For instance, if you are in state _playerTurn_, it will have the class _gamestate\_playerTurn_.

So now, if you want to show the discard pile only during player turns, you may use:

#discard\_pile { display: none }
.gamestate\_playerTurn #discard\_pile { display: block }

This can be used if you want to change sizing of elements, position, layout or visual appearance.

## Game Model and Database design

### Database for The euro game

Lets say we have a game with workers, dice, tokens, board, resources, money and vp. Workers and dice can be placed in various zones on the board, and you can get resources, money, tokens and vp in your home zone. Also tokens can be flipped or not flipped.

[![Madeira board.png](/images/c/c4/Madeira_board.png)](/File:Madeira_board.png)

  
Now lets try to map it, we have

-   (meeple,zone)
-   (die, zone, sideup)
-   (resource cube/money token/vp token,player home zone)
-   (token, player home zone, flip state)

We can notice that resource and money are uncountable, and don't need to be track individually so we can replace our mapping to

-   (resource type/money,player home zone, count)

And vp stored already for us in player table, so we can remove it from that list.

Now when we get to encode it we can see that everything can be encoded as (object,zone,state) form, where object and zone is string and state is integer. The resource mapping is slightly different semantically so you can go with two table, or counting using same table with state been used as count for resources.

So the piece mapping for non-grid based games can be in most case represented by (string: token\_key, string: token\_location, int: token\_state), example of such database schema can be found here: [dbmodel.sql](https://github.com/elaskavaia/bga-sharedcode/blob/master/dbmodel.sql) and class implementing access to it here [table.game.php](https://github.com/elaskavaia/bga-sharedcode/blob/master/modules/tokens.php).

Variant 1: Minimalistic

CREATE TABLE IF NOT EXISTS \`token\` (
 \`token\_key\` varchar(32) NOT NULL,
 \`token\_location\` varchar(32) NOT NULL,
 \`token\_state\` int(10),
 PRIMARY KEY (\`token\_key\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  

token

token\_key

token\_location

token\_state

meeple\_red\_1

home\_red

0

dice\_black\_2

board\_guard

1

dice\_green\_1

board\_action\_mayor

3

bread

home\_red

5

Now how we represent resource counters such as bread? Using same table from we simply add special counter token for bread and use state to indicate the count. Note to keep first column unique we have to add player identification for that counter, i.e. ff0000 is red player.

token

token\_key

token\_location

token\_state

bread\_ff0000

tableau\_ff0000

5

  
See php module for this table here [https://github.com/elaskavaia/bga-sharedcode/blob/master/modules/tokens.php](https://github.com/elaskavaia/bga-sharedcode/blob/master/modules/tokens.php)

Variant 2: Additional resource table, resource count for each player id

CREATE TABLE IF NOT EXISTS \`resource\` (
 \`player\_id\` int(10) unsigned NOT NULL,
 \`resource\_key\` varchar(32) NOT NULL,
 \`resource\_count\` int(10) signed NOT NULL,
 PRIMARY KEY (\`player\_id\`,\`resource\_key\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE resource ADD CONSTRAINT fk\_player\_id FOREIGN KEY (player\_id) REFERENCES player(player\_id);

resource

player\_id

resource\_key

resource\_count

123456

bread

5

  

Variant 3: More normalised

This version is similar to "card" table from hearts tutorial, you can also use exact cards database schema and Deck implementation for most purposes (even you not dealing with cards).

CREATE TABLE IF NOT EXISTS \`token\` (
 \`token\_id\` int(10) unsigned NOT NULL AUTO\_INCREMENT,
 \`token\_type\` varchar(16) NOT NULL,
 \`token\_arg\` int(11) NOT NULL,
 \`token\_location\` varchar(32) NOT NULL,
 \`token\_state\` int(10),
 PRIMARY KEY (\`token\_id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

token

token\_id

token\_type

token\_arg

token\_location

token\_state

22

meeple

123456

home\_123456

0

23

dice

2

board\_guard

1

26

dice

1

board\_action\_mayor

3

49

bread

0

home\_123456

5

Advantages of this would be is a bit more straightforward to do some queries in db, disadvantage its hard to read (as you can compare with previous example, you cannot just look at say, ah I know what it means). Another questionable advantage is it allows you to do id randomisation, so it hard to do crafted queries to cheat, the down side of that you cannot understand it either, and handcraft db states for debugging or testing.

### Database for The card game

Lets say you have a standard card game, player have hidden cards in hand, you can draw card from draw deck, play card on tableau and discard to discard pile. We have to design database for such game.

In real word to "save" the game we take a picture a play area, save cards from it, then put away draw deck, discard and hand of each player separately and mark it, also we will record current scoring (if any) and who's turn was it.

-   Framework handles state machine transition, so you don't have to worry about database design for that (i.e. who's turn it is, what phase of the game we are at, you still have to design it but part of state machine step)
-   Also framework supports basic player information, color, order around the table, basic scoring, etc, so you don't have to worry about it either
-   The only thing you need in our database is state of the "board", which is "where each pieces is, and in what state", or (position,rotation) pair.

Lets see what we have for that:

-   The card state is very simple, its usually "face up/face down", "tapped/untapped", "right side up/up side down"
-   As position go we never need real coordinates x,y,z. We need to know what "zone" card was, and depending on the zone it may sometimes need an extra "z" or "x" as card order. The zone position usually static or irrelevant.
-   So our model is: we have cards, which have some attributes, at any given point in time they belong to a "zone", and can also have order and state
-   Now for mapping we should consider what information changes and what information is static, later is always candidate for material file
-   For dynamic information we should try to reduce amount of fields we need
    -   we need at least a field for card, so its one
    -   we need to know what zone cards belong to, its 2
    -   and we have possibly few other fields, if you look closely at you game you may find out that most of the zone only need one attribute at a time, i.e. draw pile always have cards face down, hand always face up, also for hand and discard order does not matter at all (but for draw it does matter). So in majority of cases we can get away with one single extra integer field representing state or order
-   In real database both card and zone will be integers as primary keys referring to additional tables, but in our case its total overkill, so they can be strings as easily

Variant 1: Minimalistic

CREATE TABLE IF NOT EXISTS \`card\` (
  \`card\_key\` varchar(32) unsigned NOT NULL,
  \`card\_location\` varchar(32) NOT NULL,
  \`card\_state\` int(11) NOT NULL,
  PRIMARY KEY (\`card\_id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO\_INCREMENT=1 ;

  
Variant 2: More normalised

This version supported by Deck php class, so unless you want to rewrite db access layer go with this one

CREATE TABLE IF NOT EXISTS \`card\` (
  \`card\_id\` int(10) unsigned NOT NULL AUTO\_INCREMENT,
  \`card\_type\` varchar(16) NOT NULL,
  \`card\_type\_arg\` int(11) NOT NULL,
  \`card\_location\` varchar(16) NOT NULL,
  \`card\_location\_arg\` int(11) NOT NULL,
  PRIMARY KEY (\`card\_id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO\_INCREMENT=1 ;

Note: if you using this schema, some zones/locations have special semantic. The 'hand' location is actually multiple locations - one per player, but player id is encoded as card\_location\_arg. If 'hand' in your game is ordered, visible or can have some other card states, you cannot use hand location (replacement is hand\_<player\_id> or hand\_<color\_id>)

## Game Elements

### Resource

A game resource, such as "wood," is usually infinite and represented by a count allocated to a specific player or supply.

If a resource can be placed on location, use the "Meeple" model described below.

For a working example, you can check out: [CodePen Example](https://codepen.io/VictoriaLa/pen/emYgLzR) (ui only).

#### Representation in Database

In a minimalistic "tokens" database, it would look like this (e.g., the red player has 3 wood):

token

token\_key

token\_location

token\_state

wood\_ff0000

3

wood\_supply

40

  
The second row you need to store the resource in the supply (if it's counted).

If you don't like this solution can use bga\_globals table for this or create your own resource table.

Its not recommened to extend player table to store this information.

  

#### Representation in Material File (material.inc.php)

In the material file, you can define some information about resources. For instance, you can specify that it's of type "resource" and call it "Wood" in English, along with a tooltip:

$this->token\_types = \[
  ...
  'wood' => \[
     'name' => clienttranslate('Wood'),
     'tooltip' => clienttranslate('Game resource used for building houses'),
     'type' => 'resource',
     'max' => 40
  \]
\];

#### HTML Representation

In HTML, this would look something like this within the player panel. Using the data attribute instead of a CDATA value is much more flexible:

<div id="wood\_ff0000" class="resource wood" data-value="3"></div>

#### JavaScript Handling

When you get the object from the server, one of the tokens will be sent in the array like this:

token = {
  key: 'wood\_ff0000',
  location: '-',
  state: '3'
}

You can create a corresponding \`

\` in the \`setup()\` method of \`game.js\` as follows:

const playerColor = token.key.split('\_')\[1\];
const resType = token.key.split('\_')\[0\];
const tokenInfo = this.gamedatas.token\_types\[resType\]; // token\_types is the structure from the material file sent to the client
const div = \`<div id="${token.key}" class="${resType} ${tokenInfo.type} ${token.key}" data-value="${token.state}"></div>\`;

if (playerColor != 'supply') {
    document.querySelector(\`#player\_board\_${this.getPlayerIdByColor(playerColor)} > .player-board-game-specific-content\`).insertAdjacentHTML('beforeend',div);
    this.addTooltip(token.key, \_(tokenInfo.name) + " " + \_(tokenInfo.tooltip), "");
}

  
When you receive an update notification (assuming you get the same "token" object), you can simply update the \`data-value\`:

document.querySelector(\`#${token.key}\`).dataset.value = token.state;

To display the resource in the game log, you can format it like this:

Player gains <div class="resource wood" data-value="1"></div>

For more on injecting icon images in the log, see: [BGA\_Studio\_Cookbook#Inject\_icon\_images\_in\_the\_log](/BGA_Studio_Cookbook#Inject_icon_images_in_the_log "BGA Studio Cookbook")

#### Graphic Representation (.css)

To properly display the resource image, it is preferable to use an image sprite that includes all resources, usually it will be .png as these objects have shape. This how will .css look like with horizontal sprite image:

.resource {
  background-image: url(https://en.doc.boardgamearena.com/images/d/d3/Cubes.png);
  background-repeat: no-repeat;
  background-size: cover; /\* auto-scale \*/
  aspect-ratio: 1/1; /\* that will keep heigh in sync \*/
  width: 32px; /\* default size, specific location should override \*/
}

.wood {
  /\* Since the sprite is a horizontal row of 11 cubes and 9 is the brown cube position \*/
  background-position: calc(100% / (11 - 1) \* 9) 0%;
}

  
To show a text overlay with the resource value, you can use the following CSS:

.resource\[data-value\]:after {
  content: attr(data-value);
  width: 100%;
  height: 100%;
  position: absolute;
  font-size: xx-large;
  text-align: center;
  text-shadow: 2px 0 2px #fff, 0 -2px 2px #fff, 0 2px 2px #fff, -2px 0 2px #fff;
}

  
This solution is fully scalable — you only need to specify the size where you want it displayed. For example:

.player\_board\_content > .resource {
  width: 40px;
  position: relative;
}

Note: if you have that board that tracks the resource on resource tracker - you can show this IN ADDITION of showing resource on player panel.

#### Selection and Actions

It is best to put buttons with resource images on the status bar, rather than having the player click on the player panel.

For animation:

-   Can use move animation to animate resourced gained or played from the board location to the player panel
-   Can use "vapor" animation to show resource gained from the board

  

### Meeple

A meeple is a game piece, typically representing a "worker," depicted as a human-shaped figure in a specific color assigned to a player.

The key distinction between meeples and traditional resources is that meeples are placed on locations and can exist in at least two states — standing or lying down.

This concept also applies to similar pieces like "houses," "animeeples," "ships," and others.

  

#### Representation in Database

In a simplified "tokens" database, it might be represented like this (e.g., the red player's first meeple is on action spot 1, while the white meeple remains in supply):

token

token\_key

token\_location

token\_state

meeple\_ff0000\_1

actionspot\_1

1

meeple\_ffffff\_1

supply

0

  

#### Representation in Material File (material.inc.php)

Here we define some properties, for example name can be used in notification and as tooltip, type can be used to create the div by javascript, 'create' - can be used by server to create 8 meeples of this type in database and set location to 'supply\_ff0000'

$this->token\_types = \[
  ...
  'meeple\_ff0000' => \[
     'name' => clienttranslate('Red Worker'),
     'type' => 'meeple meeple\_ff0000',
     'create' => 8,
     'location' => 'supply\_ff0000'
  \]
\];

#### HTML Representation

In HTML, this would look something like this.

<div id="meeple\_ff0000\_1" class="meeple meeple\_ff0000" data-state="1"></div>

#### JavaScript Handling

When you get the object from the server, each meeple object will be similar to this:

meeple = {
  key: 'meeple\_ff0000\_1',
  location: 'actionslot\_1',
  state: '1'
}

You can create a corresponding \`

\` in the \`setup()\` method of \`game.js\` as follows:

const playerColor = token.key.split('\_')\[1\];
const resType = token.key.split('\_')\[0\];
const tokenInfo = this.gamedatas.token\_types\[resType\]; // token\_types is the structure from the material file sent to the client
const div = \`<div id="${token.key}" class="${tokenInfo.type} ${token.key}" data-state="${token.state}"></div>\`;

$(token.location).insertAdjacentHTML('beforeend',div);
this.addTooltip(token.key, \_(tokenInfo.name) + " " + \_(tokenInfo.tooltip), "");
$(token.key).addEventListener('onclick',(ev)=>this.onMeepleClick(ev));

  
When you receive an update notification (assuming you get the same "token" object), you either create it if not exists or animate:

if ($(token.key)) {
  // exists
  $(token.key).dataset.state = token.state; // update state
  this.moveToken(token.key, token.location); // animate to new location (custom functon), see \[\[BGA\_Studio\_Cookbook#Animation\]\]
  } else {
  // crate meeple using code in previous section
}

#### Graphic Representation (.css)

Use same sprite technique from Resource section above.

When placed on the board it will look good with shadow, but its not recommended on mobile

 filter: drop-shadow(black 5px 5px 5px);

  
To represent "laying down" meeple, you have to use a different sprite image, which you will apply based on data attribute

.meeple\[data-state="1"\] {
   background-image: url(...);
}

You can also rotate you div, but it will look lame. Other options include changing its shading, adding overlay (i.e. sad face) and so on.

  

#### Selection and Actions

To show user that meeple is active it best to use drop-shadow as image as non-square, however this may be very slow. For simplier case use box shadow.

.active\_slot {
  filter: drop-shadow(0px 0px 10px blue);
  cursor: pointer;
}

/\* draw a circle around game element for selection \*/
.active\_slot\_simple:after {
  content: " ";
  width: 110%;
  top: -5%;
  left: -5%;
  aspect-ratio: 1/1;
  position: absolute;
  border-radius: 50%;
  box-shadow: 0px 0px 4px 3px #64b4ff;
}

See code example at [https://codepen.io/VictoriaLa/pen/emYgLzR](https://codepen.io/VictoriaLa/pen/emYgLzR)

When a meeple is gained from the supply, you can display a meeple icon on the status bar button instead of showing the supply on the board.

### Dice

The 2D dice can use similar handing as meeple but it has 6 states instead of 2.

IMPORTANT: Never roll dice using javascript. All dice rolling must be done using bga\_rand() function in php.

#### Representation in Database

In a simplified "tokens" database, it might be represented like this

token

token\_key

token\_location

token\_state

die\_black

actionspot\_1

1

die\_red

supply

6

  

#### Representation in Material File (material.inc.php)

Similar to resource and meeple above

#### HTML Representation

<div id="die\_black" class="die" data-state="1"></div>

  

#### JavaScript Handling (.js)

See meeple section

#### Graphic Representation (.css)

For dice we would usually use N x 6 sprite, and since the sides are square - the .jpg format is better (it is smaller then png)

.die {  
  background-image: url(https://en.doc.boardgamearena.com/images/c/c5/64\_64\_dice.jpg);
  background-size: 600%;
  background-repeat: no-repeat;
  aspect-ratio: 1/1;
  width: 64px;
  border-radius: 5%; /\* looks better with rounded corders \*/
}
.die\[data-state="3"\] {
  background-position: calc(100% / 5 \* 2) 0%;
}

The 3D dice a bit tricker to create but its feasible, see [https://codepen.io/VictoriaLa/pen/QWBBbwz](https://codepen.io/VictoriaLa/pen/QWBBbwz) for an example.

Also multiple examples on N-sided dice can be found on [BGA\_Code\_Sharing](/BGA_Code_Sharing "BGA Code Sharing")

#### Selection and Actions

Since it is a square its a lot easier to make a square selection highlight

.die.active\_slot  {
  box-shadow: 0px 0px 4px 4px blue;
}

### Card

Cards are the most complex game resource, they can be located in various zones, stacked, tapped, put face down and can have arbitrary complete abilities. If you have square tiles - it can be treated the same as cards.

#### Representation in Database

In a simplified "tokens" database, it might be represented like this

token

token\_key

token\_location

token\_state

card\_project\_123

tableau\_ff0000

1

card\_corp\_p1

deck\_corp

6

Means project card is player red tableau and state 1 means it has been used for example, and second card of corproration file in in deck at position 6 from the top.

When duplcates are in play you need to add extra unique disambigator in the key.

Usually cards will have numeric id associated with type, but it this number is per expansition, so leave the space for expansion identifier in there also.

Another option use [Deck](/Deck "Deck") component.

  

#### Representation in Material

All the card properties which do not change during the game can be put in material file (or its alternative)

This is example from terraforming mars

 'card\_main\_81' => \[  //
  'location' => 'deck\_main',
  'create' => 'single',
  'num' => 81,
  'name' => clienttranslate('Ganymede Colony'),
  't' => 1,
  'r' => "city('Ganymede Colony')",
  'cost' => 20,
  'tags' => 'Space City Jovian',
  'vp' => 'tagJovian',
  'deck' => 'Basic',
  'text' => clienttranslate('Place a city tile ON THE RESERVED AREA \[for Ganymede Colony\].'),
  'text\_vp' => clienttranslate('1 VP per Jovian tag you have.'),
\],

#### HTML Representation

There is 2 main options:

-   Use exact cards images in english
-   Use cards images WITHOUT text

The 3d option is completely redo the graphic layout that game designed did already, but I won't go there

The first option is very simple and similar to other resources and meeple

<div id="card\_project\_123" class="card card\_project card\_project\_123" data-state="1"></div>

The second option means you can add translated text instead

<div id="card\_project\_123" class="card card\_project card\_project\_123" data-state="1" data-cost="22">
   <div class="card\_name">Ganymede Colony</div>
   <div class="card\_text">Place a city tile ON THE RESERVED AREA \[for Ganymede Colony\].</div>
   <div class="card\_cost"></div>
</div>

In the example above card\_cost is also rendered as this can change during the game (even printed value is the same), it would be rendered using data-cost attribute.

Instead of using class you can also use state even if it is static

Note: to make flip animation you effectively need to make a "3d" card and provide both faces with separate graphics, which makes more complex div. See examples at [BgaCards](/BgaCards "BgaCards").

Note: you can also use [Stock](/Stock "Stock") component that handles html, js, css, layout and selection at the same time. If do that skip sections below.

#### JavaScript Handling (.js)

You will get the server data which looks like this

card = {
  key: 'card\_project\_123',
  location: 'tableau\_ff0000',
  state: '1'
}

  

  createCard(token: Token) {
    // token\_types is the structure from the material file sent to the client
    const tokenInfo = this.gamedatas.token\_types\[token.key\]; 
    const div = \`
    <div id="${token.key}" class="${tokenInfo.type} ${token.key}" data-state="${token.state}">
       <div class="card\_name">${\_(tokenInfo.name)}</div>
       <div class="card\_text">${\_(tokenInfo.text)}</div>
    </div>\`;

    $(token.location).insertAdjacentHTML('beforeend',div);
    this.addTooltipHtml(token.key, this.getTooptipHtmlForToken(token));
    $(token.key).addEventListener("onclick", (ev) => this.onCardClick(ev));
  }

When you receive an update notification (assuming you get the same "token" object), you either create it if not exists or animate:

if ($(token.key)) {
  // exists
  $(token.key).dataset.state = token.state; // update state
  $(token.key).dataset.cost = token.cost; // update cost (discounted cost)
  this.moveCard(token.key, token.location); // animate to new location (custom functon), see \[\[BGA\_Studio\_Cookbook#Animation\]\]
} else {
  this.createCard(token);
}

  

#### Graphic Representation (.css)

For this html

<div id="game" class="classic\_deck">
  <div id="hand" class="hand">
    <div id="card\_H\_10" class="card" data-suit='H' data-rank="10"> </div>
    <div id="card\_C\_K" class="card" data-suit='C' data-rank="K"> </div>
  </div>
</div>

This is example css using sprite image of playing cards

.card {
   background-image: url('https://x.boardgamearena.net/data/others/cards/FULLREZ\_CARDS\_ORIGINAL\_NORMAL.jpg');   /\* don't do full url in your game, copy this file inside img folder \*/
  
   background-size: 1500% auto;  /\* this mean size of background is 15 times bigger than size of card, because its sprite \*/
   border-radius: 5%;
   width: 10em;
   height: 13.5em;
   box-shadow: 0.1em 0.1em 0.2em 0.1em #555;
}


.card\[data-rank="10"\] { /\* 10 is column number 10 - 2 because we start from 0 and first card is sprite is 2. The multiplier is (15 - 1) is because we have 15 columns. -1 is because % in CSS is weird like that. \*/
   background-position-x: calc(100% / (15 - 1) \* (10 - 2));
}
.card\[data-rank="K"\] { /\* King will be number 13 in rank \*/
   background-position-x: calc(100% / (15 - 1) \* (13 - 2));
}
.card\[data-suit="H"\] { /\* Hears row position is 1 (because we count from 0). Multiplier (4 - 1) is because we have 4 rows and -1 is because % in CSS is weird like that. \*/
   background-position-y: calc(100% / (4 - 1) \* (1));
}
.card\[data-suit="C"\] { /\* Clubs row position is 2 \*/
   background-position-y: calc(100% / (4 - 1) \* (2));
}

See code at [https://codepen.io/VictoriaLa/pen/mdMzRxa](https://codepen.io/VictoriaLa/pen/mdMzRxa)

#### Selection and Actions

Since it is a square its a lot easier to make a square selection highlight

.card.active\_slot  {
  box-shadow: 0px 0px 4px 4px blue;
}

#### Card Layouts

There are two options - you use [Stock](/Stock "Stock") component or don't, see [Anti-Stock](/Anti-Stock "Anti-Stock") for details on how to do your own layouts.

This is comprehensive example of various card layouts and animations [https://thoun.github.io/bga-cards/demo/index.html](https://thoun.github.io/bga-cards/demo/index.html)

### Hex Tiles

From data perspective hex tiles exactly the same as cards. From visualization there is a small trick.

CSS:

.hex {
  width: var(--hex-width);
  aspect-ratio: 1 / 1.1193;
  border-radius: 30%;
  background-color: yellow; /\* this is just for demo, use proper .png file for this \*/
  clip-path: polygon(50% 0, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
}

.hex.active\_slot\_simple {
  background-color: rgba(86, 207, 110, 0.4);
}
.hex.active\_slot {
  filter: drop-shadow(0px 0px 10px blue);
}

### Tetris Tiles

You can use clip-path for actual shape and svg path for outline See example at [https://codepen.io/VictoriaLa/pen/OJmoZGw](https://codepen.io/VictoriaLa/pen/OJmoZGw)

### Track

Tracker can be represented as "resource" in database - in this case its just number, or similar to "meeple" in which case the location will have the "number" associated with position on track

In a simplified "tokens" database, it might be represented like this

token

token\_key

token\_location

token\_state

tracker\_o

scale\_o\_1

0

tracker\_t

scale\_t\_10

0

  
The tracker location in this case may have some properties in material file, for example to trigger game effect when we land on this spot

  'scale\_t' => \[
    'index\_start' => 0,
    'max' => 20,
    'value\_start' => -30,
    'value\_step' => 2,
    'slot\_type' => 'slot slot\_t'
  \],
  'scale\_t\_10' => \[
    'r' => 'ocean',
    'param' => 't',
    'tooltp' => clienttranslate('Place an ocean'),
    'value' => 0
  \],

The track in this case can be generated as series of slots (create in js)

    for(let i=trackInfo.index\_start,value = trackInfo.value\_start;i<trackInfo.index\_start+trackInfo.max;i++,value+=trackInfo.value\_step) {
      $(trackInfo.key).insertAdjacentHTML('beforeend',\`<div id="${trackInfo.key}\_${i}" class="${trackInfo.slot\_type} data-value=${value}"></div>\`);
      this.addTooltip(\`${trackInfo.key}\_${i}\`, \_(tokenInfo.tooltip), "");
    }

Then use regular move animation to move tracker into position on track.

## Code Organization

### Including your own JavaScript module

**Ingredients:** ggg.js, modules/ggg\_other.js

-   Create ggg\_other.js in modules/ folder and sync

define(\[
    "dojo", "dojo/\_base/declare"
\], function( dojo, declare )
{
return declare("bgagame.other", null, { // null here if we don't want to inherit from anything
        constructor: function(){},
        mystuff: function(){},
    });
        
});

-   Modify ggg.js to include it

  

 define(\[ "dojo", "dojo/\_base/declare", "ebg/core/gamegui", "ebg/counter",
   g\_gamethemeurl + "modules/ggg\_other.js"     // load my own module!!!
 \], function(dojo,
       declare) {
    

use it

 foo = new bgagame.other();

### Including your own JavaScript module (II)

-   Create ggg\_other.js in modules/ folder and sync

 define(\[\], function () {
   return "value";
 }); 

-   Modify ggg.js to include it

 define(\[ 
   "dojo", 
   "dojo/\_base/declare", 
   "bgagame/modules/ggg\_other", 
   "ebg/core/gamegui", 
   "ebg/counter"
 \], function(dojo, declare, other) {
 
 });

  
This is maybe a little bit more the idea of the AMD Loader than the first option, although the first option should work as well.

A little explanation to this: The define function loads all the modules listed in the array and calls the following function with these loaded modules as parameters. By putting your module at the third position in the array it is passed as the third parameter to the function. Be aware that the modules are resolved by position only, not by name. So you can load the module **ggg\_other** and pass it as a parameter with the name **other**. **gamegui** and **counter** are passed in as well, but when the parameters are not defined they are just skipped. Because these modules put their content into the global scope it does not matter and you can use them from there.

In the example above the string "value" is passed for the parameter **other**, but the function in your module can return whatever you want. It can be an object, an array, something you declared with dojo.declare, you can return even functions. Your module can load other modules. Just put them in the array at the beginning and pass them as parameters to your function. The advantage of passing the values as parameter is that you do not need to put these values in the global scope, so they can't be collisions with values defined in other scripts or the BGA Framework.

The dojo toolkit provides good documentation to all of its components, the complete documentation for the AMD-Loader is here: [https://dojotoolkit.org/documentation/tutorials/1.10/modules/index.html](https://dojotoolkit.org/documentation/tutorials/1.10/modules/index.html) It should be still correct, even as it seems to be only for version 1.10

### Including your own PHP module

**Ingredients:** ggg.game.php, modules/ggg\_other.php

-   Create ggg\_other.php in modules/ folder and sync
-   Modify ggg.game.php to include it

require\_once ('modules/ggg\_other.php');

### Creating a test class to run PHP locally

**Ingredients:** ggg.game.php, stubs For this you need stubs of other method you can use this for example [https://github.com/elaskavaia/bga-sharedcode/raw/master/misc/module/table/table.game.php](https://github.com/elaskavaia/bga-sharedcode/raw/master/misc/module/table/table.game.php)

Create another php files, i.e ggg\_test.php

<?php
define("APP\_GAMEMODULE\_PATH", "misc/"); // include path to stubs, which defines "table.game.php" and other classes
require\_once ('eminentdomaine.game.php');

class MyGameTest1 extends MyGame { // this is your game class defined in ggg.game.php
    function \_\_construct() {
        parent::\_\_construct();
        include '../material.inc.php';// this is how this normally included, from constructor
    }

    // override/stub methods here that access db and stuff
    function getGameStateValue($var) {
        if ($var == 'round')
            return 3;
        return 0;
    }
}
$x = new MyGameTest1(); // instantiate your class
$p = $x->getGameProgression(); // call one of the methods to test
if ($p != 50)
    echo "Test1: FAILED";
else
    echo "Test1: PASSED";

Run from command line like

php8.4 ggg\_test.php

If you do it this way - you can also use local php debugger (i.e. integrated with IDE or command line).

### Avoiding code in dojo declare style

Dojo class declarations are rather bizzare and do not work with most IDEs. If you want to write in plain JS with classes, you can stub all the dojo define/declare stuff and hook your class into that, so the classes are outside of this mess.

NOTE: this technique is for experienced developers, do not try it if you do not understand the consequences.

This is complete example of game .js class

  // Testla is game name is has to be changed
class Testla {
	constructor(game) {
		console.log('game constructor');
		this.game = game;
		this.varfoo = new MyFoo(); // this example of class from custom module
	}

	setup(gamedatas) {
		console.log("Starting game setup", this.varfoo);
		this.gamedatas = gamedatas;
		this.dojo.create("div", { class: 'whiteblock', innerHTML: \_("hello") }, 'thething');
		console.log("Ending game setup");
	};
	onEnteringState(stateName, args) {
		console.log('onEnteringState : ' + stateName, args);
		this.game.addActionButton('b1',\_('Click Me'), (e)=>this.onButtonClick(e));
	};
	onLeavingState(stateName) {
		console.log('onLeavingState : ' + stateName, args);
	};
	onUpdateActionButtons(stateName, args) {
		console.log('onUpdateActionButtons : ' + stateName, args);
	};
	onButtonClick(event) {
		console.log('onButtonClick',event);
	};
};


define(\[
	"dojo", "dojo/\_base/declare",
	"ebg/core/gamegui",
	"ebg/counter",
	g\_gamethemeurl + '/modules/foo.js' // custom module if needed
\],
	function(dojo, declare) {
                // testla is game name is has to be changed
		return declare("bgagame.testla", ebg.core.gamegui, {
			constructor: function() {
				this.xapp = new Testla(this);
				this.xapp.dojo = dojo;
			},
			setup: function(gamedatas) {
				this.xapp.setup(gamedatas);
			},
			onEnteringState: function(stateName, args) {
				this.xapp.onEnteringState(stateName, args?.args);
			},
			onLeavingState: function(stateName) {
				this.xapp.onLeavingState(stateName, args);
			},
			onUpdateActionButtons: function(stateName, args) {
				this.xapp.onUpdateActionButtons(stateName, args);
			},
		});
	});

### More readable JS: onEnteringState

If you have a lot of states in onEnteringState or onUpdateActionButtons and friends - it becomes rather wild, you can do this trick to call some methods dynamically.

     onEnteringState: function(stateName, args) {
       console.log('Entering state: ' + stateName, args);

       // Call appropriate method
       var methodName = "onEnteringState\_" + stateName;
       if (this\[methodName\] !== undefined) {             
          console.log('Calling ' + methodName, args.args);
          this\[methodName\](args.args);
       }
     },

     onEnteringState\_playerTurn: function(args) { // this is args directly, not args.args 
         // process
     },

     onEnteringState\_playerSomethingElse: function(args) { 
         // process
     },

Note: since its ignores the undefined functions you don't have define function for each state, but on the other hand you cannot make typos. Same applies to onUpdateActionButtons except you pass 'args' to method, not args.args, and for onLeavingState where you don't pass anything.

### Frameworks and Preprocessors

-   [BGA Type Safe Template](/BGA_Type_Safe_Template "BGA Type Safe Template") - Setting up a fully typed project using typescript and more!
-   [Using Vue](/Using_Vue "Using Vue") - work-in-progress guide on using the modern framework Vue.js to create a game
-   [Using Typescript and Scss](/Using_Typescript_and_Scss "Using Typescript and Scss") - How to auto-build Typescript and SCSS files to make your code cleaner

  

### PHP Migration

**Ingredients:** \*/php, modules/\*.php

BGA recently migrated to from 7.4 to 8.2 then 8.4. New php has new rules and deprecations.

There is a tool that can help you do the migration automation, which is php module called rector [https://getrector.com/](https://getrector.com/). Below is the recipe that converts variables in strings like ${var} (which is deprecated) to {$var}.

1\. Install the module

composer global require --dev rector/rector

2\. Go to your project directory, commit your code first before this!

3\. Create a rector.php file on top level with the following content:

<?php

use Rector\\Config\\RectorConfig;
use Rector\\Php82\\Rector\\Encapsed\\VariableInStringInterpolationFixerRector;

return RectorConfig::configure()
    ->withPaths(\[
        \_\_DIR\_\_ 
    \])
    // A. whole set
    //->withPreparedSets(typeDeclarations: true)
    // B. or few rules
    ->withRules(\[
        VariableInStringInterpolationFixerRector::class
    \]);

4\. Dry run (mine is on linux, not sure where global install on windows)

 ~/.config/composer/vendor/bin/rector process --dry-run

5\. If happy re-run without --dry-run

6\. Can remove rector.php now (or can do different rules)

## Backend

### Assigning Player Order

Normally when game starts there is "natural" player order assigned randomly.

If you want to deliberatly assign player order at the start of the game (for example, in a game with teams options), you can do so by retrieving the initialization-only player attribute **player\_table\_order** and using it to assign values to **player\_no** (which is normally assigned at the start of a game in the order in which players come to the table). (See [Game database model](https://en.doc.boardgamearena.com/Game_database_model:_dbmodel.sql#The_player_table) for more details.)

  

**WARNING:** To prevent unfair advantage (e.g. collusion), the random order must be the default option and **non-random options should be limited to friendly mode.**

  
**Example:**

                // Retrieve inital player order (\[0=>playerId1, 1=>playerId2, ...\])
		$playerInitialOrder = \[\];
		foreach ($players as $playerId => $player) {
			$playerInitialOrder\[$player\['player\_table\_order'\]\] = $playerId;
		}
		ksort($playerInitialOrder);
		$playerInitialOrder = array\_flip(array\_values($playerInitialOrder));

		// Player order based on 'playerTeams' option
		$playerOrder = \[0, 1, 2, 3\];
		switch ($this->getGameStateValue('playerTeams')) {
			case $this->TEAM\_1\_2:
				$playerOrder = \[0, 2, 1, 3\];
				break;
			case $this->TEAM\_1\_4:
				$playerOrder = \[0, 1, 3, 2\];
				break;
			case $this->TEAM\_RANDOM:
				shuffle($playerOrder);
				break;
			default:
			case $this->TEAM\_1\_3:
				// Default order
				break;
		}

                // Create players
		// Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
		$sql =
			'INSERT INTO player (player\_id, player\_color, player\_canal, player\_name, player\_avatar, player\_no) VALUES ';
		$values = \[\];

		foreach ($players as $playerId => $player) {
			$color = array\_shift($default\_colors);
			$values\[\] =
				"('" .
				$playerId .
				"','$color','" .
				$player\['player\_canal'\] .
				"','" .
				addslashes($player\['player\_name'\]) .
				"','" .
				addslashes($player\['player\_avatar'\]) .
				"','" .
				$playerOrder\[$playerInitialOrder\[$playerId\]\] .
				"')";
		}
		$sql .= implode(',', $values);
		$this->DbQuery($sql);
		$this->reattributeColorsBasedOnPreferences(
			$players,
			$gameinfos\['player\_colors'\]
		);
		$this->reloadPlayersBasicInfos();

### Send different notifications to active player vs everybody else

**Ingredients:** ggg.js

Hack alert. This is a hack. We were hoping for proper solution by bga framework.

This will allow you to send notification with two message one for specific player and one for everybody else including spectators. Note that this does not split the data - all data must be shared.

Add this to .js file

bgaFormatText: function(log, args) {
   if (typeof args.log\_others != 'undefined' && typeof args.player\_id != 'undefined' && this.player\_id != args.player\_id) {
      log = args.log\_others;
   }
   return { log, args }; // you must return this so the framework can handle the default formatting
},

Example of usage (from eminentdomain)

    $this->notify->all('tokenMoved', 
             clienttranslate('${player\_name} adds +2 Colonies to ${place\_name}'), // notification with show for player with player\_id
             \['player\_id'=>$player\_id, // this is mandatory
             'log\_others'=>clienttranslate('${player\_name} adds +2 Colonies to an unknown planet'), // notification will show for others
              ...
             \]);

### Send transient notifications without incrementing move ID

**Ingredients:** ggg.php

Hack alert. This is a hack.

Use this if you need to send some transient notification that should not create a new move ID. The notification should be idempotent -- it should have no practical effect on the game state and would be **safe to drop** (e.g., it would not matter if a player never received this notification). For example, in a co-op game you want all players to see a real-time preview of some action, before the active player commits their turn.

Doing this mainly affects the instant replay & archive modes. During replay, the BGA framework automatically inserts a 1.5-second pause between each "move". With this hack, your transient notifications are not considered to be a "move", so no pause gets added.

In ggg.php

$this->not\_a\_move\_notification = true; // note: do not increase the move counter
$this->notify->all('cardsPreview', '', $args);

Note: you cannot have code that send notification or even changes state after this, and you cannot reset this variable back either because it only takes effect when you exit action handling function

  

## Assorted Stuff

### Out-of-turn actions: Un-pass

**Ingredients:** ggg.js, ggg.game.php, ggg.action.php, states.inc.php

In multiplayer game sometimes players passes but than they think more and want to un-Pass and redo their choice. To re-active a player who passes some trickery required.

Define a special action that does that and hook it up.

In states.inc.php add an action to MULTIPLE\_ACTIVE\_PLAYER state to "unpass", lets call it "actionCancel"

In ggg.action.php add action hook

   public function actionCancel() {
       $this->setAjaxMode();
       $this->game->actionCancel();
       $this->ajaxResponse();
   }

In ggg.game.php add action handler

   function actionCancel() {
       $this->gamestate->checkPossibleAction('actionCancel');
       $this->gamestate->setPlayersMultiactive(array ($this->getCurrentPlayerId() ), 'error', false);
   }

Finally to call this in client ggg.js you would do something like:

 onUpdateActionButtons:  function(stateName, args) {
   if (this.isCurrentPlayerActive()) { 
     // ...
   } else if (!this.isSpectator) { // player is NOT active but not spectator
       switch (stateName) {
          case 'playerTurnMultiPlayerState':
		this.addActionButton('button\_unpass', \_('Oh no!'), 'onUnpass');
		break;
	}
   }
 }
				
 onUnpass: function(e) {
    this.bgaPerformAction("actionCancel", null, { checkAction: false }); // no checkAction!
 }

Although be careful that if the turn comes back to the player while he is about to click cancel, the action buttons will be updated and the player will misclick which can be quite frustrating. To avoid this, move the cancel button to another position, like to the left of pagemaintitletext:

 dojo.place('button\_unpass', 'pagemaintitletext', 'before');

Being out of the generalactions div, it won't be automatically destroyed like normal buttons, so you'll have to handle that yourself in onLeavingState. You might also want to change the button color to red (blue buttons for active player only, red buttons also for inactive players?)

Note: same technique can be used to do other out-of-turn actions, such as re-arranging cards in hand, exchanging resources, etc (i.e. if permitted by rules, such as "at any time player can...")

### Multi Step Interactions: Select Worker/Place Worker - Using Selection

**Ingredients:** ggg.js

Simple way to implement something like that without extra states is to use "selection" mechanism. When user click on worker add some sort of class into that element i.e. 'selected' (which also have to have some indication by css i.e. outline).

Than user can click on placement zone, you can use dojo.query for "selected" element and use it along with zone id to send data to server. If proper worker is not selected yet can give a error message using this.showMessage(...) function.

Extra code required to properly cleanup selection between states. Also when you do that sometimes you want to change the state prompt, see below 'Change state prompt'

### Multi Step Interactions: Select Worker/Place Worker - Using Client States

**Ingredients:** ggg.js

I don't think its documented feature but there is a way to do client-only states, which is absolutely wonderful for few reasons

-   When player interaction is two step process, such as select worker, place worker, or place worker, pick one of two resources of your choice
-   When multi-step process can result of impossible situation and has to be undone (by rules)
-   When multi-step process is triggered from multiple states (such as you can do same thing as activated card action, pass action or main action)

So lets do Select Worker/Place Worker

Define your server state as usual, i.e. playerMainTurn -> "You must pick up a worker". Now define a client state, we only need "name" and "descriptionmyturn", lets say "client\_playerPicksLocation". Always prefix names of client state with "client\_" to avoid confusion. Now we have to do the following:

-   Have a handler for onUpdateActionButtons for playerMainTurn to activate all possible workers he can pick
-   When player clicks workers, remember the worker in one of the members of the main class, I usually use one called this.clientStateArgs.
-   Transition to new client state

 onWorker: function(e) {
     var id = event.currentTarget.id;
     dojo.stopEvent(event);
     ... // do validity checks
     this.clientStateArgs.worker\_id = id;
     this.setClientState("client\_playerPicksLocation", {
                               descriptionmyturn : \_("${you} must select location"),
                           });
  }

-   Have a handler for onUpdateActionButtons for client\_playerPicksLocation to activate all possible locations this worker can go AND add Cancel button (see below)
-   Have a location handler which will eventually send a server request, using stored this.clientStateArgs.worker\_id as worker id
-   The cancel button should call a method to restore server state, also if you doing it for more than one state you can add this universally using this.on\_client\_state check

  

       if (this.isCurrentPlayerActive()) {
         if (this.on\_client\_state && !$('button\_cancel')) {
              this.addActionButton('button\_cancel', \_('Cancel'), dojo.hitch(this, function() {
                                            this.restoreServerGameState();
              }));
         }
       } 

Note: usually I call my own function call this.cancelLocalStateEffects() which will do more stuff first then call restoreServerGameState(), same function is usually needs to be called when server request has failed (i.e. invalid move)

Note: If you need more than 2 steps, you may have to do client side animation to reflect the new state, which gets trickier because you have to undo that also on cancellation.

Code is available here [sharedcode.js](https://github.com/elaskavaia/bga-sharedcode/blob/master/sharedcode.js) (its using playerTurnPlayCubes and client\_selectCubeLocation).

### Action Stack - Using Client States

Action stack required where game is very complex and use triggered effects that can "stack". It not always actual stack, it can be queue or random access.

Examples:

-   Magic the Gathering - classic card game where effects go on Stack, that allows to counter spell and counter spell of counter spell (not on bga - it just example of mechanics)
-   Ultimate Railroads - action taking game where effects can be executed in any order
-   Lewis and Clark - card game where actions executed as queue

There is two ways of implementing it - on the server or the client. For the server see article below. The requirement for client side stack implementation is - all action can be undone, which means

-   No dice rolls
-   No card drawn
-   No other players interaction

No snippets are here, as this will be too complex but basically flow is:

-   You have a action/effect stack (queue/list) as js object attached to "this", i.e. this.unprocessed\_actions
-   When player plays a card, worker, etc, you read the effect of that card from the material file (client copy), and place into stack
-   Then we call dispatch method which pulls the next action from the stack and change client state accordinly, i.e. this.setClientState("client\_playerGainsCubes")
-   When players acts on it - the action is removed from the stack and added to "server action arguments" list, this is another object which be used to send ajax call, i.e. this.clientStateArgs
-   If nothing left in stack we can submit the ajax call assembling parameters from collected arguments (that can include action name)
-   This method allows cheap undo - by restoring server state you will wipe out all user actions (but if you need intermediate aninmation you have to handle it yourself)

Code can be found in Ultimate Railroads game (but it is random access list - so it a bit complex) and Lewis and Clark (complexity - user can always deny part of any effect)

  

### Action Stack - Using Server States

See definition of Action Stack above.

To implement you usually need another db table that has the following fields: index of effect - which is used for sorted access, type - which is essense of the effect (i.e. collect resource), some extra arguments (i.e. resource type and resource count), and usually owner of the effect (i.e. player id) The flow is:

-   There is some initial player state, where player can play card for example
-   Player main action - pushes the card effect on stack, which also can cause triggered effects which also go on stack
-   After action processing is finished switch to game state which is "dispatcher"
-   Dispatcher pulls the top effect (whatever definition of the top is), changes the active player and changes the state to appropriate player state to collect response. The "top" can be choice of multiple actions, in this case player has to chose one before resolving the effect.
-   Player state knows about the stack and pulls arguments (argX) from the effect arguments of the db
-   Player action should clear up the top effect, and can possibly add more effects, then switch to "dispatcher" state again
-   If stack is empty, dispatcher can either pick next player itself or use another game state which responsible for picking next player

Code can be found in Tapestry and Terraforming Mars.

### Custom error/exception handling in JavaScript

In ggg.php

Throw \\BgaUserException with some easy-to-identify prefix such as "!!!" and a custom error code. DO NOT TRANSLATE this message text. The exception will rollback database transaction and cancel all changes (including any notifications).

    function foo(bool $didConfirm = false): void
    {
        // do processing for the user's move
        // afterwards, you determine this move will end the game
        // so you want to rollback the transaction and require the user to confirm the move first

        if ($gameIsEnding && !$didConfirm) {
            throw new \\BgaUserException('!!!endGameConfirm', 9001);
        }
    }

In ggg.js

Override framework function showMessage to suppress the red banner message and gamelog message when you detect the "!!!" prefix

    /\* @Override \*/
    showMessage: function (msg, type) {
      if (type == "error" && msg && msg.startsWith("!!!")) {
        return; // suppress red banner and gamelog message
      }
      this.inherited(arguments);
    },

Deal with the error in your callback:

    fooAction: function (didConfirm) {
      var data = {
        foo: "bar",
        didConfirm: !!didConfirm,
      };
      this.bgaPerformAction("fooAction", data).catch((error, errorMsg) => {
        if (error && errorMsg == "!!!endGameConfirm") {
          // your custom error handling goes here
          // for example, show a confirmation dialog and repeat the action with additional param
          this.confirmationDialog(
            \_("Doing the foo action now will end the game"),
            () => this.fooAction(true)
          );
        }
      });
    },

For custom global error handling, you could modify ajaxcallwrapper:

  ajaxcallwrapper: function (action, args, handler) {
    if (!args) args = {};
    args.lock = true;
    args.version = this.gamedatas.version;
    if (this.checkAction(action)) {
      this.bgaPerformAction(
        action,
        args
      ).catch((error, errorMsg, errorCode) => {
          if (error && errorMsg == "!!!checkVersion") {
            this.infoDialog(
              \_("A new version of this game is now available"),
              \_("Reload Required"),
              () => {
                window.location.reload();
              },
              true
            );
          } else {
            if (handler) handler(error, errorMsg, errorCode);
          }
        }
      );
    }
  },

### Force players to refresh after new deploy

When you deploy a new version of your game, the PHP backend code is immediately updated but the JavaScript/HTML/CSS frontend code \*does not update\* for active players until they manually refresh the page (F5) in their browser. Obviously this is not ideal. In the best case, real-time tables don't see your shiny new enhancements. In the worst case, your old JS code isn't compatible with your new PHP code and the game breaks in strange ways (any bug reports filed will be false positives and unable to reproduce). To avoid any problems, you should force all players to immediately reload the page following a new deploy.

By throwing a "visible" exception (simplest solution), you'll get something like this which instructs the user to reload:

[![Force-refresh.png](/images/thumb/1/13/Force-refresh.png/950px-Force-refresh.png)](/File:Force-refresh.png)

Or, if you combine this technique with the above custom error handling technique, you could do something a bit nicer. You could show a dialog box and automatically refresh the page when the user clicks "OK":

[![Reload-required.png](/images/thumb/f/f0/Reload-required.png/500px-Reload-required.png)](/File:Reload-required.png)

In ggg.php

Transmit the server version number in `getAllDatas()`.

    protected function getAllDatas(): array
    {
        $players = $this->getCollectionFromDb("SELECT player\_id id, player\_score score FROM player");
        return \[
            'players' => $players,
            'version' => intval($this->gamestate->table\_globals\[300\]), // <-- ADD HERE
            ...
        \];
    }

Create a helper function to fail if the client and server versions mismatch. Note the version check uses `!=` instead of `<` so it can support rollback to a previous deploy as well. ;-)

    public function checkVersion(int $clientVersion): void
    {
        if ($clientVersion != intval($this->gamestate->table\_globals\[300\])) {
            // Simplest way is to throw a "visible" exception
            // It's ugly but comes with a "click here" link to refresh
            throw new BgaVisibleSystemException($this->\_("A new version of this game is now available. Please reload the page (F5)."));

            // For something prettier, throw a "user" exception and handle in JS
            // (see BGA cookbook section above on custom error handling)
            throw new \\BgaUserException('!!!checkVersion');
        }
    }

Every action requires a parameter `int $version` and a call to `$this->checkVersion()` as the first line. The version check should happen before anything else, even before checking if the action is allowed (since possible actions could change between versions). If you are using auto-wired "act" action functions, modify each to start like this:

    #\[CheckAction(false)\]
    public function actXxx(int $version, ...) {
        $this->checkVersion($version);
        $this->checkAction('actXxx'); // or $this->gamestate->checkPossibleAction('actXxx');
        ...
    }

In ggg.js

Transmit the version (from gamedatas) as a parameter with every ajax call. For example, if you're already using a wrapper function for every ajax call, add it like this:

  ajaxcallwrapper: function (action, args) {
    if (!args) args = {};
    args.version = this.gamedatas.version; // <-- ADD HERE
    this.bgaPerformAction(action, args);
  },

### Disable / lock table creation for new deploy

If you are deploying a major new game version, especially if it involves upgrading production game databases, you may have a lot of angry players if you break their tables. Depending on your changes, you may be able to restore the previous version and fix the tables easily.

However, if a new deploy turns out bad and players created turn-based tables while it was live, it may be quite difficult to fix those tables, since they were created from a bad deploy.

The solution? You can announce in your game group that you are locking table creation, and then in your new version, add an impossible startcondition to an existing option. Note: This only makes sense if you have a few games running in real time mode in the time of deployment, otherwise it won't achieve much, unless you wait at least a day for other turn based games to break (or not)

Here is an example of an option with only 2 values (if you don't have options at all you have to create a fake option to use this method, if you have more values - you have to list them all):

In gameoptions.json

        "startcondition": {
            "0": \[ { "type": "minplayers", "value": 32, "message": "Maintenance in progress.  Table creation is disabled." } \],
            "1": \[ { "type": "minplayers", "value": 32, "message": "Maintenance in progress.  Table creation is disabled." } \]
        },

In gameoptions.inc.php (older method if you have it in php)

// TODO NEXT remove after testing deploy to upgrade, here 0 and 1 - replace with values of your option!
'startcondition' => \[
   0 => \[ \[ 'type' => 'minplayers', 'value' => 32, 'message' => totranslate('Maintenance in progress.  Table creation is disabled.') \] \],
   1 => \[ \[ 'type' => 'minplayers', 'value' => 32, 'message' => totranslate('Maintenance in progress.  Table creation is disabled.') \] \],
\],

-   Be sure to click "Reload game options configuration" after making this change, then test in studio (test that you cannot create any table)
-   Deploy to production
-   Now, when a player attempts to create a new table, they will see a red error bar with your "Maintenance in progress" message.
-   Wait for screaming (if you have real times games in progress waiting 15 min probably ok, if you have only turn based games, probably a day)
-   Once you confirm the new deploy looks good, you can revert the change in gameoptions.inc.php and do another deploy.

### Local Storage

There is not much you can store in localStorage ([https://developer.mozilla.org/docs/Web/API/Window/localStorage](https://developer.mozilla.org/docs/Web/API/Window/localStorage)), since most stuff should be stored either in game db or in user prefrences, but some stuff makes sense to store there, for example "zoom" level (if you use custom zooming). This setting really affect this specific host and specific browser, setting it localStorage makes most sense.

game.js

   setup: function (gamedatas) {
        let zoom = localStorage.getItem(\`${this.game\_name}\_zoom\`);
        this.setZoom(zoom);
...
   },

In this case setZoom is custom function to actually set it. When zoom changed, for example when some buttons pressed, store current value (but sanitize it so it never so bad that game cannot be viewed

  

    setZoom: function (zoom) {
      zoom = parseInt(zoom) || 0;
      if (zoom === 0 || zoom < 0.1 || zoom > 10) {
        zoom = 1;
      }
      this.zoom = zoom;
      localStorage.setItem(\`${this.game\_name}\_zoom\`, "" + this.zoom);
... do actual zooming stuff
    },

  

### Capture client JavaScript errors in the "unexpected error" log

PHP (backend) errors are recorded in the "unexpected error" log, but JavaScript (frontend) errors are only available in the browser itself. This means you have no visibility about things that go wrong in the client... unless you make clients report their errors to the server.

In actions.php

  public function jsError()
  {
    $this->setAjaxMode(false);
    $this->game->jsError($\_POST\['userAgent'\], $\_POST\['msg'\]);
    $this->ajaxResponse();
  }

In game.php

    public function jsError($userAgent, $msg): void
    {
        $this->error("JavaScript error from User-Agent: $userAgent\\n$msg // ");
    }

In game.js

define(\["dojo", "dojo/\_base/declare", "ebg/core/gamegui", "ebg/counter"\], function (dojo, declare) {
  const uniqJsError = {};
  ...

  return declare("bgagame.nowboarding", ebg.core.gamegui, {
    ...
    /\* @Override \*/
    onScriptError(msg) {
      if (!uniqJsError\[msg\]) {
        uniqJsError\[msg\] = true;
        console.error("⛔ Reporting JavaScript error", msg);
        this.ajaxcall(
          "/" + this.game\_name + "/" + this.game\_name + "/jsError.html",
          {
            msg,
            userAgent: navigator.userAgent,
          },
          this,
          () => {},
          () => {},
          "post"
        );
      }
      this.inherited(arguments);
    },

## Algorithms

### Generate permutations in lexicographic order

Use this when you have an array like \[1, 2, 3, 4\] and need to loop over some/all 24 permutations of possible ordering. This type of [generator function](https://www.php.net/manual/en/language.generators.syntax.php) computes each possibility one at a time, making it vastly more efficient than either a normal iteration or recursive function that produce all possibilities up front.

function generatePermutations(array $array): Generator
{
    // https://en.wikipedia.org/wiki/Permutation#Generation\_in\_lexicographic\_order
    // Sort the array and this is the first permutation
    sort($array);
    yield $array;

    $count = count($array);
    do {
        // Find the largest index k where a\[k\] < a\[k + 1\]
        // End when no such index exists
        $found = false;
        for ($k = $count - 2; $k >= 0; $k--) {
            $kvalue = $array\[$k\];
            $knext = $array\[$k + 1\];
            if ($kvalue < $knext) {
                // Find the largest index l greater than k where a\[k\] < a\[l\]
                for ($l = $count - 1; $l > $k; $l--) {
                    $lvalue = $array\[$l\];
                    if ($kvalue < $lvalue) {
                        // Swap a\[k\] and a\[l\]
                        \[$array\[$k\], $array\[$l\]\] = \[$array\[$l\], $array\[$k\]\];

                        // Reverse the sequence from a\[k + 1\] up to and including the final element
                        $reverse = array\_reverse(array\_slice($array, $k + 1));
                        array\_splice($array, $k + 1, $count, $reverse);
                        yield $array;

                        // Restart with the new array to find the next permutation
                        $found = true;
                        break 2;
                    }
                }
            }
        }
    } while ($found);
}

Usage:

$cash = \[4, 1, 2, 3, 4\];
foreach ($this->generatePermutations($cash) as $p) {
    // your code here to evaluate permutation $p
    // first iteration: $p = \[1, 2, 3, 4, 4\]
    // last (60th) iteration: $p = \[4, 4, 3, 2, 1\]
    // break from loop once you achieve your goal
}

Retrieved from "[http:///index.php?title=BGA\_Studio\_Cookbook&oldid=26946](http:///index.php?title=BGA_Studio_Cookbook&oldid=26946)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")