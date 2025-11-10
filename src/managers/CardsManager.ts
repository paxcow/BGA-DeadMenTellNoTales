import { Manager as BgaCards } from "bga-cards";
import { Manager as BgaAnimations } from "bga-animations";

export class CardsManager {
  private cardsManager: any;
  private animationManager: any;

  constructor(animationManager: any) {
    this.animationManager = animationManager;
    this.cardsManager = new BgaCards.Manager({
      animationManager: this.animationManager,
      // TODO: set up type, getId, setupFrontDiv/backDiv per card type
    });
  }

  initializeCharacterCards(containerId: string, cards: any[]): void {
    // TODO: create stock for character cards and add initial cards
  }

  initializeItemCards(containerId: string, cards: any[]): void {
    // TODO: create stock for item cards and add initial cards
  }

  revealSkelitCard(card: any): Promise<void> {
    // TODO: animate Skelit's Revenge card reveal
    return Promise.resolve();
  }

  swapItemCard(playerId: number, oldCardId: number, newCardId: number): Promise<void> {
    // TODO: handle item swap animation
    return Promise.resolve();
  }
}
