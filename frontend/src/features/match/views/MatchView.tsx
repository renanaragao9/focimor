import Link from "next/link";

import { AppShell } from "@/components/app/AppShell";
import { Button } from "@/components/ui/button";
import { H1, Muted } from "@/components/ui/typography";
import { MatchBoard } from "@/features/match/components/MatchBoard";
import { MatchEmpty } from "@/features/match/components/MatchEmpty";
import { MatchFiltersBar } from "@/features/match/components/MatchFiltersBar";
import { fetchEligiblePets, fetchFeed, fetchMatchCatalogs } from "@/features/match/server/matchServer";
import type { MatchFilters } from "@/features/match/types/matchTypes";
import { matchBoardKey, matchHref } from "@/features/match/utils/matchFilters";
import type { AuthUser } from "@/lib/auth/authTypes";

interface MatchViewProps {
  user: AuthUser;
  filters: MatchFilters;
}

function MatchHeader() {
  return (
    <div className="flex flex-col items-center gap-1 text-center">
      <H1 className="text-3xl">Match</H1>
      <Muted>Deslize para a direita para curtir, para a esquerda para passar.</Muted>
    </div>
  );
}

export async function MatchView({ user, filters }: MatchViewProps) {
  const [pets, catalogs] = await Promise.all([fetchEligiblePets(), fetchMatchCatalogs()]);
  const actingPet = pets.find((pet) => pet.uuid === filters.petUuid) ?? pets[0];

  if (!actingPet) {
    return (
      <AppShell user={user}>
        <MatchHeader />
        <MatchEmpty kind="no-pets">
          <Button asChild>
            <Link href="/meus-pets">Ir para Meus pets</Link>
          </Button>
        </MatchEmpty>
      </AppShell>
    );
  }

  const active: MatchFilters = { ...filters, petUuid: actingPet.uuid };
  const { items, error } = await fetchFeed(active);

  return (
    <AppShell user={user}>
      <MatchHeader />
      <div className="relative flex flex-col gap-6 lg:block">
        <aside className="lg:absolute lg:right-[calc(50%+16rem)] lg:top-2 lg:flex lg:flex-col lg:gap-2">
          <h2 className="hidden text-sm font-semibold text-foreground lg:block">Filtros</h2>
          <MatchFiltersBar filters={active} pets={pets} species={catalogs.species} sizes={catalogs.sizes} />
        </aside>
        {error ? (
          <MatchEmpty kind="error" message={error}>
            <Button variant="outline" asChild>
              <Link href={matchHref({ ...active, maxDistanceKm: "" })}>Remover filtro de distância</Link>
            </Button>
          </MatchEmpty>
        ) : (
          <MatchBoard key={matchBoardKey(active)} petUuid={actingPet.uuid} petPhotos={actingPet.photos} initial={items} filters={active} />
        )}
      </div>
    </AppShell>
  );
}
