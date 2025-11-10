export interface ActionOption {
  name: string;
  cost: number;
  enabled: boolean;
}

export class ActionSelector {
  constructor(private game: any) {}

  initialize(options: ActionOption[]): void {
    // TODO: render action buttons based on options
  }

  updateAvailableActions(options: ActionOption[]): void {
    // TODO: enable/disable buttons
  }

  selectAction(actionName: string): void {
    // TODO: highlight selected action and notify game
  }

  highlightTargets(actionName: string, targets: any[]): void {
    // TODO: show target highlights on board
  }

  disableAll(): void {
    // TODO: disable all action buttons
  }
}
