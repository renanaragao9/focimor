import type { Metadata } from "next";
import { Suspense } from "react";

import { AdoptionsSkeleton } from "@/features/adoption/components/AdoptionsSkeleton";
import { parseAdoptionsParams } from "@/features/adoption/utils/adoptionParams";
import { AdoptionsView } from "@/features/adoption/views/AdoptionsView";
import { requireUser } from "@/lib/auth/requireUser";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = { title: "Adoções", robots: { index: false, follow: false } };

async function AdoptionsContent({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  const user = await requireUser("/entrar", "/adocoes");

  return <AdoptionsView user={user} params={parseAdoptionsParams(await searchParams)} />;
}

export default function AdoptionsPage({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<AdoptionsSkeleton />}>
      <AdoptionsContent searchParams={searchParams} />
    </Suspense>
  );
}
