"use client";

import { HandHeart, Loader2 } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { toast } from "sonner";

import { Button } from "@/components/ui/button";
import { adoptionService } from "@/features/adoption/services/adoptionService";
import { adoptablePetHref } from "@/features/adoption/utils/adoptablePetParams";
import { adoptionHref } from "@/features/adoption/utils/adoptionParams";
import { cn } from "@/lib/utils";
import { ApiError } from "@/lib/api/errors";

interface AdoptButtonProps {
  petUuid: string;
  petName: string;
  isAuthenticated: boolean;
  className?: string;
}

export function AdoptButton({ petUuid, petName, isAuthenticated, className }: AdoptButtonProps) {
  const router = useRouter();
  const [isSending, setIsSending] = useState(false);

  if (!isAuthenticated) {
    return (
      <div className="flex flex-col gap-2">
        <Button size="lg" className={className} asChild>
          <Link href={`/entrar?redirect=${encodeURIComponent(adoptablePetHref(petUuid))}`}>
            <HandHeart /> Entrar para adotar {petName}
          </Link>
        </Button>
        <p className="text-xs text-muted-foreground">
          Não tem conta?{" "}
          <Link href="/cadastro" className="font-semibold text-primary hover:underline">
            Cadastre-se
          </Link>
        </p>
      </div>
    );
  }

  async function handleClick(): Promise<void> {
    setIsSending(true);
    try {
      const result = await adoptionService.swipe(petUuid, "like", true);
      toast.success("Interesse enviado!", { description: "Avisamos o responsável. Você acompanha a resposta em Adoções." });
      if (result.adoption) router.push(adoptionHref(result.adoption.uuid));
    } catch (caught) {
      toast.error(caught instanceof ApiError ? caught.message : "Não foi possível enviar o interesse.");
    } finally {
      setIsSending(false);
    }
  }

  return (
    <Button size="lg" className={cn("cursor-pointer", className)} disabled={isSending} onClick={() => void handleClick()}>
      {isSending ? <Loader2 className="animate-spin" /> : <HandHeart />} Quero adotar {petName}
    </Button>
  );
}
