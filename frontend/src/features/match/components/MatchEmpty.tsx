import { Dog, SearchX, TriangleAlert, type LucideIcon } from "lucide-react";
import type { ReactNode } from "react";

export type MatchEmptyKind = "no-pets" | "finished" | "error";

interface MatchEmptyProps {
  kind: MatchEmptyKind;
  message?: string;
  children?: ReactNode;
}

const CONTENT: Record<MatchEmptyKind, { icon: LucideIcon; title: string; description: string }> = {
  "no-pets": {
    icon: Dog,
    title: "Cadastre um pet para dar match",
    description: "Para dar match, você precisa de um pet ativo que não esteja para adoção. Cadastre um e volte aqui.",
  },
  finished: {
    icon: SearchX,
    title: "Você viu todos os pets por aqui",
    description: "Novos pets aparecem o tempo todo. Volte mais tarde ou amplie os filtros para ver mais.",
  },
  error: {
    icon: TriangleAlert,
    title: "Não foi possível buscar pets",
    description: "Ajuste os filtros e tente de novo.",
  },
};

export function MatchEmpty({ kind, message, children }: MatchEmptyProps) {
  const { icon: Icon, title, description } = CONTENT[kind];

  return (
    <div className="mx-auto flex w-full max-w-md flex-col items-center gap-4 rounded-2xl border border-dashed px-6 py-14 text-center">
      <span className="flex size-16 items-center justify-center rounded-full bg-brand-soft">
        <Icon className="size-8 text-brand" aria-hidden="true" />
      </span>
      <div className="flex flex-col gap-1">
        <h2 className="text-xl font-semibold">{title}</h2>
        <p className="text-muted-foreground">{message ?? description}</p>
      </div>
      {children}
    </div>
  );
}
