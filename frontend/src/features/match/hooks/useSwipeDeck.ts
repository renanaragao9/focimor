"use client";

import { useCallback, useReducer, useRef } from "react";

import type { FeedPet, MatchInfo, SwipeAction, SwipeResult } from "@/features/match/types/matchTypes";
import { createDeck, deckReducer, isDeckFinished, needsRefill } from "@/features/match/utils/deck";
import { ApiError } from "@/lib/api/errors";
import type { ApiPage } from "@/lib/api/types";

const RETRY_DELAY_MS = 700;
const MAX_REFILL_ATTEMPTS = 5;

interface UseSwipeDeckOptions {
  initial: FeedPet[];
  loadFeed: () => Promise<ApiPage<FeedPet>>;
  sendSwipe: (petUuid: string, action: SwipeAction) => Promise<SwipeResult>;
  onMatch: (match: MatchInfo) => void;
  onError: (message: string) => void;
}

export function useSwipeDeck({ initial, loadFeed, sendSwipe, onMatch, onError }: UseSwipeDeckOptions) {
  const [state, dispatch] = useReducer(deckReducer, initial, createDeck);
  const inFlight = useRef(0);
  const seen = useRef(new Set(initial.map((pet) => pet.uuid)));

  const refill = useCallback(async (): Promise<void> => {
    dispatch({ type: "refill-started" });

    try {
      for (let attempt = 1; attempt <= MAX_REFILL_ATTEMPTS; attempt++) {
        const page = await loadFeed();
        const fresh = page.data.filter((pet) => !seen.current.has(pet.uuid));
        fresh.forEach((pet) => seen.current.add(pet.uuid));

        const waitingForSwipes = fresh.length === 0 && inFlight.current > 0 && attempt < MAX_REFILL_ATTEMPTS;
        if (!waitingForSwipes) {
          dispatch({ type: "refill-finished", items: page.data, inFlight: inFlight.current });
          return;
        }

        await new Promise((resolve) => window.setTimeout(resolve, RETRY_DELAY_MS));
      }
    } catch {
      dispatch({ type: "refill-failed" });
    }
  }, [loadFeed]);

  const swipe = useCallback(
    async (pet: FeedPet, action: SwipeAction): Promise<void> => {
      const next = deckReducer(state, { type: "swiped", pet });

      dispatch({ type: "swiped", pet });
      inFlight.current += 1;
      if (needsRefill(next)) void refill();

      try {
        const result = await sendSwipe(pet.uuid, action);
        if (result.matched && result.match) onMatch(result.match);
      } catch (caught) {
        const alreadyHandled = caught instanceof ApiError && caught.isValidation;
        if (!alreadyHandled) dispatch({ type: "restore", pet });
        onError(caught instanceof ApiError ? caught.message : "Não foi possível registrar. Tente de novo.");
      } finally {
        inFlight.current -= 1;
      }
    },
    [state, refill, sendSwipe, onMatch, onError],
  );

  return { deck: state.deck, isRefilling: state.isRefilling, isFinished: isDeckFinished(state), swipe, refill };
}
