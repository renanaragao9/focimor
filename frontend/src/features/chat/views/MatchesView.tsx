import { redirect } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { Pagination } from "@/components/Pagination";
import { H1, Muted } from "@/components/ui/typography";
import { MatchesEmpty } from "@/features/chat/components/MatchesEmpty";
import { MatchesFilters } from "@/features/chat/components/MatchesFilters";
import { MatchRow } from "@/features/chat/components/MatchRow";
import { fetchMatches, fetchPetOptions } from "@/features/chat/server/chatServer";
import { matchesHref, type MatchesParams } from "@/features/chat/utils/matchesParams";
import type { AuthUser } from "@/lib/auth/authTypes";

interface MatchesViewProps {
  user: AuthUser;
  params: MatchesParams;
}

export async function MatchesView({ user, params }: MatchesViewProps) {
  const [page, pets] = await Promise.all([fetchMatches(params), fetchPetOptions()]);

  if (page.data.length === 0 && params.page > 1) redirect(matchesHref({ ...params, page: Math.max(page.meta.last_page, 1) }));

  const showMyPet = pets.length > 1 && params.petUuid === "";

  return (
    <AppShell user={user}>
      <div className="flex flex-col gap-1">
        <H1 className="text-3xl">Matches</H1>
        <Muted>Os pets que se curtiram de volta. Toque em um para conversar.</Muted>
      </div>

      {pets.length > 1 && <MatchesFilters petUuid={params.petUuid} pets={pets} />}

      {page.data.length === 0 ? (
        <MatchesEmpty filtered={params.petUuid !== ""} />
      ) : (
        <>
          <div className="flex flex-col gap-2">
            {page.data.map((match) => (
              <MatchRow key={match.uuid} match={match} showMyPet={showMyPet} />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => matchesHref({ ...params, page: target })} />
        </>
      )}
    </AppShell>
  );
}
