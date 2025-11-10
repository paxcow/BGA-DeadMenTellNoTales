export interface BattleResult {
  outcome: "win" | "loss";
  roll: number;
  modifiers: { battleTrack: number; item: number };
  totalStrength: number;
  fatigueGained?: number;
}

export class BattleInterface {
  constructor(private game: any) {}

  showBattle(
    enemyId: number,
    enemyType: string,
    strength: number,
    canRetreat: boolean
  ): void {
    // TODO: render battle UI panel with enemy info and modifiers
  }

  updatePrediction(useBattleTrack: boolean, useItem: boolean): void {
    // TODO: calculate and display predicted strength
  }

  sendFightAction(useBattleTrack: boolean, useItem: boolean): void {
    // TODO: trigger fight via game.ajaxcall
  }

  animateBattleRoll(roll: number): Promise<void> {
    // TODO: animate die roll
    return Promise.resolve();
  }

  showResult(result: BattleResult): Promise<void> {
    // TODO: display battle outcome animations
    return Promise.resolve();
  }

  hideBattle(): void {
    // TODO: remove/hide battle UI panel
  }
}
