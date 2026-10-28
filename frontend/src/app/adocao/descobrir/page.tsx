import type { Metadata } from "next";
import { Suspense } from "react";

import { AdoptionsSkeleton } from "@/features/adoption/components/AdoptionsSkeleton";
import { AdoptionFeedView } from "@/features/adoption/views/AdoptionFeedView";
import { parseMatchFilters } from "@/features/match/utils/matchFilters";
import { requireUser } from "@/lib/auth/requireUser";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = { title: "Descobrir pets para adoção", robots: { index: false, follow: false } };

async function AdoptionFeedContent({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  const user = await requireUser("/entrar", "/adocao/descobrir");

  return <AdoptionFeedView user={user} filters={{ ...parseMatchFilters(await searchParams), petUuid: "" }} />;
}

export default function AdoptionFeedPage({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<AdoptionsSkeleton />}>
      <AdoptionFeedContent searchParams={searchParams} />
    </Suspense>
  );
}
