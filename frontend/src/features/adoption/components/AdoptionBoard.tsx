"use client";

import { Loader2, RefreshCw } from "lucide-react";
import { useRouter } from "next/navigation";
import { useCallback, useEffect, useRef } from "react";
import { toast } from "sonner";

import { Button } from "@/components/ui/button";
import { adoptionService } from "@/features/adoption/services/adoptionService";
import { adoptionHref } from "@/features/adoption/utils/adoptionParams";
import { MatchEmpty } from "@/features/match/components/MatchEmpty";
import { PetCardFace } from "@/features/match/components/PetCardFace";
import { SwipeActions } from "@/features/match/components/SwipeActions";
import { SwipeCard, type SwipeCardHandle } from "@/features/match/components/SwipeCard";
import { useSwipeDeck } from "@/features/match/hooks/useSwipeDeck";
import type { FeedPet, MatchFilters, SwipeAction, SwipeResult } from "@/features/match/types/matchTypes";

interface AdoptionBoardProps {
  initial: FeedPet[];
  filters: MatchFilters;
}

const KEY_ACTIONS: Record<string, SwipeAction> = { ArrowRight: "like", ArrowLeft: "dislike", ArrowUp: "superlike" };

function isTypingTarget(target: EventTarget | null): boolean {
  return target instanceof HTMLElement && (target.isContentEditable || ["INPUT", "TEXTAREA", "SELECT"].includes(target.tagName) || target.closest('[role="dialog"], [role="listbox"]') !== null);
}

export function AdoptionBoard({ initial, filters }: AdoptionBoardProps) {
  const router = useRouter();
  const cardRef = useRef<SwipeCardHandle>(null);

  const loadFeed = useCallback(() => adoptionService.feed(filters), [filters]);
  const onError = useCallback((message: string) => toast.error(message), []);
  const sendSwipe = useCallback(
    async (petUuid: string, action: SwipeAction): Promise<SwipeResult> => {
      const result = await adoptionService.swipe(petUuid, action);

      if (result.adoption) {
        const uuid = result.adoption.uuid;
        toast.success("Interesse enviado!", { description: `Avisamos o responsável por ${result.adoption.pet.name}.`, action: { label: "Ver", onClick: () => router.push(adoptionHref(uuid)) } });
      }

      return { swipe: { action, target_pet_uuid: petUuid }, matched: false, match: null };
    },
    [router],
  );

  const { deck, isRefilling, isFinished, swipe, refill } = useSwipeDeck({ initial, loadFeed, sendSwipe, onMatch: () => undefined, onError });

  const top = deck[0];
  const next = deck[1];

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
            <SwipeCard key={top.uuid} ref={cardRef} pet={top} onSwiped={(action) => void swipe(top, action)} />
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
      <p className="hidden text-center text-xs text-muted-foreground sm:block">Curtir ou super curtir envia seu interesse de adoção. ← passar, → curtir, ↑ super curtir.</p>
    </div>
  );
}
