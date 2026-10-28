"use client";

import { Ban, Check, HeartHandshake, X } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { toast } from "sonner";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Button } from "@/components/ui/button";
import { adoptionService } from "@/features/adoption/services/adoptionService";
import type { Adoption } from "@/features/adoption/types/adoptionTypes";
import { availableActions, type AdoptionAction } from "@/features/adoption/utils/adoptionDisplay";
import { ApiError } from "@/lib/api/errors";

const CONFIRM: Record<Exclude<AdoptionAction, "accept">, { title: string; warning: string; confirm: string; success: string; error: string; icon: typeof X; tone: "destructive" | "neutral" }> = {
  decline: {
    title: "Recusar interesse",
    warning: "O interessado será avisado e a conversa será encerrada.",
    confirm: "Recusar",
    success: "Interesse recusado.",
    error: "Não foi possível recusar.",
    icon: X,
    tone: "destructive",
  },
  cancel: {
    title: "Cancelar interesse",
    warning: "O responsável pelo pet deixa de ver o seu interesse.",
    confirm: "Cancelar interesse",
    success: "Interesse cancelado.",
    error: "Não foi possível cancelar.",
    icon: Ban,
    tone: "destructive",
  },
  adopt: {
    title: "Confirmar adoção",
    warning: "O pet sai da lista de adoção e os outros interesses são recusados.",
    confirm: "Marcar como adotado",
    success: "Adoção confirmada!",
    error: "Não foi possível confirmar a adoção.",
    icon: HeartHandshake,
    tone: "neutral",
  },
};

export function AdoptionActions({ adoption }: { adoption: Adoption }) {
  const router = useRouter();
  const [confirming, setConfirming] = useState<Exclude<AdoptionAction, "accept"> | null>(null);
  const [isAccepting, setIsAccepting] = useState(false);
  const actions = availableActions(adoption);

  if (actions.length === 0) return null;

  async function accept(): Promise<void> {
    setIsAccepting(true);
    try {
      await adoptionService.decide(adoption.uuid, "accepted");
      toast.success("Interesse aceito. O chat foi aberto.");
      router.refresh();
    } catch (caught) {
      toast.error(caught instanceof ApiError ? caught.message : "Não foi possível aceitar.");
    } finally {
      setIsAccepting(false);
    }
  }

  async function confirm(action: Exclude<AdoptionAction, "accept">): Promise<void> {
    if (action === "cancel") await adoptionService.cancel(adoption.uuid);
    else await adoptionService.decide(adoption.uuid, action === "adopt" ? "adopted" : "declined");
  }

  const current = confirming ? CONFIRM[confirming] : null;

  return (
    <>
      <div className="flex flex-wrap gap-2">
        {actions.includes("accept") && (
          <Button className="cursor-pointer" disabled={isAccepting} onClick={() => void accept()}>
            <Check /> Aceitar
          </Button>
        )}
        {actions.includes("adopt") && (
          <Button className="cursor-pointer" onClick={() => setConfirming("adopt")}>
            <HeartHandshake /> Marcar como adotado
          </Button>
        )}
        {actions.includes("decline") && (
          <Button variant="outline" className="cursor-pointer" onClick={() => setConfirming("decline")}>
            <X /> Recusar
          </Button>
        )}
        {actions.includes("cancel") && (
          <Button variant="outline" className="cursor-pointer" onClick={() => setConfirming("cancel")}>
            <Ban /> Cancelar interesse
          </Button>
        )}
      </div>

      <DeleteDialog
        name={confirming ? adoption.pet.name : null}
        title={current?.title ?? ""}
        description={`Sobre ${adoption.pet.name}.`}
        warning={current?.warning ?? ""}
        successMessage={current?.success ?? ""}
        errorMessage={current?.error ?? ""}
        confirmLabel={current?.confirm}
        icon={current?.icon}
        tone={current?.tone}
        onConfirm={() => confirm(confirming ?? "decline")}
        onDeleted={() => router.refresh()}
        onOpenChange={(open) => !open && setConfirming(null)}
      />
    </>
  );
}
