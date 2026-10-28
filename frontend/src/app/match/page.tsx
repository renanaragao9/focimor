import type { Metadata } from "next";
import { Suspense } from "react";

import { MatchSkeleton } from "@/features/match/components/MatchSkeleton";
import { parseMatchFilters } from "@/features/match/utils/matchFilters";
import { MatchView } from "@/features/match/views/MatchView";
import { requireUser } from "@/lib/auth/requireUser";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = { title: "Match", robots: { index: false, follow: false } };

async function MatchContent({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  const user = await requireUser("/entrar", "/match");

  return <MatchView user={user} filters={parseMatchFilters(await searchParams)} />;
}

export default function MatchPage({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<MatchSkeleton />}>
      <MatchContent searchParams={searchParams} />
    </Suspense>
  );
}
