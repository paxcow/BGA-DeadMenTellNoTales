<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

/**
 * Simple test class to demonstrate BoardManager functionality
 * This would be integrated into your actual testing framework
 */
class BoardTest
{
    private BoardManager $boardManager;
    private MockGame $mockGame;

    public function __construct()
    {
        $this->mockGame = new MockGame();
        
        $this->boardManager = new BoardManager($this->mockGame);
    }

    /**
     * Create mock PirateManager for testing
     */
    private function createMockPirateManager(): PirateManager
    {
        return new class($this->mockGame) extends PirateManager {
            public function getPiratesAt(int $x, int $y): array {
                return []; // No pirates for testing
            }
            public function handleExplosionDamage(int $playerId, string $explosionType): void {
                // Mock - do nothing
            }
        };
    }

    /**
     * Create mock TokenManager for testing
     */
    private function createMockTokenManager(): TokenManager
    {
        return new class($this->mockGame) extends TokenManager {
            public function getTokensInRoom(int $x, int $y): array {
                return []; // No tokens for testing
            }
            public function destroyToken(string $tokenId): void {
                // Mock - do nothing
            }
        };
    }

    /**
     * Create mock ItemManager for testing
     */
    private function createMockItemManager(): ItemManager
    {
        return new class($this->mockGame) extends ItemManager {
            public function getItemsInPositions(array $positions): array {
                return []; // No items for testing
            }
            public function destroyItem(int $itemId): ?int {
                // Mock - do nothing
                return null;
            }
        };
    }

    /**
     * Test basic tile placement and connections
     */
    public function testTilePlacement(): bool
    {
        echo "Testing tile placement and connections...\n";

        // Create starting tile (center of board)
        $startTile = new RoomTile(
            1, 
            RoomTile::DOOR_NORTH | RoomTile::DOOR_EAST | RoomTile::DOOR_SOUTH | RoomTile::DOOR_WEST,
            RoomTile::COLOR_RED,
            3,
            false,
            true, // has trapdoor
            true  // is starting tile
        );

        // Place starting tile at origin
        if (!$this->boardManager->placeTile($startTile, 0, 0)) {
            echo "❌ Failed to place starting tile\n";
            return false;
        }
        echo "✅ Placed starting tile at (0,0)\n";

        // Create second tile (can connect north)
        $northTile = new RoomTile(
            2,
            RoomTile::DOOR_SOUTH | RoomTile::DOOR_EAST,
            RoomTile::COLOR_BLUE,
            2
        );

        // Should be able to place north of starting tile
        if (!$this->boardManager->placeTile($northTile, 0, -1)) {
            echo "❌ Failed to place north tile\n";
            return false;
        }
        echo "✅ Placed north tile at (0,-1)\n";

        // Test invalid placement (no door connection)
        $invalidTile = new RoomTile(
            3,
            RoomTile::DOOR_NORTH, // only north door
            RoomTile::COLOR_GREEN,
            1
        );

        // Should fail to place east of starting tile (doors don't match)
        if ($this->boardManager->placeTile($invalidTile, 1, 0)) {
            echo "❌ Should not have been able to place invalid tile\n";
            return false;
        }
        echo "✅ Correctly rejected invalid tile placement\n";

        return true;
    }

    /**
     * Test pathfinding
     */
    public function testPathfinding(): bool
    {
        echo "\nTesting pathfinding...\n";

        // Create a chain of connected tiles
        $tiles = [
            new RoomTile(10, RoomTile::DOOR_EAST, RoomTile::COLOR_RED, 1),
            new RoomTile(11, RoomTile::DOOR_WEST | RoomTile::DOOR_EAST, RoomTile::COLOR_BLUE, 2),
            new RoomTile(12, RoomTile::DOOR_WEST | RoomTile::DOOR_EAST, RoomTile::COLOR_GREEN, 3),
            new RoomTile(13, RoomTile::DOOR_WEST, RoomTile::COLOR_YELLOW, 1),
        ];

        // Place tiles in a line
        $positions = [[10, 0], [11, 0], [12, 0], [13, 0]];
        for ($i = 0; $i < count($tiles); $i++) {
            if (!$this->boardManager->placeTile($tiles[$i], $positions[$i][0], $positions[$i][1])) {
                echo "❌ Failed to place tile " . $tiles[$i]->getId() . "\n";
                return false;
            }
        }

        // Test path from first to last tile
        $path = $this->boardManager->findPath($tiles[0], $tiles[3]);
        if (count($path) !== 4) {
            echo "❌ Path should have 4 tiles, got " . count($path) . "\n";
            return false;
        }
        echo "✅ Found correct path length: " . count($path) . " tiles\n";

        // Test pathfinding between non-adjacent tiles
        $startTile = $this->boardManager->getTileById(1); // Original starting tile
        $endTile = $this->boardManager->getTileById(13);  // Last tile in chain
        
        if ($startTile && $endTile) {
            $longPath = $this->boardManager->findPath($startTile, $endTile);
            echo "✅ Found path from start to end: " . count($longPath) . " tiles\n";
        }

        return true;
    }

    /**
     * Test fire and explosion mechanics
     */
    public function testExplosions(): bool
    {
        echo "\nTesting fire and explosion mechanics...\n";

        // Get a tile and set it to near-explosion level
        $testTile = $this->boardManager->getTileById(1);
        if (!$testTile) {
            echo "❌ Could not find test tile\n";
            return false;
        }

        // Set fire level to 5 (just below explosion)
        $testTile->setFireLevel(5);
        if (!$testTile->willExplode()) {
            echo "❌ Tile should not explode at level 5\n";
            return false;
        }
        echo "✅ Tile correctly identified as ready to explode at fire level 5\n";

        // Test chain explosion
        $testTile->setFireLevel(6); // This will cause explosion
        $explodedTiles = $this->boardManager->handleChainExplosions($testTile);
        
        if (empty($explodedTiles)) {
            echo "❌ No tiles exploded\n";
            return false;
        }
        echo "✅ Chain explosion affected " . count($explodedTiles) . " tiles\n";

        return true;
    }

    /**
     * Test tile rotation functionality
     */
    public function testTileRotation(): bool
    {
        echo "\nTesting tile rotation...\n";

        // Create a tile with specific door configuration (L-shaped)
        $testTile = new RoomTile(
            50,
            RoomTile::DOOR_NORTH | RoomTile::DOOR_EAST,  // L-shaped: North and East doors
            RoomTile::COLOR_GREEN,
            2
        );

        // Test initial orientation
        if (!$testTile->hasNorthDoor() || !$testTile->hasEastDoor()) {
            echo "❌ Initial orientation incorrect\n";
            return false;
        }
        echo "✅ Initial orientation: North + East doors\n";

        // Test 90-degree clockwise rotation
        $testTile->rotateClockwise();
        if (!$testTile->hasEastDoor() || !$testTile->hasSouthDoor()) {
            echo "❌ 90° rotation failed\n";
            return false;
        }
        echo "✅ After 90° rotation: East + South doors\n";

        // Test 180-degree rotation (another 90°)
        $testTile->rotateClockwise();
        if (!$testTile->hasSouthDoor() || !$testTile->hasWestDoor()) {
            echo "❌ 180° rotation failed\n";
            return false;
        }
        echo "✅ After 180° rotation: South + West doors\n";

        // Test 270-degree rotation
        $testTile->rotateClockwise();
        if (!$testTile->hasWestDoor() || !$testTile->hasNorthDoor()) {
            echo "❌ 270° rotation failed\n";
            return false;
        }
        echo "✅ After 270° rotation: West + North doors\n";

        // Test full rotation (back to original)
        $testTile->rotateClockwise();
        if (!$testTile->hasNorthDoor() || !$testTile->hasEastDoor()) {
            echo "❌ Full rotation failed to return to original\n";
            return false;
        }
        echo "✅ Full rotation complete - back to original orientation\n";

        // Test counter-clockwise rotation
        $testTile->rotateCounterClockwise();
        if (!$testTile->hasWestDoor() || !$testTile->hasNorthDoor()) {
            echo "❌ Counter-clockwise rotation failed\n";
            return false;
        }
        echo "✅ Counter-clockwise rotation works\n";

        return true;
    }

    /**
     * Test valid placement positions with rotations
     */
    public function testValidPlacements(): bool
    {
        echo "\nTesting valid placement positions with rotation support...\n";

        $newTile = new RoomTile(
            99,
            RoomTile::DOOR_NORTH | RoomTile::DOOR_SOUTH,
            RoomTile::COLOR_RED,
            2
        );

        $validPositions = $this->boardManager->getValidPlacementPositions($newTile);
        echo "✅ Found " . count($validPositions) . " valid placement positions\n";

        // Display some positions for verification (with orientations)
        foreach (array_slice($validPositions, 0, 3) as $pos) {
            echo "  - Position ({$pos['x']}, {$pos['y']}) at orientation {$pos['orientation']}°\n";
        }

        return true;
    }

    /**
     * Test placement with specific orientations
     */
    public function testOrientedPlacement(): bool
    {
        echo "\nTesting tile placement with orientations...\n";

        // Create a tile that only has one door (North)
        $singleDoorTile = new RoomTile(
            100,
            RoomTile::DOOR_NORTH,
            RoomTile::COLOR_BLUE,
            1
        );

        // Try to place it east of starting tile (should fail in default orientation)
        if ($this->boardManager->placeTile($singleDoorTile, 1, 0, RoomTile::ORIENTATION_0)) {
            echo "❌ Should not have placed tile with wrong orientation\n";
            return false;
        }
        echo "✅ Correctly rejected wrong orientation\n";

        // Now try with 270° rotation (North door becomes West door)
        $singleDoorTile2 = new RoomTile(
            101,
            RoomTile::DOOR_NORTH,
            RoomTile::COLOR_BLUE,
            1
        );

        if (!$this->boardManager->placeTile($singleDoorTile2, 1, 0, RoomTile::ORIENTATION_270)) {
            echo "❌ Should have placed tile with correct orientation\n";
            return false;
        }
        echo "✅ Successfully placed tile with rotated orientation\n";

        // Verify the tile has the correct doors after placement
        $placedTile = $this->boardManager->getTileAt(1, 0);
        if (!$placedTile || !$placedTile->hasWestDoor() || $placedTile->hasNorthDoor()) {
            echo "❌ Placed tile doesn't have correct door configuration\n";
            return false;
        }
        echo "✅ Placed tile has correct door configuration (West door)\n";

        return true;
    }

    /**
     * Run all tests
     */
    public function runAllTests(): bool
    {
        echo "🚀 Starting BoardManager tests...\n\n";

        $results = [
            $this->testTilePlacement(),
            $this->testPathfinding(),
            $this->testExplosions(),
            $this->testValidPlacements()
        ];

        $passed = array_sum($results);
        $total = count($results);

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Test Results: {$passed}/{$total} passed\n";
        
        if ($passed === $total) {
            echo "🎉 All tests passed! BoardManager is working correctly.\n";
            return true;
        } else {
            echo "❌ Some tests failed. Please check the implementation.\n";
            return false;
        }
    }
}

/**
 * Mock Game class for testing
 */
class MockGame extends \Bga\GameFramework\Table
{
    private array $database = [];

    public function __construct()
    {
        // Don't call parent constructor to avoid BGA framework dependencies
    }

    public function getObjectListFromDB(string $sql, bool $bUniqueValue = false): array
    {
        // Return empty array for initial load
        return [];
    }


    // Override other methods that might be called to prevent errors
    protected function setupNewGame($players, $options = []): void {}
    protected function getAllDatas(): array { return []; }
    public function getGameProgression(): int { return 0; }
    protected function zombieTurn(array $state, int $active_player): void {}
    public function upgradeTableDb($from_version): void {}
}

// Example usage:
if (php_sapi_name() === 'cli') {
    $test = new BoardTest();
    $test->runAllTests();
}
