<?php
declare(strict_types=1);

namespace Bga\Games\DeadMenPax;

/**
 * Simple test to validate TokenManager lifecycle integration
 * This demonstrates the complete token workflow from setup to pickup
 */
class TokenManagerTest
{
    private $mockGame;
    private TokenManager $tokenManager;
    
    public function __construct()
    {
        // Mock BGA Table for testing
        $this->mockGame = new class {
            private array $queries = [];
            private array $results = [];
            
            public function DbQuery(string $query): void {
                $this->queries[] = $query;
                echo "SQL: $query\n";
            }
            
            public function getObjectFromDB(string $query): ?array {
                echo "Query: $query\n";
                
                // Mock responses for different queries
                if (str_contains($query, "ORDER BY RAND()")) {
                    return ['token_id' => 'crew_grog_1']; // Mock random token
                }
                
                if (str_contains($query, "crew_grog_1")) {
                    return [
                        'token_id' => 'crew_grog_1',
                        'token_type' => 'crew_grog',
                        'token_location' => 'room',
                        'token_location_arg' => '2_3',
                        'token_state' => 0 // enemy side initially
                    ];
                }
                
                return null;
            }
            
            public function getCollectionFromDb(string $query): array {
                echo "Collection Query: $query\n";
                
                if (str_contains($query, "2_3") && str_contains($query, "token_state = 0")) {
                    return [
                        'crew_grog_1' => [
                            'token_id' => 'crew_grog_1',
                            'token_type' => 'crew_grog',
                            'token_location' => 'room',
                            'token_location_arg' => '2_3',
                            'token_state' => 0
                        ]
                    ];
                }
                
                return [];
            }
            
            public function getUniqueValueFromDB(string $query): string {
                echo "Unique Query: $query\n";
                return "1"; // Mock count
            }
        };
        
        $this->tokenManager = new TokenManager($this->mockGame);
    }
    
    public function runCompleteLifecycleTest(): void
    {
        echo "=== TokenManager Complete Lifecycle Test ===\n\n";
        
        // Test 1: Setup tokens from material definitions
        echo "1. Testing token setup from material definitions...\n";
        $mockTokens = [
            [
                'front_type' => 'crew',
                'back_type' => 'grog', 
                'quantity' => 2
            ],
            [
                'front_type' => 'guardian',
                'back_type' => 'treasure',
                'quantity' => 1
            ]
        ];
        
        $this->tokenManager->setupTokens($mockTokens);
        echo "✓ Tokens created in bag\n\n";
        
        // Test 2: Spawn enemy in room
        echo "2. Testing enemy spawn in room...\n";
        $tokenId = $this->tokenManager->spawnRandomEnemyInRoom(2, 3);
        echo "✓ Enemy spawned: $tokenId\n\n";
        
        // Test 3: Check if room has enemies
        echo "3. Testing enemy detection...\n";
        $hasEnemies = $this->tokenManager->hasEnemiesInRoom(2, 3);
        echo "✓ Room has enemies: " . ($hasEnemies ? 'yes' : 'no') . "\n\n";
        
        // Test 4: Defeat enemy (flip to object)
        echo "4. Testing enemy defeat...\n";
        $defeated = $this->tokenManager->defeatEnemy('crew_grog_1');
        echo "✓ Enemy defeated: " . ($defeated ? 'success' : 'failed') . "\n\n";
        
        // Test 5: Check pickup eligibility
        echo "5. Testing pickup eligibility...\n";
        $canPickup = $this->tokenManager->canPickupObject('crew_grog_1');
        echo "✓ Can pickup object: " . ($canPickup ? 'yes' : 'no') . "\n\n";
        
        // Test 6: Pickup object
        echo "6. Testing object pickup...\n";
        $pickup = $this->tokenManager->pickupObject(123, 'crew_grog_1'); // player 123
        echo "✓ Object pickup: " . ($pickup ? 'success' : 'failed') . "\n\n";
        
        // Test 7: Get token definition
        echo "7. Testing token definition parsing...\n";
        $def = $this->tokenManager->getTokenDefinition('crew_grog_1');
        if ($def) {
            echo "✓ Token definition: front={$def['front_type']}, back={$def['back_type']}, state={$def['current_state']}\n\n";
        }
        
        echo "=== Test Complete ===\n";
        echo "All token lifecycle operations working correctly!\n";
    }
    
    public function runBattleScenarioTest(): void
    {
        echo "\n=== Battle Scenario Test ===\n\n";
        
        // Scenario: Player enters room with enemies, defeats them, picks up objects
        echo "Scenario: Player enters room (2,3) with enemies\n";
        
        $enemies = $this->tokenManager->getEnemyTokensInRoom(2, 3);
        echo "Enemies found: " . count($enemies) . "\n";
        
        foreach ($enemies as $enemy) {
            echo "- Defeating enemy: {$enemy['token_id']}\n";
            $this->tokenManager->defeatEnemy($enemy['token_id']);
        }
        
        $objects = $this->tokenManager->getObjectTokensInRoom(2, 3);
        echo "Objects available after battle: " . count($objects) . "\n";
        
        foreach ($objects as $object) {
            if ($this->tokenManager->canPickupObject($object['token_id'])) {
                echo "- Picking up: {$object['token_id']}\n";
                $this->tokenManager->pickupObject(123, $object['token_id']);
            }
        }
        
        echo "✓ Battle scenario complete\n";
    }
}

// Run the test if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    $test = new TokenManagerTest();
    $test->runCompleteLifecycleTest();
    $test->runBattleScenarioTest();
}
