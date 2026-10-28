import type { Metadata } from "next";
import { Suspense } from "react";

import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import { parseListParams } from "@/features/ong/utils/ongParams";
import { PostsView } from "@/features/ong/views/PostsView";
import { pageMetadata } from "@/lib/seo";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = pageMetadata({ title: "Novidades das ONGs", description: "Acompanhe campanhas, eventos, resgates e pedidos de ajuda das ONGs parceiras.", path: "/posts" });

async function PostsContent({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return <PostsView params={parseListParams(await searchParams)} />;
}

export default function PostsPage({ searchParams }: { searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <PostsContent searchParams={searchParams} />
    </Suspense>
  );
}
