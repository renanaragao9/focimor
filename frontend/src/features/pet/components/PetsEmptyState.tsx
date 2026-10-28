import { PawPrint, Plus, SearchX } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";

interface PetsEmptyStateProps {
  filtered: boolean;
  onCreate: () => void;
}

export function PetsEmptyState({ filtered, onCreate }: PetsEmptyStateProps) {
  const Icon = filtered ? SearchX : PawPrint;

  return (
    <div className="flex flex-col items-center gap-4 rounded-sm border border-dashed px-6 py-16 text-center">
      <span className="flex size-16 items-center justify-center rounded-full bg-brand-soft">
        <Icon className="size-8 text-brand" aria-hidden="true" />
      </span>
      <div className="flex flex-col gap-1">
        <h2 className="text-xl font-semibold">{filtered ? "Nenhum pet encontrado" : "Você ainda não cadastrou nenhum pet"}</h2>
        <p className="max-w-md text-muted-foreground">{filtered ? "Tente buscar por outro nome ou limpar os filtros." : "Cadastre o primeiro para começar a dar match e encontrar novos amigos."}</p>
      </div>
      {filtered ? (
        <Button variant="outline" asChild>
          <Link href="/meus-pets">Limpar filtros</Link>
        </Button>
      ) : (
        <Button className="cursor-pointer" onClick={onCreate}>
          <Plus /> Cadastrar pet
        </Button>
      )}
    </div>
  );
}
