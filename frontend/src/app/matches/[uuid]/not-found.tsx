import { HeartOff } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";

export default function MatchNotFound() {
  return (
    <div className="flex min-h-dvh flex-col items-center justify-center gap-5 px-6 text-center">
      <span className="flex size-20 items-center justify-center rounded-full bg-brand-soft">
        <HeartOff className="size-10 text-brand" aria-hidden="true" />
      </span>
      <div className="flex flex-col gap-1">
        <h1 className="text-2xl font-bold">Conversa não encontrada</h1>
        <p className="max-w-md text-muted-foreground">Esse match não existe mais ou foi desfeito. Volte para a lista e escolha outro.</p>
      </div>
      <Button asChild>
        <Link href="/matches">Voltar para Matches</Link>
      </Button>
    </div>
  );
}
