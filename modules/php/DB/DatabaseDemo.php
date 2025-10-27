<?php

namespace Bga\Games\DeadMenPax\DB;

use Bga\Games\DeadMenPax\DB\Models\RoomTileModel;
use Bga\Games\DeadMenPax\DB\Models\CardModel;
use Bga\Games\DeadMenPax\DB\Models\TokenModel;

/**
 * Demonstration of the new database infrastructure
 * This file shows how to use the DBManager and model classes
 */
class DatabaseDemo
{
    /**
     * Example of creating and saving a room tile using the new infrastructure
     */
    public function exampleRoomTileOperations($game): void
    {
        // Create DBManager for room tiles
        $roomTileDB = new DBManager('room_tile', RoomTileModel::class, $game);
        
        // Create a new room tile model
        $tile = new RoomTileModel();
        $tile->setTileId(1);
        $tile->setTileType('cabin');
        $tile->setPosition(0, 0);
        $tile->setColor('red');
        $tile->setPips(3);
        $tile->setHasPowderKeg(true);
        $tile->setIsStartingTile(true);
        
        // Save to database - automatically handles INSERT/UPDATE
        $tileId = $roomTileDB->saveObjectToDB($tile);
        echo "Saved room tile with ID: $tileId\n";
        
        // Retrieve from database
        $retrievedTile = $roomTileDB->createObjectFromDB($tileId);
        if ($retrievedTile) {
            echo "Retrieved tile at position: {$retrievedTile->getX()}, {$retrievedTile->getY()}\n";
        }
        
        // Get all tiles
        $allTiles = $roomTileDB->getAllObjects();
        echo "Total tiles in database: " . count($allTiles) . "\n";
        
        // Update tile (increase fire level)
        $tile->increaseFireLevel(2);
        $roomTileDB->saveObjectToDB($tile);
        echo "Updated tile fire level to: {$tile->getFireLevel()}\n";
    }
    
    /**
     * Example of card operations using the new infrastructure
     */
    public function exampleCardOperations($game): void
    {
        // Create DBManager for cards
        $cardDB = new DBManager('card', CardModel::class, $game);
        
        // Create a character card
        $characterCard = new CardModel();
        $characterCard->setCardId(1);
        $characterCard->setCardType('character');
        $characterCard->setCardTypeArg(1);
        $characterCard->setCardLocation('player');
        $characterCard->setCardLocationArg(12345); // Player ID
        
        // Save card
        $cardId = $cardDB->saveObjectToDB($characterCard);
        echo "Saved character card with ID: $cardId\n";
        
        // Move card to table
        $characterCard->moveToTable();
        $cardDB->saveObjectToDB($characterCard);
        echo "Moved card to table\n";
        
        // Create an item card
        $itemCard = new CardModel();
        $itemCard->setCardId(2);
        $itemCard->setCardType('item');
        $itemCard->setCardTypeArg(3);
        $itemCard->moveToDeck();
        
        $cardDB->saveObjectToDB($itemCard);
        echo "Created item card in deck\n";
    }
    
    /**
     * Example of token operations using the new infrastructure
     */
    public function exampleTokenOperations($game): void
    {
        // Create DBManager for tokens
        $tokenDB = new DBManager('token', TokenModel::class, $game);
        
        // Create a treasure token
        $token = new TokenModel();
        $token->setTokenId('treasure_1');
        $token->setTokenType('treasure');
        $token->moveToRoom(2, 3);
        $token->setTokenState(0);
        
        // Save token
        $tokenId = $tokenDB->saveObjectToDB($token);
        echo "Saved token with ID: $tokenId\n";
        
        // Check token location
        $coordinates = $token->getRoomCoordinates();
        if ($coordinates) {
            echo "Token is in room at: {$coordinates[0]}, {$coordinates[1]}\n";
        }
        
        // Move token to supply
        $token->moveToSupply();
        $tokenDB->saveObjectToDB($token);
        echo "Moved token to supply\n";
    }
    
    /**
     * Comparison: Old way vs New way
     */
    public function comparisonExample($game): void
    {
        echo "\n=== COMPARISON: OLD WAY vs NEW WAY ===\n";
        
        echo "\nOLD WAY (Raw SQL):\n";
        echo "// Raw SQL with security vulnerabilities\n";
        echo "\$sql = \"INSERT INTO room_tile (x, y, fire_level) VALUES (\$x, \$y, \$fireLevel)\";\n";
        echo "\$game->DbQuery(\$sql); // Potential SQL injection\n";
        echo "\$result = \$game->getObjectListFromDB(\"SELECT * FROM room_tile WHERE x = \$x\");\n";
        
        echo "\nNEW WAY (Type-safe with DBManager):\n";
        echo "// Type-safe, secure, object-oriented\n";
        echo "\$roomTileDB = new DBManager('room_tile', RoomTileModel::class, \$game);\n";
        echo "\$tile = new RoomTileModel();\n";
        echo "\$tile->setPosition(\$x, \$y);\n";
        echo "\$tile->setFireLevel(\$fireLevel);\n";
        echo "\$tileId = \$roomTileDB->saveObjectToDB(\$tile); // Automatic escaping\n";
        echo "\$retrievedTile = \$roomTileDB->createObjectFromDB(\$tileId); // Returns typed object\n";
        
        echo "\nBENEFITS:\n";
        echo "✅ No SQL injection vulnerabilities\n";
        echo "✅ Type safety with IDE autocomplete\n";
        echo "✅ Automatic INSERT/UPDATE detection\n";
        echo "✅ Object hydration from database rows\n";
        echo "✅ Consistent database patterns\n";
        echo "✅ Easy testing with mock objects\n";
        echo "✅ Clear separation of concerns\n";
    }
}
