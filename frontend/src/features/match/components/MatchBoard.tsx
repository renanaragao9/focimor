"use client";

import { Loader2, RefreshCw } from "lucide-react";
import { useCallback, useEffect, useRef, useState } from "react";
import { toast } from "sonner";

import { Button } from "@/components/ui/button";
import { MatchDialog } from "@/features/match/components/MatchDialog";
import { MatchEmpty } from "@/features/match/components/MatchEmpty";
import { PetCardFace } from "@/features/match/components/PetCardFace";
import { SwipeActions } from "@/features/match/components/SwipeActions";
import { SwipeCard, type SwipeCardHandle } from "@/features/match/components/SwipeCard";
import { useSwipeDeck } from "@/features/match/hooks/useSwipeDeck";
import { matchService } from "@/features/match/services/matchService";
import type { FeedPet, MatchFilters, MatchInfo, SwipeAction } from "@/features/match/types/matchTypes";
import type { PetPhoto } from "@/features/pet/types/petTypes";

interface MatchBoardProps {
  petUuid: string;
  petPhotos: PetPhoto[];
  initial: FeedPet[];
  filters: MatchFilters;
}

const KEY_ACTIONS: Record<string, SwipeAction> = { ArrowRight: "like", ArrowLeft: "dislike", ArrowUp: "superlike" };

function isTypingTarget(target: EventTarget | null): boolean {
  return target instanceof HTMLElement && (target.isContentEditable || ["INPUT", "TEXTAREA", "SELECT"].includes(target.tagName) || target.closest('[role="dialog"], [role="listbox"]') !== null);
}

export function MatchBoard({ petUuid, petPhotos, initial, filters }: MatchBoardProps) {
  const cardRef = useRef<SwipeCardHandle>(null);
  const [match, setMatch] = useState<MatchInfo | null>(null);

  const loadFeed = useCallback(() => matchService.feed(petUuid, filters), [petUuid, filters]);
  const sendSwipe = useCallback((targetUuid: string, action: SwipeAction) => matchService.swipe(petUuid, targetUuid, action), [petUuid]);
  const onError = useCallback((message: string) => toast.error(message), []);

  const { deck, isRefilling, isFinished, swipe, refill } = useSwipeDeck({ initial, loadFeed, sendSwipe, onMatch: setMatch, onError });

  const top = deck[0];
  const next = deck[1];

  function handleSwiped(action: SwipeAction): void {
    if (top) void swipe(top, action);
  }

  useEffect(() => {
    function handleKey(event: KeyboardEvent): void {
      const action = KEY_ACTIONS[event.key];
      if (!action || event.altKey || event.ctrlKey || event.metaKey || isTypingTarget(event.target)) return;

      event.preventDefault();
      cardRef.current?.swipe(action);
    }

    window.addEventListener("keydown", handleKey);
    return () => window.removeEventListener("keydown", handleKey);
  }, []);

  if (isFinished) {
    return (
      <MatchEmpty kind="finished">
        <Button variant="outline" className="cursor-pointer" onClick={() => void refill()}>
          <RefreshCw /> Procurar de novo
        </Button>
      </MatchEmpty>
    );
  }

  return (
    <div className="mx-auto flex w-full max-w-md flex-col gap-5">
      <div className="relative h-[min(66dvh,36rem)] w-full lg:h-[min(52dvh,33rem)]">
        {top ? (
          <>
            {next && (
              <div className="absolute inset-0 scale-95 opacity-80" aria-hidden="true">
                <PetCardFace pet={next} />
              </div>
            )}
            <SwipeCard key={top.uuid} ref={cardRef} pet={top} onSwiped={handleSwiped} />
          </>
        ) : (
          <div className="flex size-full flex-col items-center justify-center gap-3 rounded-2xl border border-dashed text-muted-foreground" role="status">
            {isRefilling ? <Loader2 className="size-8 animate-spin" /> : <RefreshCw className="size-8" />}
            <p>{isRefilling ? "Procurando pets..." : "Nada por enquanto."}</p>
            {!isRefilling && (
              <Button variant="outline" className="cursor-pointer" onClick={() => void refill()}>
                Tentar de novo
              </Button>
            )}
          </div>
        )}
      </div>

      <SwipeActions disabled={!top} onAction={(action) => cardRef.current?.swipe(action)} />
      <p className="hidden text-center text-xs text-muted-foreground sm:block">
        Arraste o cartão ou use as setas do teclado: ← passar, → curtir, ↑ super curtir. Toque nas laterais da foto para ver as outras.
      </p>

      <MatchDialog match={match} myPhotos={petPhotos} onClose={() => setMatch(null)} />
    </div>
  );
}
