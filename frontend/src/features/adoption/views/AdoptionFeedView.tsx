import Link from "next/link";

import { AppShell } from "@/components/app/AppShell";
import { Button } from "@/components/ui/button";
import { H1, Muted } from "@/components/ui/typography";
import { AdoptionBoard } from "@/features/adoption/components/AdoptionBoard";
import { fetchAdoptionFeed } from "@/features/adoption/server/adoptionServer";
import { MatchEmpty } from "@/features/match/components/MatchEmpty";
import { MatchFiltersBar } from "@/features/match/components/MatchFiltersBar";
import { fetchMatchCatalogs } from "@/features/match/server/matchServer";
import type { MatchFilters } from "@/features/match/types/matchTypes";
import { matchBoardKey, matchHref } from "@/features/match/utils/matchFilters";
import type { AuthUser } from "@/lib/auth/authTypes";

export async function AdoptionFeedView({ user, filters }: { user: AuthUser; filters: MatchFilters }) {
  const [{ items, error }, catalogs] = await Promise.all([fetchAdoptionFeed(filters), fetchMatchCatalogs()]);

  return (
    <AppShell user={user}>
      <div className="flex flex-col items-center gap-1 text-center">
        <H1 className="text-3xl">Adoção</H1>
        <Muted>Encontre um novo amigo. Curtir envia seu interesse para quem cuida do pet.</Muted>
        <Button variant="link" size="sm" asChild>
          <Link href="/adocoes">Ver meus interesses</Link>
        </Button>
      </div>
      <div className="relative flex flex-col gap-6 lg:block">
        <aside className="lg:absolute lg:right-[calc(50%+16rem)] lg:top-2 lg:flex lg:flex-col lg:gap-2">
          <h2 className="hidden text-sm font-semibold text-foreground lg:block">Filtros</h2>
          <MatchFiltersBar filters={filters} species={catalogs.species} sizes={catalogs.sizes} basePath="/adocao/descobrir" anySpeciesLabel="Qualquer espécie" />
        </aside>
        {error ? (
          <MatchEmpty kind="error" message={error}>
            <Button variant="outline" asChild>
              <Link href={matchHref({ ...filters, maxDistanceKm: "" }, "/adocao/descobrir")}>Remover filtro de distância</Link>
            </Button>
          </MatchEmpty>
        ) : (
          <AdoptionBoard key={matchBoardKey(filters)} initial={items} filters={filters} />
        )}
      </div>
    </AppShell>
  );
}
