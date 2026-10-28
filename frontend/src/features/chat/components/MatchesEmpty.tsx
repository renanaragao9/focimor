import { HeartOff, MessageCircleHeart } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";

interface MatchesEmptyProps {
  filtered: boolean;
}

export function MatchesEmpty({ filtered }: MatchesEmptyProps) {
  const Icon = filtered ? HeartOff : MessageCircleHeart;

  return (
    <div className="flex flex-col items-center gap-4 rounded-sm border border-dashed px-6 py-16 text-center">
      <span className="flex size-16 items-center justify-center rounded-full bg-brand-soft">
        <Icon className="size-8 text-brand" aria-hidden="true" />
      </span>
      <div className="flex flex-col gap-1">
        <h2 className="text-xl font-semibold">{filtered ? "Nenhum match com este pet" : "Você ainda não tem matches"}</h2>
        <p className="max-w-md text-muted-foreground">
          {filtered ? "Escolha outro pet ou veja todos os matches." : "Curta outros pets na tela de Match. Quando os dois se curtirem, o match aparece aqui."}
        </p>
      </div>
      <Button asChild variant={filtered ? "outline" : "default"}>
        <Link href={filtered ? "/matches" : "/match"}>{filtered ? "Ver todos" : "Ir para o Match"}</Link>
      </Button>
    </div>
  );
}
