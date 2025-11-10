export interface PlayerStats {
  playerId: number;
  fatigue: number;
  battleTrack: number;
  actionTokens: number;
  carriedTokens: string[];
  characterCardId: number;
  itemCardId: number;
}

export class PlayerPanelManager {
  constructor(private game: any) {}

  initialize(players: PlayerStats[]): void {
    players.forEach((stats) => this.createPanel(stats));
  }

  private createPanel(stats: PlayerStats): void {
    // TODO: inject HTML into BGA player board
    // - Render character & item slots
    // - Render fatigue meter
    // - Render battle track
    // - Render action tokens
    // - Render carried token icons
  }

  updateFatigue(playerId: number, newFatigue: number): void {
    // TODO: update fatigue meter display
  }

  updateBattleTrack(playerId: number, position: number): void {
    // TODO: update battle track UI
  }

  updateActionTokens(playerId: number, remaining: number): void {
    // TODO: update action token icons
  }

  updateCarriedTokens(playerId: number, tokens: string[]): void {
    // TODO: refresh carried token icons
  }
}
