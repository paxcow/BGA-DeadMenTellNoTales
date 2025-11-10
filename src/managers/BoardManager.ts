export interface TilePlacement {
  tileId: number;
  x: number;
  y: number;
  rotation: number;
}

export class BoardManager {
  private scrollmap: any;

  constructor(scrollmap: any) {
    this.scrollmap = scrollmap;
  }

  initialize(startingTiles: any[]): void {
    // TODO: set up initial tiles on scrollmap
  }

  placeTile(tile: TilePlacement): Promise<void> {
    // TODO: animate and place a new tile
    return Promise.resolve();
  }

  setFireLevel(tileId: number, level: number): Promise<void> {
    // TODO: update fire die rendering
    return Promise.resolve();
  }

  addToken(token: any, tileId: number): void {
    // TODO: render token on tile
  }

  movePirate(playerId: number, fromTileId: number, toTileId: number): Promise<void> {
    // TODO: animate pirate movement
    return Promise.resolve();
  }

  explodeRoom(tileId: number): Promise<void> {
    // TODO: handle room explosion visuals
    return Promise.resolve();
  }

  highlightRooms(tileIds: number[], color: string): void {
    // TODO: highlight tiles
  }

  clearHighlights(): void {
    // TODO: clear all highlights
  }
}
