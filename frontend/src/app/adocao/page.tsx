import type { Metadata } from "next";

import { pageMetadata } from "@/lib/seo";
import { Suspense } from "react";

import { parseAdoptablePetParams } from "@/features/adoption/utils/adoptablePetParams";
import { AdoptablePetsView } from "@/features/adoption/views/AdoptablePetsView";
import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = pageMetadata({
  title: "Adote um pet",
  description: "Cães e gatos de ONGs verificadas esperando um lar. Filtre por espécie, porte e sexo e adote com responsabilidade.",
  path: "/adocao",
});

async function AdoptablePetsContent({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return <AdoptablePetsView params={parseAdoptablePetParams(await searchParams)} />;
}

export default function AdoptablePetsPage({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <AdoptablePetsContent searchParams={searchParams} />
    </Suspense>
  );
}
