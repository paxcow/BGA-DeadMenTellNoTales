<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * DeadMenPax implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * Game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 */
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

use Bga\Games\DeadMenPax\DB\PlayerDBManager;

class Game extends \Bga\GameFramework\Table
{
    private static array $CARD_TYPES;
    private BoardManager $boardManager;
    private PirateManager $pirateManager;
    private ItemManager $itemManager;
    private TokenManager $tokenManager;
    private PlayerDBManager $playerDBManager;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->initGameStateLabels([
            "my_first_global_variable" => 10,
            "my_second_global_variable" => 11,
        ]);        

        self::$CARD_TYPES = [
            1 => [
                "card_name" => clienttranslate('Troll'), // ...
            ],
            2 => [
                "card_name" => clienttranslate('Goblin'), // ...
            ],
            // ...
        ];

        // Initialize managers
        $this->playerDBManager = new PlayerDBManager($this);
        $this->pirateManager = new PirateManager($this);
        $this->itemManager = new ItemManager($this);
        $this->tokenManager = new TokenManager($this);
        $this->boardManager = new BoardManager($this);

        /* example of notification decorator.
        // automatically complete notification args when needed
        $this->notify->addDecorator(function(string $message, array $args) {
            if (isset($args['player_id']) && !isset($args['player_name']) && str_contains($message, '${player_name}')) {
                $args['player_name'] = $this->getPlayerNameById($args['player_id']);
            }
        
            if (isset($args['card_id']) && !isset($args['card_name']) && str_contains($message, '${card_name}')) {
                $args['card_name'] = self::$CARD_TYPES[$args['card_id']]['card_name'];
                $args['i18n'][] = ['card_name'];
            }
            
            return $args;
        });*/
    }

    /**
     * Plays a card.
     *
     * @param int $card_id The ID of the card to play.
     * @throws BgaUserException
     */
    public function actPlayCard(int $card_id): void
    {
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();

        // check input values
        $args = $this->argPlayerTurn();
        $playableCardsIds = $args['playableCardsIds'];
        if (!in_array($card_id, $playableCardsIds)) {
            throw new \BgaUserException('Invalid card choice');
        }

        // Add your game logic to play a card here.
        $card_name = self::$CARD_TYPES[$card_id]['card_name'];

        // Notify all players about the card played.
        $this->notify->all("cardPlayed", clienttranslate('${player_name} plays ${card_name}'), [
            "player_id" => $player_id,
            "player_name" => $this->getActivePlayerName(), // remove this line if you uncomment notification decorator
            "card_name" => $card_name, // remove this line if you uncomment notification decorator
            "card_id" => $card_id,
            "i18n" => ['card_name'], // remove this line if you uncomment notification decorator
        ]);

        // at the end of the action, move to the next state
        $this->gamestate->nextState("playCard");
    }

    /**
     * Passes the turn.
     */
    public function actPass(): void
    {
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();

        // Notify all players about the choice to pass.
        $this->notify->all("pass", clienttranslate('${player_name} passes'), [
            "player_id" => $player_id,
            "player_name" => $this->getActivePlayerName(), // remove this line if you uncomment notification decorator
        ]);

        // at the end of the action, move to the next state
        $this->gamestate->nextState("pass");
    }

    /**
     * Gets the arguments for the player turn.
     *
     * @return array
     */
    public function argPlayerTurn(): array
    {
        // Get some values from the current game situation from the database.

        return [
            "playableCardsIds" => [1, 2],
        ];
    }

    /**
     * Computes and returns the current game progression.
     *
     * @return int
     */
    public function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }

    /**
     * Handles the next player state.
     */
    public function stNextPlayer(): void {
        // Retrieve the active player ID.
        $player_id = (int)$this->getActivePlayerId();

        // Give some extra time to the active player when he completed an action
        $this->giveExtraTime($player_id);
        
        $this->activeNextPlayer();

        // Go to another gamestate
        $gameEnd = false; // Here, we would detect if the game is over to make the appropriate transition
        if ($gameEnd) {
            $this->gamestate->nextState("endScore");
        } else {
            $this->gamestate->nextState("nextPlayer");
        }
    }

    /**
     * Handles the end score state.
     */
    public function stEndScore(): void {
        // Here, we would compute scores if they are not updated live, and compute average statistics

        $this->gamestate->nextState();
    }

    /**
     * Migrates the database.
     *
     * @param int $from_version
     * @return void
     */
    public function upgradeTableDb($from_version)
    {
//       if ($from_version <= 1404301345)
//       {
//            // ! important ! Use `DBPREFIX_<table_name>` for all tables
//
//            $sql = "ALTER TABLE `DBPREFIX_xxxxxxx` ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
//
//       if ($from_version <= 1405061421)
//       {
//            // ! important ! Use `DBPREFIX_<table_name>` for all tables
//
//            $sql = "CREATE TABLE `DBPREFIX_xxxxxxx` ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
    }

    /**
     * Gathers all information about the current game situation.
     *
     * @return array
     */
    protected function getAllDatas(): array
    {
        $result = [];

        // WARNING: We must only return information visible by the current player.
        $current_player_id = (int) $this->getCurrentPlayerId();

        // Get information about players.
        $players = $this->playerDBManager->getAllObjects();
        $result['players'] = [];
        foreach ($players as $player) {
            $result['players'][$player->id] = [
                'id' => $player->id,
                'score' => $player->score,
            ];
        }

        // TODO: Gather all information about current game situation (visible by player $current_player_id).

        return $result;
    }

    /**
     * Sets up a new game.
     *
     * @param array $players The players in the game.
     * @param array $options The game options.
     */
    protected function setupNewGame($players, $options = [])
    {
        // Set the colors of the players with HTML color code. The default below is red/green/blue/orange/brown. The
        // number of colors defined here must correspond to the maximum number of players allowed for the gams.
        $gameinfos = $this->getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        foreach ($players as $player_id => $player) {
            // Now you can access both $player_id and $player array
            $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s')", [
                $player_id,
                array_shift($default_colors),
                $player["player_canal"],
                addslashes($player["player_name"]),
                addslashes($player["player_avatar"]),
            ]);
        }

        // Create players based on generic information.
        static::DbQuery(
            sprintf(
                "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES %s",
                implode(",", $query_values)
            )
        );

        $this->reattributeColorsBasedOnPreferences($players, $gameinfos["player_colors"]);
        $this->reloadPlayersBasicInfos();

        // Init global values with their initial values.
        $this->setGameStateInitialValue("my_first_global_variable", 0);

        // Setup the initial game situation here.
        $this->itemManager->setupItemCards();
        $itemAssignments = $this->itemManager->dealStartingItemCards();
        foreach ($itemAssignments as $playerId => $itemId) {
            $this->pirateManager->assignItem($playerId, $itemId);
        }

        // Include token definitions from material.inc.php
        include __DIR__ . '/material.inc.php';
        $this->tokenManager->setupTokens($tokens);
        // Additional setup for board, etc. will go here

        // Activate first player once everything has been initialized and ready.
        $this->activeNextPlayer();
    }

    /**
     * Handles a zombie player's turn.
     *
     * @param array{ type: string, name: string } $state
     * @param int $active_player
     * @return void
     * @throws feException if the zombie mode is not supported at this game state.
     */
    protected function zombieTurn(array $state, int $active_player): void
    {
        $state_name = $state["name"];

        if ($state["type"] === "activeplayer") {
            switch ($state_name) {
                default:
                {
                    $this->gamestate->nextState("zombiePass");
                    break;
                }
            }

            return;
        }

        // Make sure player is in a non-blocking status for role turn.
        if ($state["type"] === "multipleactiveplayer") {
            $this->gamestate->setPlayerNonMultiactive($active_player, '');
            return;
        }

        throw new \feException("Zombie mode not supported at this game state: \"{$state_name}\".");
    }

    /**
     * Places a new room tile on the board.
     *
     * @param int $tileId The ID of the tile.
     * @param int $x The x-coordinate.
     * @param int $y The y-coordinate.
     * @param int $orientation The orientation of the tile.
     */
    public function actPlaceTile(int $tileId, int $x, int $y, int $orientation = RoomTile::ORIENTATION_0): void
    {
        $playerId = (int)$this->getActivePlayerId();
        
        // Create room tile from material data (you would have this predefined)
        $tileData = $this->getTileData($tileId);
        $tile = new RoomTile(
            $tileId,
            $tileData['doors'],
            $tileData['color'],
            $tileData['pips'],
            $tileData['has_powder_keg'] ?? false,
            $tileData['has_trapdoor'] ?? false
        );
        
        // Try to place the tile with specified orientation
        if (!$this->boardManager->placeTile($tile, $x, $y, $orientation)) {
            throw new \BgaUserException("Cannot place tile at this position with this orientation");
        }
        
        // Notify all players
        $this->notify->all("tilePlaced", clienttranslate('${player_name} places a new room tile'), [
            "player_id" => $playerId,
            "player_name" => $this->getActivePlayerName(),
            "tile_id" => $tileId,
            "x" => $x,
            "y" => $y,
            "orientation" => $orientation
        ]);
    }
    
    
    /**
     * Handles the fight fire action.
     *
     * @param int $tileId The ID of the tile.
     */
    public function actFightFire(int $tileId): void
    {
        $playerId = (int)$this->getActivePlayerId();
        $tile = $this->boardManager->getTileById($tileId);
        
        if (!$tile) {
            throw new \BgaUserException("Invalid tile");
        }
        
        if ($tile->getFireLevel() <= 0) {
            throw new \BgaUserException("No fire to fight in this room");
        }
        
        // Decrease fire level
        $oldLevel = $tile->getFireLevel();
        $tile->decreaseFireLevel(1);
        
        $this->boardManager->saveToDatabase($tile);
        
        // Notify fire level change
        $this->notify->all("fireLevelChanged", clienttranslate('Fire level in room decreased'), [
            "player_id" => $playerId,
            "player_name" => $this->getActivePlayerName(),
            "tile_id" => $tileId,
            "old_level" => $oldLevel,
            "new_level" => $tile->getFireLevel()
        ]);
    }
    
    /**
     * Checks for and handles explosions.
     */
    public function checkForExplosions(): void
    {
        $tilesToCheck = [];

        // Find tiles that might explode
        foreach ($this->boardManager->getAllTiles() as $tile) {
            if ($tile->willExplode()) {
                $tilesToCheck[] = $tile;
            }
        }

        foreach ($tilesToCheck as $tile) {
            $explosionResult = $this->boardManager->handleChainExplosions($tile);
            $this->stHandleExplosion($explosionResult);

            // Check if ship is critically damaged
            if ($this->boardManager->isCriticallyDamaged()) {
                $this->notify->all("shipDestroyed", clienttranslate('The ship is critically damaged!'), []);
                $this->gamestate->nextState("gameEnd");
                return;
            }
        }
    }

    /**
     * Handles the results of an explosion.
     *
     * @param array $explosionResult The result of the explosion.
     */
    public function stHandleExplosion(array $explosionResult): void
    {
        foreach ($explosionResult['exploded_tiles'] as $tileInfo) {
            // Get affected pirates and tokens
            $piratesInTile = $this->pirateManager->getPiratesAt($tileInfo['x'], $tileInfo['y']);
            $tokensInTile = $this->tokenManager->getTokensInRoom($tileInfo['x'], $tileInfo['y']);

            // Tell PirateManager to handle its part
            foreach ($piratesInTile as $playerId) {
                $this->pirateManager->handleExplosionDamage($playerId, $tileInfo['type']);
            }

            // Tell TokenManager to handle its part
            foreach ($tokensInTile as $token) {
                $this->tokenManager->destroyToken($token['token_id']);
            }
        }
    }
    
    /**
     * Gets the board state for the client.
     *
     * @return array
     */
    public function getBoardState(): array
    {
        $tiles = [];
        foreach ($this->boardManager->getAllTiles() as $tile) {
            $tiles[] = [
                'id' => $tile->getId(),
                'x' => $tile->getX(),
                'y' => $tile->getY(),
                'doors' => $tile->getDoors(),
                'color' => $tile->getColor(),
                'pips' => $tile->getPips(),
                'fire_level' => $tile->getFireLevel(),
                'has_powder_keg' => $tile->hasPowderKeg(),
                'has_trapdoor' => $tile->hasTrapdoor(),
                'powder_keg_exploded' => $tile->isPowderKegExploded()
            ];
        }
        
        return [
            'tiles' => $tiles,
            'bounds' => $this->boardManager->getShipBounds(),
            'exploded_count' => count($this->boardManager->getExplodedTiles())
        ];
    }
    
    /**
     * Gets valid placement positions for a tile.
     *
     * @param int $tileId The ID of the tile.
     * @return array
     */
    public function getValidPlacements(int $tileId): array
    {
        $tileData = $this->getTileData($tileId);
        $tile = new RoomTile(
            $tileId,
            $tileData['doors'],
            $tileData['color'],
            $tileData['pips'],
            $tileData['has_powder_keg'] ?? false,
            $tileData['has_trapdoor'] ?? false
        );
        
        return $this->boardManager->getValidPlacementPositions($tile);
    }
    
    /**
     * Handles effects when a player enters a tile.
     *
     * @param int $playerId The ID of the player.
     * @param RoomTile $tile The tile entered.
     */
    private function handleTileEffects(int $playerId, RoomTile $tile): void
    {
        // Fire damage
        if ($tile->getFireLevel() > 0) {
            $this->increaseFatigue($playerId, $tile->getFireLevel());
        }
        
        // Other tile effects can be added here
    }
    
    /**
     * Gets tile data from `material.inc.php`.
     *
     * @param int $tileId The ID of the tile.
     * @return array
     */
    private function getTileData(int $tileId): array
    {
        // This would be replaced with actual tile data from your material.inc.php or database
        return [
            'doors' => RoomTile::DOOR_NORTH | RoomTile::DOOR_SOUTH,
            'color' => RoomTile::COLOR_RED,
            'pips' => 3,
            'has_powder_keg' => false,
            'has_trapdoor' => false
        ];
    }
    
    /**
     * Increases a player's fatigue.
     *
     * @param int $playerId The ID of the player.
     * @param int $amount The amount to increase fatigue by.
     */
    private function increaseFatigue(int $playerId, int $amount): void
    {
        $player = $this->playerDBManager->createObjectFromDB($playerId);
        $player->fatigue += $amount;
        $this->playerDBManager->saveObjectToDB($player);
        
        $this->notify->all("fatigueChanged", clienttranslate('${player_name}\'s fatigue increases'), [
            "player_id" => $playerId,
            "player_name" => $this->getPlayerNameById($playerId),
            "amount" => $amount
        ]);
    }

    /**
     * Jumps to a specific game state for debugging.
     *
     * @param int $state The ID of the state to jump to.
     */
    public function debug_goToState(int $state = 3) {
        $this->gamestate->jumpToState($state);
    }

    /*
    Another example of debug function, to easily create situations you want to test.
    Here, put a card you want to test in your hand (assuming you use the Deck component).

    public function debug_setCardInHand(int $cardType, int $playerId) {
        $card = array_values($this->cards->getCardsOfType($cardType))[0];
        $this->cards->moveCard($card['id'], 'hand', $playerId);
    }
    */
}
