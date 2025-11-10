export class ActionManager {
  constructor(private game: any) {}

  sendAction(action: string, params: any): void {
    // Use BGA's performAction wrapper for server calls
    this.game.bgaPerformAction(action, params);
  }
}
