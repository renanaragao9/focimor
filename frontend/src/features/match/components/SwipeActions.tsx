"use client";

import { Heart, Star, X } from "lucide-react";

import { Button } from "@/components/ui/button";
import type { SwipeAction } from "@/features/match/types/matchTypes";

interface SwipeActionsProps {
  disabled: boolean;
  onAction: (action: SwipeAction) => void;
}

export function SwipeActions({ disabled, onAction }: SwipeActionsProps) {
  return (
    <div className="flex items-center justify-center gap-5">
      <Button
        type="button"
        variant="outline"
        className="size-16 cursor-pointer rounded-full border-destructive/40 text-destructive hover:bg-destructive/10 hover:text-destructive"
        aria-label="Passar"
        title="Passar (seta para a esquerda)"
        disabled={disabled}
        onClick={() => onAction("dislike")}
      >
        <X className="size-8" />
      </Button>
      <Button
        type="button"
        variant="outline"
        className="size-12 cursor-pointer rounded-full border-info/40 text-info hover:bg-info/10 hover:text-info"
        aria-label="Super curtir"
        title="Super curtir (seta para cima)"
        disabled={disabled}
        onClick={() => onAction("superlike")}
      >
        <Star className="size-6" />
      </Button>
      <Button type="button" className="size-16 cursor-pointer rounded-full" aria-label="Curtir" title="Curtir (seta para a direita)" disabled={disabled} onClick={() => onAction("like")}>
        <Heart className="size-8" />
      </Button>
    </div>
  );
}
