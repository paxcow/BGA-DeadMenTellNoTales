import declare from "dojo/_base/declare";
import gamegui from "ebg/core/gamegui";
import counter from "ebg/counter";
import "ebg/scrollmap";
import { Manager as BgaAnimations } from "bga-animations";
import { Manager as BgaCards } from "bga-cards";

export default declare("bgagame.deadmenpax", [gamegui], {
  constructor() {
    this.animationManager = null;
    this.cardsManager = null;
    this.scrollmap = null;
  },

  setup(gamedatas: any): void {
    this.setupManagers(gamedatas);
    this.setupNotifications();
  },

  setupManagers(gamedatas: any): void {
    // Initialize animation manager
    this.animationManager = new BgaAnimations({
      animationsActive: () => this.bgaAnimationsActive(),
    });

    // TODO: initialize scrollmap, cardsManager, other managers
  },

  setupNotifications(): void {
    // TODO: subscribe to all notifications
  },

  onEnteringState(stateName: string, args: any): void {
    console.log("Entering state:", stateName, args);
    // TODO: handle UI updates per state
  },

  onLeavingState(stateName: string): void {
    console.log("Leaving state:", stateName);
    // TODO: cleanup per state
  },
});
