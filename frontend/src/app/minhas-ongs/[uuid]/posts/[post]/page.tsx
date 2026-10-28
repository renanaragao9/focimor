import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import { PostDetailView } from "@/features/ong-panel/views/PostDetailView";
import { requireUser } from "@/lib/auth/requireUser";
import { isUuid } from "@/lib/uuid";

export const metadata: Metadata = { title: "Detalhes do post", robots: { index: false, follow: false } };

type PageProps = { params: Promise<{ uuid: string; post: string }> };

async function PostDetailContent({ params }: PageProps) {
  const { uuid, post } = await params;
  if (!isUuid(uuid) || !isUuid(post)) notFound();

  const user = await requireUser("/entrar", `/minhas-ongs/${uuid}/posts/${post}`);

  return <PostDetailView user={user} ongUuid={uuid} postUuid={post} />;
}

export default function PostDetailPage(props: PageProps) {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <PostDetailContent {...props} />
    </Suspense>
  );
}
