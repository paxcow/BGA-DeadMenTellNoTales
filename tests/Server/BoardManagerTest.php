<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bga\Games\DeadMenPax\BoardManager;
use Bga\GameFramework\Table;
use Bga\Games\DeadMenPax\RoomTile;

final class BoardManagerTest extends TestCase
{
    private BoardManager $boardManager;

    protected function setUp(): void
    {
        // Create a mock Table for constructor dependency
        $tableMock = $this->createMock(Table::class);
        $this->boardManager = new BoardManager($tableMock);
    }

    public function testPlaceTileAndGetTileAt(): void
    {
        $tile = new RoomTile(1, RoomTile::DOOR_NORTH | RoomTile::DOOR_SOUTH, RoomTile::COLOR_RED, 0, false, false, true);
        $placed = $this->boardManager->placeTile($tile, 0, 0);
        $this->assertTrue($placed, 'Starting tile should be placed');
        $fetched = $this->boardManager->getTileAt(0, 0);
        $this->assertSame($tile, $fetched, 'getTileAt should return the placed tile');
    }

    public function testInvalidPlacementWhenOccupied(): void
    {
        $tileA = new RoomTile(2, RoomTile::DOOR_EAST, RoomTile::COLOR_BLUE, 0, false, false, true);
        $tileB = new RoomTile(3, RoomTile::DOOR_WEST, RoomTile::COLOR_GREEN, 0, false, false, false);
        // Place first tile
        $this->assertTrue($this->boardManager->placeTile($tileA, 1, 1));
        // Attempt to place second tile on same coordinates
        $this->assertFalse($this->boardManager->placeTile($tileB, 1, 1));
    }

    public function testFindPathSimple(): void
    {
        $start = new RoomTile(4, RoomTile::DOOR_EAST, RoomTile::COLOR_YELLOW, 0, false, false, true);
        $end   = new RoomTile(5, RoomTile::DOOR_WEST, RoomTile::COLOR_YELLOW, 0, false, false, false);
        // Place adjacent tiles
        $this->boardManager->placeTile($start, 0, 0);
        $this->boardManager->placeTile($end, 1, 0, RoomTile::ORIENTATION_0);
        $path = $this->boardManager->findPath($start, $end);
        $this->assertCount(2, $path, 'Path should contain 2 tiles');
        $this->assertSame($start, $path[0]);
        $this->assertSame($end, $path[1]);
    }
}
