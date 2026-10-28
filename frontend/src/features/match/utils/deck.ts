import { REFILL_THRESHOLD, type FeedPet } from "@/features/match/types/matchTypes";

export interface DeckState {
  deck: FeedPet[];
  seen: string[];
  isRefilling: boolean;
  exhausted: boolean;
}

export type DeckAction =
  { type: "swiped"; pet: FeedPet } | { type: "restore"; pet: FeedPet } | { type: "refill-started" } | { type: "refill-finished"; items: FeedPet[]; inFlight: number } | { type: "refill-failed" };

export function createDeck(initial: FeedPet[]): DeckState {
  return { deck: initial, seen: initial.map((pet) => pet.uuid), isRefilling: false, exhausted: false };
}

export function deckReducer(state: DeckState, action: DeckAction): DeckState {
  switch (action.type) {
    case "swiped":
      return { ...state, deck: state.deck.filter((pet) => pet.uuid !== action.pet.uuid) };

    case "restore":
      return { ...state, deck: [action.pet, ...state.deck.filter((pet) => pet.uuid !== action.pet.uuid)] };

    case "refill-started":
      return { ...state, isRefilling: true };

    case "refill-finished": {
      const known = new Set(state.seen);
      const fresh = action.items.filter((pet, index, list) => !known.has(pet.uuid) && list.findIndex((other) => other.uuid === pet.uuid) === index);

      return {
        deck: [...state.deck, ...fresh],
        seen: [...state.seen, ...fresh.map((pet) => pet.uuid)],
        isRefilling: false,
        exhausted: fresh.length === 0 && action.inFlight === 0,
      };
    }

    case "refill-failed":
      return { ...state, isRefilling: false };
  }
}

export function needsRefill(state: DeckState): boolean {
  return !state.isRefilling && !state.exhausted && state.deck.length <= REFILL_THRESHOLD;
}

export function isDeckFinished(state: DeckState): boolean {
  return state.deck.length === 0 && state.exhausted;
}
