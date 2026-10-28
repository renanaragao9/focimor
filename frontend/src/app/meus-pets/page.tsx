import type { Metadata } from "next";
import { Suspense } from "react";

import { PetsSkeleton } from "@/features/pet/components/PetsSkeleton";
import { PetsView } from "@/features/pet/views/PetsView";
import { parsePetFilters } from "@/features/pet/utils/petFilters";
import { requireUser } from "@/lib/auth/requireUser";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = { title: "Meus pets", robots: { index: false, follow: false } };

async function PetsContent({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  const user = await requireUser("/entrar", "/meus-pets");
  const filters = parsePetFilters(await searchParams);

  return <PetsView user={user} filters={filters} />;
}

export default function MyPetsPage({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<PetsSkeleton />}>
      <PetsContent searchParams={searchParams} />
    </Suspense>
  );
}
