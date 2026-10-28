import { ChevronLeft, ChevronRight } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";
import type { PageMeta } from "@/lib/api/types";

interface PaginationProps {
  meta: PageMeta;
  buildHref: (page: number) => string;
}

export function Pagination({ meta, buildHref }: PaginationProps) {
  if (meta.last_page <= 1) return null;

  const hasPrevious = meta.current_page > 1;
  const hasNext = meta.current_page < meta.last_page;

  return (
    <nav aria-label="Paginação" className="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
      <p className="text-sm text-muted-foreground">
        Página {meta.current_page} de {meta.last_page} · {meta.total} {meta.total === 1 ? "resultado" : "resultados"}
      </p>
      <div className="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto">
        <Button variant="outline" size="sm" asChild={hasPrevious} disabled={!hasPrevious}>
          {hasPrevious ? (
            <Link href={buildHref(meta.current_page - 1)} scroll={false}>
              <ChevronLeft /> Anterior
            </Link>
          ) : (
            <>
              <ChevronLeft /> Anterior
            </>
          )}
        </Button>
        <Button variant="outline" size="sm" asChild={hasNext} disabled={!hasNext}>
          {hasNext ? (
            <Link href={buildHref(meta.current_page + 1)} scroll={false}>
              Próxima <ChevronRight />
            </Link>
          ) : (
            <>
              Próxima <ChevronRight />
            </>
          )}
        </Button>
      </div>
    </nav>
  );
}
