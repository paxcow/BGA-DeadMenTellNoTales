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

use Bga\Games\DeadMenPax\Models\PlayerModel;
use Bga\Games\DeadMenPax\DB\PlayerDBManager;
use Bga\Games\DeadMenPax\DB\RoomTilesManager;
use Bga\Games\DeadMenPax\StatsManager;

class Game extends \Bga\GameFramework\Table
{
    private const STATE_PENDING_TILE_ID = 10;
    private const STATE_BATTLE_CONTEXT = 12;

    private const JSON_KEY_BATTLE_CONTEXT = 'BATTLE_CONTEXT_JSON';

    private static array $CARD_TYPES;
    private BoardManager $boardManager;
    private RoomTilesManager $roomTilesManager;
    private PirateManager $pirateManager;
    private ItemManager $itemManager;
    private TokenManager $tokenManager;
    private PlayerDBManager $playerDBManager;
    private StatsManager $statsManager;
    /** @var array<int, array<string, mixed>> */
    private array $battleContexts = [];
    /** @var array<int, array<string, mixed>> */
    private array $startingRooms = [];
    /** @var array<int, array<string, mixed>> */
    private array $startingRoomLookup = [];
    /** @var array<int, array<string, mixed>> */
    private array $roomTiles = [];
    /** @var array<int, array<string, mixed>> */
    private array $revengeCards = [];
    /** @var array<int, array<string, mixed>> */
    private array $tokens = [];
    /** @var array<int, array<string, mixed>> */
    private array $itemCards = [];
    /** @var array<int, array<string, mixed>> */
    private array $roomTileLookup = [];
    private bool $materialLoaded = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->initGameStateLabels([
            'PENDING_TILE_ID' => self::STATE_PENDING_TILE_ID,
            'BATTLE_CONTEXT_JSON' => self::STATE_BATTLE_CONTEXT,
        ]);

        $this->ensureMaterialLoaded();

        // Initialize managers
        $this->playerDBManager = new PlayerDBManager($this);
        $this->pirateManager = new PirateManager($this);
        $this->itemManager = new ItemManager($this);
        $this->tokenManager = new TokenManager($this);
        $this->roomTilesManager = new RoomTilesManager($this, $this->getAllRoomTileDefinitions());
        $this->boardManager = new BoardManager($this, $this->roomTilesManager);
        $this->statsManager = new StatsManager($this);
        $this->loadBattleContextFromDB();

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
     * Computes and returns the current game progression.
     *
     * @return int
     */
    public function getGameProgression()
    {
        // Progress = weighted average of net looted treasures vs. explosion clock
        $target       = (int) $this->getGameStateValue('TARGET_TREASURES');
        $looted       = (int) $this->statsManager->getTableStat('treasures_looted');
        $destroyed    = (int) $this->statsManager->getTableStat('treasures_destroyed');
        $maxExplodes  = (int) $this->getGameStateValue('MAX_EXPLOSION_TRACK');
        $explosion    = (int) $this->getGameStateValue('explosion_track');
        // clamp ratios to [0,1]
        $pLoot = $target > 0 ? max(0, min(1, ($looted - $destroyed) / $target)) : 0;
        $pSafe = $maxExplodes > 0 ? max(0, min(1, 1 - $explosion / $maxExplodes)) : 0;
        // weights: 70% looting, 30% safety
        $progress = 0.7 * $pLoot + 0.3 * $pSafe;
        return (int) round(100 * $progress);
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

        // Create players in the database using the manager
        $this->playerDBManager->createPlayers($players, $default_colors);

        $this->reattributeColorsBasedOnPreferences($players, $gameinfos["player_colors"]);
        $this->reloadPlayersBasicInfos();

        // Init global values with their initial values.
        $this->setGameStateInitialValue("my_first_global_variable", 0);
        $this->setGameStateInitialValue('PENDING_TILE_ID', 0);
        $this->setGameStateInitialValue('BATTLE_CONTEXT_JSON', 0);
        $this->initializePersistentStateStores();
        $this->rebuildRuntimeCaches();

        $this->roomTilesManager->initializeTiles();
        $this->tokenManager->setupTokens($this->tokens);

        $this->initializeGame();
    }

    /**
     * Initializes the game.
     */
    public function initializeGame(): void
    {
        // Setup the initial game situation here.
        $this->ensureMaterialLoaded();
        $this->itemManager->setupItemCards();
        $itemAssignments = $this->itemManager->dealStartingItemCards();
        foreach ($itemAssignments as $playerId => $itemId) {
            $this->pirateManager->assignItem($playerId, $itemId);
        }
        // Additional setup for board, etc. will go here

        // Activate first player once everything has been initialized and ready.
        $this->activeNextPlayer();
    }

    /**
     * Forces every manager to rebuild its cached view of database rows.
     */
    private function rebuildRuntimeCaches(): void
    {
        $this->pirateManager->reload();
        $this->itemManager->reload();
        $this->tokenManager->reload();
    }

    /**
     * Ensures material definitions are loaded exactly once.
     */
    private function ensureMaterialLoaded(): void
    {
        if ($this->materialLoaded) {
            return;
        }

        require __DIR__ . '/material.inc.php';
        $this->assignRoomTileIds();
        $this->assignStartingRoomIds();
        $this->materialLoaded = true;
    }

    /**
     * Assigns deterministic IDs to room tiles and builds lookup maps.
     */
    private function assignRoomTileIds(): void
    {
        $this->roomTileLookup = [];
        foreach ($this->roomTiles as $index => $definition) {
            if (!isset($definition['id'])) {
                $definition['id'] = 100 + $index;
            }

            $this->roomTiles[$index] = $definition;
            $this->roomTileLookup[$definition['id']] = $definition;
        }
    }

    /**
     * Assigns deterministic IDs to starting room tiles.
     */
    private function assignStartingRoomIds(): void
    {
        $this->startingRoomLookup = [];
        foreach ($this->startingRooms as $index => $definition) {
            if (!isset($definition['id'])) {
                $definition['id'] = 1 + $index;
            }

            $this->startingRooms[$index] = $definition;
            $this->startingRoomLookup[$definition['id']] = $definition;
        }
    }

    /**
     * Returns all room tile definitions keyed by tile id.
     *
     * @return array<int,array<string,mixed>>
     */
    public function getAllRoomTileDefinitions(): array
    {
        $this->ensureMaterialLoaded();
        $definitions = [];

        foreach ($this->startingRooms as $definition) {
            $definitions[$definition['id']] = $definition;
        }

        foreach ($this->roomTiles as $definition) {
            $definitions[$definition['id']] = $definition;
        }

        return $definitions;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getTokenDefinitions(): array
    {
        $this->ensureMaterialLoaded();
        return $this->tokens;
    }


    /**
     * Draws the next room tile from the deck and persists the pointer.
     *
     * @return int
     */
    public function drawNextRoomTile(): int
    {
        $this->ensureMaterialLoaded();
        $tileId = $this->roomTilesManager->drawNextTileId();
        $this->setGameStateValue('PENDING_TILE_ID', $tileId);

        return $tileId;
    }

    /**
     * Gets the tile ID that is currently pending placement.
     *
     * @return int
     */
    public function getNextTileToPlace(): int
    {
        $tileId = (int) $this->getGameStateValue('PENDING_TILE_ID');
        if ($tileId === 0) {
            throw new \BgaSystemException('Unable to determine pending room tile.');
        }

        return $tileId;
    }

    /**
     * Gets the material definition for a tile ID.
     *
     * @param int $tileId
     * @return array
     */
    public function getTileData(int $tileId): array
    {
        $this->ensureMaterialLoaded();

        if (isset($this->roomTileLookup[$tileId])) {
            return $this->roomTileLookup[$tileId];
        }

        if (isset($this->startingRoomLookup[$tileId])) {
            return $this->startingRoomLookup[$tileId];
        }

        throw new \BgaSystemException("Unknown tile id {$tileId}.");
    }

    /**
     * Places a random token when a new room is added.
     *
     * @param int $tileId
     * @return string|null
     */
    public function placeTokenForNewRoom(int $tileId): ?string
    {
        return $this->tokenManager->placeRandomTokenInRoom($tileId);
    }

    /**
     * Checks whether the dinghy/second exit should be placed.
     * Placeholder for future detailed implementation.
     */
    public function checkForSecondExit(): void
    {
        // TODO: Implement dinghy placement rules.
    }

    /**
     * Loads persisted battle context rows into memory.
     */
    public function loadBattleContextFromDB(): void
    {
        if (!isset($this->playerDBManager)) {
            $this->battleContexts = [];
            return;
        }

        $this->battleContexts = [];
        $players = $this->playerDBManager->getAllObjects();
        foreach ($players as $player) {
            $context = $this->buildBattleContextFromPlayer($player);
            if ($context !== null) {
                $this->battleContexts[$player->id] = $context;
            }
        }

        if (!empty($this->battleContexts)) {
            $this->storeJsonState(self::JSON_KEY_BATTLE_CONTEXT, $this->battleContexts);
        } else {
            $this->battleContexts = $this->loadJsonState(self::JSON_KEY_BATTLE_CONTEXT);
        }
    }

    /**
     * Persists a player's battle context to both DB and JSON cache.
     *
     * @param int $playerId
     * @param array|null $context
     */
    public function persistBattleContext(int $playerId, ?array $context): void
    {
        $player = $this->requirePirate($playerId);

        if ($context === null) {
            $player->currentEnemyTokenId = null;
            $player->currentBattleRoomId = null;
            $player->battleState = null;
            unset($this->battleContexts[$playerId]);
        } else {
            $player->currentEnemyTokenId = $context['enemyTokenId'] ?? null;
            $player->currentBattleRoomId = $context['roomId'] ?? null;
            $player->battleState = $context['state'] ?? null;

            if ($player->currentEnemyTokenId === '') {
                $player->currentEnemyTokenId = null;
            }
            if ($player->battleState === '') {
                $player->battleState = null;
            }

            $filtered = array_filter([
                'enemyTokenId' => $player->currentEnemyTokenId,
                'roomId' => $player->currentBattleRoomId,
                'state' => $player->battleState,
            ], static fn($value) => $value !== null);

            if (empty($filtered)) {
                unset($this->battleContexts[$playerId]);
            } else {
                $this->battleContexts[$playerId] = $filtered;
            }
        }

        $this->pirateManager->persistPirate($player);
        $this->storeJsonState(self::JSON_KEY_BATTLE_CONTEXT, $this->battleContexts);
    }

    /**
     * Gets the current battle context for a player.
     *
     * @param int $playerId
     * @return array<string,mixed>|null
     */
    public function getBattleContext(int $playerId): ?array
    {
        return $this->battleContexts[$playerId] ?? null;
    }

    /**
     * Returns enemy tokens in the active player's current room.
     *
     * @param int $playerId
     * @return array<int, array<string, mixed>>
     */
    public function getEnemiesInCurrentRoom(int $playerId): array
    {
        $roomId = $this->pirateManager->getPirateRoom($playerId);
        if ($roomId === null) {
            return [];
        }

        return $this->tokenManager->getEnemiesInRoomById($roomId);
    }

    /**
     * Persists the current battle context for a player.
     *
     * @param int $playerId
     * @param array $context
     */
    public function setBattleContext(int $playerId, array $context): void
    {
        $this->persistBattleContext($playerId, $context);
    }

    /**
     * Clears a player's stored battle context.
     */
    public function clearBattleContext(int $playerId): void
    {
        $this->persistBattleContext($playerId, null);
    }

    /**
     * Describes the current enemy stored in battle context.
     *
     * @param int $playerId
     * @return array<string, mixed>|null
     */
    public function getCurrentBattleEnemy(int $playerId): ?array
    {
        $context = $this->getBattleContext($playerId);
        if ($context === null || empty($context['enemyTokenId'])) {
            return null;
        }

        return $this->tokenManager->describeEnemyToken($context['enemyTokenId']);
    }

    /**
     * Records the available room information before prompting for selection.
     */
    public function initializeEnemySelection(int $playerId): void
    {
        $enemies = $this->getEnemiesInCurrentRoom($playerId);
        if (empty($enemies)) {
            $this->clearBattleContext($playerId);
            return;
        }

        $context = $this->getBattleContext($playerId) ?? [];
        $context['roomId'] = $enemies[0]['room_id'];
        unset($context['enemyTokenId']);
        $this->persistBattleContext($playerId, $context);
    }

    /**
     * Sets the enemy token to fight in the player's battle context.
     */
    public function setCurrentBattleEnemy(int $playerId, string $tokenId): void
    {
        $context = $this->getBattleContext($playerId) ?? [];
        $context['enemyTokenId'] = $tokenId;

        if (empty($context['roomId'])) {
            $enemy = $this->tokenManager->describeEnemyToken($tokenId);
            if ($enemy !== null) {
                $context['roomId'] = $enemy['room_id'];
            }
        }

        $this->persistBattleContext($playerId, $context);
    }

    /**
     * Ensures a battle context exists for the player entering the Battle state.
     */
    public function initializeCurrentBattle(int $playerId): void
    {
        $context = $this->getBattleContext($playerId);
        if ($context !== null && !empty($context['enemyTokenId'])) {
            return;
        }

        $enemies = $this->getEnemiesInCurrentRoom($playerId);
        if (empty($enemies)) {
            $this->clearBattleContext($playerId);
            return;
        }

        $enemy = $enemies[0];
        $this->setBattleContext($playerId, [
            'enemyTokenId' => $enemy['id'],
            'roomId' => $enemy['room_id'],
        ]);
    }

    /**
     * Initializes JSON-backed persistent stores.
     */
    private function initializePersistentStateStores(): void
    {
        $this->battleContexts = [];
        $this->storeJsonState(self::JSON_KEY_BATTLE_CONTEXT, []);
    }

    /**
     * Creates a battle context array from a player model.
     *
     * @param PlayerModel $player
     * @return array<string,mixed>|null
     */
    private function buildBattleContextFromPlayer(PlayerModel $player): ?array
    {
        if (empty($player->currentEnemyTokenId) || $player->currentBattleRoomId === null) {
            return null;
        }

        $context = [
            'enemyTokenId' => $player->currentEnemyTokenId,
            'roomId' => $player->currentBattleRoomId,
        ];

        if (!empty($player->battleState)) {
            $context['state'] = $player->battleState;
        }

        return $context;
    }

    /**
     * Loads a JSON blob from the game_state helper table.
     *
     * @param string $key
     * @return array
     */
    private function loadJsonState(string $key): array
    {
        $keyEscaped = $this->escapeStringForDB($key);
        $row = $this->getObjectFromDB("SELECT state_value FROM game_state WHERE state_key = {$keyEscaped}");
        if (empty($row)) {
            return [];
        }

        $decoded = json_decode($row['state_value'], true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Stores a JSON blob into the helper table.
     *
     * @param string $key
     * @param array $value
     */
    private function storeJsonState(string $key, array $value): void
    {
        $json = json_encode($value);
        $keyEscaped = $this->escapeStringForDB($key);
        $valueEscaped = $this->escapeStringForDB($json);
        $sql = "INSERT INTO game_state (state_key, state_value)
                VALUES ({$keyEscaped}, {$valueEscaped})
                ON DUPLICATE KEY UPDATE state_value = {$valueEscaped}";
        $this->DbQuery($sql);
    }

    /**
     * Stores unused action tokens for a player.
     */
    public function storePassedTokens(int $playerId, int $count): void
    {
        $this->pirateManager->setExtraActions($playerId, max(0, $count));
    }

    /**
     * Gets the number of stored passed tokens for a player without mutating state.
     */
    public function getPassedTokensForPlayer(int $playerId): int
    {
        return $this->pirateManager->getExtraActions($playerId);
    }

    /**
     * Consumes and clears passed tokens for a player.
     */
    public function consumePassedTokens(int $playerId): int
    {
        $count = $this->pirateManager->getExtraActions($playerId);
        $this->pirateManager->setExtraActions($playerId, 0);
        return $count;
    }

    /**
     * Clears stored passed tokens for a player.
     */
    public function clearPassedTokens(int $playerId): void
    {
        $this->pirateManager->setExtraActions($playerId, 0);
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

        throw new \BgaSystemException("Zombie mode not supported at this game state: \"{$state_name}\".");
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
        
        $tile = $this->roomTilesManager->getTile($tileId);
        if ($tile === null) {
            $tileData = $this->getTileData($tileId);
            $tile = new RoomTile(
                $tileId,
                $tileData['doors'],
                $tileData['color'],
                $tileData['pips'],
                $tileData['has_powder_keg'] ?? false,
                $tileData['has_trapdoor'] ?? false,
                $tileData['is_starting_tile'] ?? false,
                0,
                $tileData['tile_type'] ?? 'room'
            );
        }
        
        // Try to place the tile with specified orientation
        if (!$this->boardManager->placeTile($tile, $x, $y, $orientation)) {
            throw new \BgaUserException("Cannot place tile at this position with this orientation");
        }

        $deckhandBefore = $tile->getDeckhandCount();
        if ($tile->hasTrapdoor()) {
            $tile->addDeckhands(1);
            $this->boardManager->saveToDatabase($tile);
        }

        $tokenId = $this->placeTokenForNewRoom($tileId);
        $this->checkForSecondExit();
        
        // Notify all players
        $this->notify->all("tilePlaced", clienttranslate('${player_name} places a new room tile'), [
            "player_id" => $playerId,
            "player_name" => $this->getActivePlayerName(),
            "tile_id" => $tileId,
            "x" => $x,
            "y" => $y,
            "orientation" => $orientation,
            "trapdoor" => $tile->hasTrapdoor(),
            "deckhands_added" => $tile->getDeckhandCount() - $deckhandBefore,
            "token_id" => $tokenId
        ]);

        $this->setGameStateValue('PENDING_TILE_ID', 0);
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
        $tile = $this->roomTilesManager->getTile($tileId);
        if ($tile === null) {
            $tileData = $this->getTileData($tileId);
            $tile = new RoomTile(
                $tileId,
                $tileData['doors'],
                $tileData['color'],
                $tileData['pips'],
                $tileData['has_powder_keg'] ?? false,
                $tileData['has_trapdoor'] ?? false,
                $tileData['is_starting_tile'] ?? false,
                0,
                $tileData['tile_type'] ?? 'room'
            );
        }

        return $this->boardManager->getValidPlacementPositions($tile);
    }
    
    /**
     * Jumps to a specific game state for debugging.
     *
     * @param int $state The ID of the state to jump to.
     */
    public function debug_goToState(int $state = 3) {
        $this->gamestate->jumpToState($state);
    }

    // --- Manager Getters for IDE and visibility ---
    public function getBoardManager(): BoardManager
    {
        return $this->boardManager;
    }

    public function getRoomTilesManager(): RoomTilesManager
    {
        return $this->roomTilesManager;
    }

    /**
     * Helper used by states to fetch cached pirate data without hitting the DB.
     */
    public function requirePirate(int $playerId): PlayerModel
    {
        $pirate = $this->pirateManager->getPirate($playerId);
        if ($pirate === null) {
            throw new \BgaSystemException("Unable to locate pirate {$playerId} in cache.");
        }

        return $pirate;
    }

    public function getPirateManager(): PirateManager
    {
        return $this->pirateManager;
    }

    public function getItemManager(): ItemManager
    {
        return $this->itemManager;
    }

    public function getTokenManager(): TokenManager
    {
        return $this->tokenManager;
    }

    public function getPlayerDBManager(): PlayerDBManager
    {
        return $this->playerDBManager;
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
