import type { Metadata } from "next";
import { Suspense } from "react";

import { MatchesSkeleton } from "@/features/chat/components/MatchesSkeleton";
import { parseMatchesParams } from "@/features/chat/utils/matchesParams";
import { MatchesView } from "@/features/chat/views/MatchesView";
import { requireUser } from "@/lib/auth/requireUser";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = { title: "Matches", robots: { index: false, follow: false } };

async function MatchesContent({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  const user = await requireUser("/entrar", "/matches");

  return <MatchesView user={user} params={parseMatchesParams(await searchParams)} />;
}

export default function MatchesPage({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<MatchesSkeleton />}>
      <MatchesContent searchParams={searchParams} />
    </Suspense>
  );
}
