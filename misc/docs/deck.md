# Deck - Board Game Arena

This is a documentation for [Board Game Arena](http://boardgamearena.com): play board games online !

# Deck

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

-   Deck: a PHP component to manage cards (deck, hands, picking cards, moving cards, shuffle deck, ...).
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

"Deck" is one of the most useful component on the PHP side. With "Deck", you can manage the cards in your game on the server side.

Using "deck", you will be able to use the following features without writing a single SQL database request:

-   Place cards in a pile, shuffle cards, draw cards one by one or many at a time.
-   "Auto-reshuffle" the discard pile into the deck when the deck is empty.
-   Move cards between different locations: hands of players, the table, etc.

  

## Contents

-   [1 Using Deck: Hearts example](#Using_Deck:_Hearts_example)
-   [2 Deck overview](#Deck_overview)
    -   [2.1 The 5 properties of each card](#The_5_properties_of_each_card)
    -   [2.2 Create a new Deck component](#Create_a_new_Deck_component)
    -   [2.3 Simple examples using Deck](#Simple_examples_using_Deck)
-   [3 Deck component reference](#Deck_component_reference)
    -   [3.1 Initializing Deck component](#Initializing_Deck_component)
    -   [3.2 Card standard format](#Card_standard_format)
    -   [3.3 Picking cards](#Picking_cards)
    -   [3.4 Moving cards](#Moving_cards)
    -   [3.5 Get cards informations](#Get_cards_informations)
    -   [3.6 Shuffling](#Shuffling)
    -   [3.7 Auto-reshuffle](#Auto-reshuffle)

## Using Deck: Hearts example

The Deck component is extensively used in the sample _Hearts_ card game. You will find in "hearts.game.php" that the object "$this->cards" is used many times.

## Deck overview

With Deck component, you manage all cards of your game.

### The 5 properties of each card

Using the Deck component, each card will have 5 properties:

-   **id**: This is the unique ID of each card.
-   **type** and **type\_arg**: These two values define the type of your card (i.e., what sort of card is this?).
-   **location** and **location\_arg**: These two values define where the card is at now.

The id, type, and type\_arg properties are constants throughout the game. location and location\_arg change when your cards move from one place to another in the game area.

**id** is the unique ID of each card. Two cards cannot have the same ID. IDs are generated automatically by the Deck component when you create cards during the Setup phase of your game.

**type** and **type\_arg** defines the type of your card.

**type** is a short string, and **type\_arg** is an integer.

You can use these two values as you like to make sure you will be able to identify the different cards in the game. See usage of "type" and "type\_arg" below.

Examples of usage of "type" and "type\_arg":

-   In _Hearts_, "type" represents the color (suite) of the card (1 to 4) and "type\_arg" is the value of the card (1, 2, ... 10, J, Q, K).
-   In _Seasons_, "type" represents the type of the card (e.g., 1 is Amulet of Air, 2 is Amulet of Fire, etc...). type\_arg is not used.
-   In _Takenoko_, a Deck component is used for objective cards. "type" is the kind of objective (irrigation/panda/plot) and "type\_arg" is the ID of the specific objective to realize (e.g., "green bamboo x4"). Note that a second Deck component is used in _Takenoko_ to manage the "garden plot" pile.

**location** and **location\_arg** define where a card is at now. **location** is a short string, and **location\_arg** is an integer.

You can use 'location' and 'location\_arg' as you like, to move your card within the game area.

There are 3 special 'location' values that Deck manages automatically. You can choose to use these locations or not, depending on your needs:

-   'deck': the 'deck' location is a standard draw deck. Cards are placed face down in a stack and are drawn in sequential order during the game. 'location\_arg' is used to specify where the card is located within the stack (the card with the highest location\_arg value is the next to be drawn).
-   'hand': the 'hand' location represents cards in a player's hand. 'location\_arg' is set to the ID of each player.
-   'discard': the 'discard' location is used for discard piles. Card in 'discard' may be reshuffled into the deck if needed (see "autoreshuffle").

  
Tips: using the Deck component, you will use generic properties ("location", "type\_arg",...) for specific purposes in your game. Thus, during the design step before realizing your game, take a few minutes to write down the exact meaning of each of these generic properties in the context of your game.

### Create a new Deck component

For each Deck component in your game, you need to create a dedicated table in the SQL database. This table has a standard format. In practice, if you just want to have a Deck component named "card", you can copy/paste the following into your "dbmodel.sql" file:

CREATE TABLE IF NOT EXISTS \`card\` (
  \`card\_id\` int(10) unsigned NOT NULL AUTO\_INCREMENT,
  \`card\_type\` varchar(16) NOT NULL,
  \`card\_type\_arg\` int(11) NOT NULL,
  \`card\_location\` varchar(16) NOT NULL,
  \`card\_location\_arg\` int(11) NOT NULL,
  PRIMARY KEY (\`card\_id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO\_INCREMENT=1 ;

Note: the database schema of this table does not have to be exactly what is listed above. You can increase the size of the fields or add more fields. For additional fields you just have to do manual queries.

In particular, if you are going to have deck locations specific to individual players, you may wish to use their player IDs in the card\_location field. Those IDs can be 8+ characters long, leaving only 8 characters for the rest of the name if you use the varchar(16). If you exceed the size of the field, it will get silently truncated, which can be very difficult to troubleshoot!

Once you have done this (and restarted your game), you can declare the Deck component in your PHP code in your class constructor. For _Hearts_ for example, I added to the "Hearts()" method:

        $this->cards = $this->deckFactory->createDeck('card');

Note that we specify "card" here: the name of our previously created table. This means you can create several "Deck" components with multiple tables:

        $this->firstKindCards = $this->deckFactory->createDeck( "first\_kind\_card" );
        $this->secondKindCards = $this->deckFactory->createDeck( "second\_kind\_card" );

Most of the time this is not useful; a Deck component should manage all objects of the same kind (i.e., all cards in the game). Note that you need to create a table for each "Deck", table name should be "first\_kind\_card" but the fields must remain "card\_id", "card\_type" and so on.

Afterwards, we can initialize your "Deck" by creating all the cards of the game. Generally, this is done only once during the game, in the "setupNewGame" method.

The "Deck" component provides a fast way to initialize all your cards at once: createCards. Here is how it is used for "Hearts":

        // Create cards
        $cards = array();
        foreach( $this->colors as  $color\_id => $color ) // spade, heart, diamond, club
        {
            for( $value=2; $value<=14; $value++ )   //  2, 3, 4, ... K, A
            {
                $cards\[\] = array( 'type' => $color\_id, 'type\_arg' => $value, 'nbr' => 1);
            }
        }

        $this->cards->createCards( $cards, 'deck' );

As you can see, "createCards" takes a description of all cards of the game. For each type of card, you have to specify its "type", "type\_arg" and the number of cards to create ("nbr"). "createCards" create all cards and place them into the "deck" location (as specified in the second argument).

Now, you are ready to use "Deck"!

### Simple examples using Deck

(Most examples are from "Hearts" game)

     // In "getAllDatas', we need to send to the current player all the cards he has in hand:
     $result\['hand'\] = $this->cards->getCardsInLocation( 'hand', $player\_id );

     // At some time we want to check if all the cards (52) are in player's hands:
     if( $this->cards->countCardInLocation( 'hand' ) == 52 )
           // do something

     // When a player plays a card in front of him on the table:
     $this->cards->moveCard( $card\_id, 'cardsontable', $player\_id );

     // Note the use of the custom location 'cardsontable' here to keep track of cards on the table.

  

     // This is a new hand: let's gather all cards from everywhere in the deck:
     $this->cards->moveAllCardsInLocation( null, "deck" );

     // And then shuffle the deck
     $this->cards->shuffle( 'deck' );

     // And then deal 13 cards to each player
     // Deal 13 cards to each players
     // Create deck, shuffle it and give 13 initial cards
     $players = $this->loadPlayersBasicInfos();
     foreach( $players as $player\_id => $player )
     {
        $cards = $this->cards->pickCards( 13, 'deck', $player\_id );
           
        // Notify player about his cards
        $this->notify->player( $player\_id, 'newHand', '', array( 
            'cards' => $cards
         ) );
     }  

     // Note the use of "notify->player" instead of "notify->all": new cards is a private information ;)  

## Deck component reference

### Initializing Deck component

**init( $table\_name )**

Initialize the Deck component.

Argument:

-   table\_name: name of the DB table used by this Deck component.

Must be called before any other Deck method.

Usually, init is called in your game constructor.

HINT: Create the deck object with $this->deckFactory->createDeck in the constructor or you will get "$this->cards" as an invalid reference later.

Example with Hearts:

	function Hearts( )
	{
        (...)
        
        $this->cards = $this->deckFactory->createDeck('card');
	}

**createCards( $cards, $location='deck', $location\_arg=null )**

Create card items in your deck component. Usually, all card items are created once, during the setup phase of the game.

"cards" describe all cards that need to be created. "cards" is an array with the following format:

   // Create 1 card of type "1" with type\_arg=99,
   //  and 4 cards of type "2" with type\_arg=12,
   //  and 2 cards of type "3" with type\_arg=33

   $cards = array(
        array( 'type' => 1, 'type\_arg' => 99, 'nbr' => 1 ),
        array( 'type' => 2, 'type\_arg' => 12, 'nbr' => 4 ),
        array( 'type' => 3, 'type\_arg' => 33, 'nbr' => 2 )
        ...
   );

Note: During the "createCards" process, Deck generates unique IDs for all card items.

Note: createCards is optimized to create a lot of cards at once. Do not use it to create cards one by one.

If "$location" and "$location\_arg" arguments are not set, newly created cards are placed in the "deck" location. If "location" (and optionally location\_arg) is specified, cards are created for this specific location.

Note: 'location' and 'location\_arg' can not be set individually, it does not read these values from the passed array of cards.

HINT: Be sure to do the createCards in setupNewGame. Doing createCards in the constructor will throw database errors.

### Card standard format

When Deck component methods are returning one or several cards, the following format is used:

array(
   'id' => ..,          // the card ID
   'type' => ..,        // the card type
   'type\_arg' => ..,    // the card type argument
   'location' => ..,    // the card location
   'location\_arg' => .. // the card location argument
);

### Picking cards

**pickCard( $location, $player\_id )**

Pick a card from a "pile" location (ex: "deck") and place it in the "hand" of specified player.

Return the card picked or "null" if there are no more card in given location.

The return value is an array of the card data elements (id, type, type\_arg...) for that card.

This method supports auto-reshuffle (see "auto-reshuffle" below).

**pickCards( $nbr, $location, $player\_id )**

Pick "$nbr" cards from a "pile" location (ex: "deck") and place them in the "hand" of specified player.

Return an array with the cards picked (indexed by the card ID), or "null" if there are no more card in given location.

Note that the number of cards picked can be less than "$nbr" in case there are not enough cards in the pile location.

This method supports auto-reshuffle (see "auto-reshuffle" below). In case there are not enough cards in the pile, all remaining cards are picked first, then the auto-reshuffle is triggered, then the other cards are picked.

**pickCardForLocation( $from\_location, $to\_location, $location\_arg=0 )**

This method is similar to 'pickCard', except that you can pick a card for any sort of location and not only the "hand" location.

-   from\_location is the "pile" style location from where you are picking a card.
-   to\_location is the location where you will place the card picked.
-   if "location\_arg" is specified, the card picked will be set with this "location\_arg".

This method supports auto-reshuffle (see "auto-reshuffle" below).

**pickCardsForLocation( $nbr, $from\_location, $to\_location, $location\_arg=0, $no\_deck\_reform=false )**

This method is similar to 'pickCards', except that you can pick cards for any sort of location and not only the "hand" location.

-   from\_location is the "pile" style location from where you are picking some cards.
-   to\_location is the location where you will place the cards picked.
-   if "location\_arg" is specified, the cards picked will be set with this "location\_arg".
-   if "no\_deck\_reform" is set to "true", the auto-reshuffle feature is disabled during this method call.

This method supports auto-reshuffle (see "auto-reshuffle" below).

### Moving cards

**moveCard( $card\_id, $location, $location\_arg=0 )**

Move the specific card to given location.

-   card\_id: ID of the card to move.
-   location: location where to move the card.
-   location\_arg: if specified, location\_arg where to move the card. If not specified "location\_arg" will be set to 0.

  
**moveCards( $cards, $location, $location\_arg=0 )**

Move the specific cards to given location.

-   cards: an array of IDs of cards to move.
-   location: location where to move the cards.
-   location\_arg: if specified, location\_arg where to move the cards. If not specified "location\_arg" will be set to 0.

**insertCard( $card\_id, $location, $location\_arg )**

Move a card to a specific "pile" location where card are ordered.

If location\_arg place is already taken, increment all cards after location\_arg in order to insert new card at this precise location.

(note: insertCardOnExtremePosition method below is more useful in most of the case)

**insertCardOnExtremePosition( $card\_id, $location, $bOnTop )**

Move a card on top or at bottom of given "pile" type location. (Lower numbers: bottom of the deck. Higher numbers: top of the deck.)

(note: Filling an empty location this way with N cards creates "location\_arg"s from 1 to N if "$bOnTop" is true and -1 to -N if "$bOnTop" is false. This can cause off-by-one errors for code intended to run on a deck generated by "shuffle( $location )" which generates "location\_arg"s from 0 to N - 1.)

**moveAllCardsInLocation( $from\_location, $to\_location, $from\_location\_arg=null, $to\_location\_arg=0 )**

Move all cards in specified "from" location to given location.

-   from\_location: where to take the cards. If null, cards from all locations will be move.
-   to\_location: where to put the cards
-   from\_location\_arg (optional): if specified, only cards with given "location\_arg" are moved.
-   to\_location\_arg (optional): if specified, cards moved "location\_arg" is set to given value. Otherwise "location\_arg" is set to 0.

Note: if you want to keep "location\_arg" untouched, you should use "moveAllCardsInLocationKeepOrder" below.

**moveAllCardsInLocationKeepOrder( $from\_location, $to\_location )**

Move all cards in specified "from" location to given "to" location. This method does not modify the "location\_arg" of cards.

**playCard( $card\_id )**

Move specified card at the top of the "discard" location.

Note: this is an alias for: insertCardOnExtremePosition( $card\_id, "discard", true )

### Get cards informations

**getCard( $card\_id )**

Get specific card information.

Return null if this card is not found.

**getCards( $cards\_array )**

Get specific cards information.

$cards\_array is an array of card IDs.

If some cards are not found or if some card IDs are specified multiple times, the method throws an (unexpected) Exception.

**getCardsInLocation( $location, $location\_arg = null, $order\_by = null )**

Get all cards in specific location, as an array. Return an empty array if the location is empty.

-   location (string): the location where to get the cards.
-   location\_arg (optional): if specified, return only cards with the specified "location\_arg".
-   order\_by (optional): if specified, returned cards are ordered by the given database field. Example: "card\_id" or "card\_type".

Using the "order\_by" parameter changes the resulting array. Without parameter you get an associative array with the "card\_id", with the paramter you get a simple indexed array.

**countCardInLocation( $location, $location\_arg=null )**

Return the number of cards in specified location.

-   location (string): the location where to count the cards.
-   location\_arg (optional): if specified, count only cards with the specified "location\_arg".

**countCardsInLocations()**

Return the number of cards in each location of the game.

The method returns an associative array with the format "location" => "number of cards".

Example:

  array(
    'deck' => 12,
    'hand' => 21,
    'discard' => 54,
    'ontable' => 3
  );

**countCardsByLocationArgs( $location )**

Return the number of cards in each "location\_arg" for the given location.

The method returns an associative array with the format "location\_arg" => "number of cards".

Example: count the number of cards in each player's hand:

    countCardsByLocationArgs( 'hand' );
    
    // Result:
    array(
        122345 => 5,    // player 122345 has 5 cards in hand
        123456 => 4     // and player 123456 has 4 cards in hand
    );

  
**getPlayerHand( $player\_id )**

Get all cards in given player hand.

Note: This is an alias for: getCardsInLocation( "hand", $player\_id )

**getCardOnTop( $location )**

Get the card on top of the given ("pile" style) location, or null if the location is empty.

Note that the card pile won't be "auto-reshuffled" if there is no more card available.

**getCardsOnTop( $nbr, $location )**

Get the "$nbr" cards on top of the given ("pile" style) location.

The method return an array with at most "$nbr" elements (or a void array if there is no card in this location).

Note that the card pile won't be "auto-reshuffled" if there is not enough cards available.

**getExtremePosition( $bGetMax ,$location )**

(rarely used)

Get the position of cards at the top of the given location / at the bottom of the given location.

Of course this method works only on location in "pile" where you are using "location\_arg" to specify the position of each card (example: "deck" location).

If bGetMax=true, return the location of the top card of the pile.

If bGetMax=false, return the location of the bottom card of the pile.

**getCardsOfType( $type, $type\_arg=null )**

Get all cards of a specific type (rarely used).

Return an array of cards, or an empty array if there is no cards of the specified type.

-   type: the type of cards
-   type\_arg: if specified, return only cards with the specified "type\_arg".

**getCardsOfTypeInLocation( $type, $type\_arg=null, $location, $location\_arg = null )**

Get all cards of a specific type in a specific location (rarely used).

Return an array of cards, or an empty array if there is no cards of the specified type.

-   type: the type of cards
-   type\_arg: if specified, return only cards with the specified "type\_arg".
-   location (string): the location where to get the cards.
-   location\_arg (optional): if specified, return only cards with the specified "location\_arg".

### Shuffling

**shuffle( $location )**

Shuffle all cards in specific location.

Shuffle only works on locations where cards are on a "pile" (ex: "deck").

Please note that all "location\_arg" will be reset to reflect the new order of the cards in the pile.

### Auto-reshuffle

To enable auto-reshuffle you must do "`$this->cards->autoreshuffle = true`" during the setup of the component (often in the _\_construct_ function when you _init()_ the Deck object).

Every time a card must be retrieved from the "deck" location, if it is empty the "discard" location will be automatically reshuffled into the "deck" location.

If you need to notify players when the deck is shuffled, you can setup a callback method using this feature:

$this->cards->autoreshuffle\_trigger = array('obj' => $this, 'method' => 'deckAutoReshuffle');

If you need to use other locations than "deck" and "discard" for auto-reshuffle feature, you can configure it this way:

$this->cards->autoreshuffle\_custom = array('deck' => 'discard');

(replace 'deck' and 'discard' with your custom locations).

Retrieved from "[http:///index.php?title=Deck&oldid=26673](http:///index.php?title=Deck&oldid=26673)"

[Category](/Special:Categories "Special:Categories"):

-   [Studio](/Category:Studio "Category:Studio")