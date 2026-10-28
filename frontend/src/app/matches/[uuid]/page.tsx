import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { ChatSkeleton } from "@/features/chat/components/ChatSkeleton";
import { ChatView } from "@/features/chat/views/ChatView";
import { requireUser } from "@/lib/auth/requireUser";
import { isUuid } from "@/lib/uuid";

export const metadata: Metadata = { title: "Conversa", robots: { index: false, follow: false } };

async function ChatContent({ params }: { params: Promise<{ uuid: string }> }) {
  const { uuid } = await params;
  if (!isUuid(uuid)) notFound();

  const user = await requireUser("/entrar", `/matches/${uuid}`);

  return <ChatView user={user} uuid={uuid} />;
}

export default function ChatPage({ params }: { params: Promise<{ uuid: string }> }) {
  return (
    <Suspense fallback={<ChatSkeleton />}>
      <ChatContent params={params} />
    </Suspense>
  );
}
