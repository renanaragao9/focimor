import type { Metadata } from "next";
import { Suspense } from "react";

import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import { parseListParams } from "@/features/ong/utils/ongParams";
import { OngsView } from "@/features/ong/views/OngsView";
import { pageMetadata } from "@/lib/seo";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = pageMetadata({ title: "ONGs de proteção animal", description: "Conheça ONGs verificadas, veja os pets que esperam um lar e ajude com doações.", path: "/ongs" });

async function OngsContent({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return <OngsView params={parseListParams(await searchParams)} />;
}

export default function OngsPage({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <OngsContent searchParams={searchParams} />
    </Suspense>
  );
}
