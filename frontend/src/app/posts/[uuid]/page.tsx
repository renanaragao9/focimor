import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import { fetchPost } from "@/features/ong/server/ongServer";
import { clip, pageMetadata } from "@/lib/seo";
import { PostView } from "@/features/ong/views/PostView";
import { isUuid } from "@/lib/uuid";

export async function generateMetadata({ params }: { params: Promise<{ uuid: string }> }): Promise<Metadata> {
  const { uuid } = await params;
  if (!isUuid(uuid)) return { title: "Publicação", robots: { index: false } };

  try {
    const post = await fetchPost(uuid);

    return pageMetadata({ title: post.title, description: clip(post.body), path: `/posts/${uuid}`, image: post.cover_url, type: "article" });
  } catch {
    return { title: "Publicação", robots: { index: false } };
  }
}

async function PostContent({ params }: { params: Promise<{ uuid: string }> }) {
  const { uuid } = await params;
  if (!isUuid(uuid)) notFound();

  return <PostView uuid={uuid} />;
}

export default function PostPage({ params }: { params: Promise<{ uuid: string }> }) {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <PostContent params={params} />
    </Suspense>
  );
}
