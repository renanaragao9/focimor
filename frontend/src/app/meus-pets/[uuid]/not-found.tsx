import { PawPrint } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";

export default function PetNotFound() {
  return (
    <div className="flex min-h-dvh flex-col items-center justify-center gap-5 px-6 text-center">
      <span className="flex size-20 items-center justify-center rounded-full bg-brand-soft">
        <PawPrint className="size-10 text-brand" aria-hidden="true" />
      </span>
      <div className="flex flex-col gap-1">
        <h1 className="text-2xl font-bold">Pet não encontrado</h1>
        <p className="max-w-md text-muted-foreground">Esse pet não existe ou não é seu. Volte para a lista e escolha outro.</p>
      </div>
      <Button asChild>
        <Link href="/meus-pets">Voltar para Meus pets</Link>
      </Button>
    </div>
  );
}
