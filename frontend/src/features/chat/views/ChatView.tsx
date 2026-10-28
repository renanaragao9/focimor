import { notFound } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { ChatHeader } from "@/features/chat/components/ChatHeader";
import { ChatRoom } from "@/features/chat/components/ChatRoom";
import { fetchLatestMessages, fetchMatch } from "@/features/chat/server/chatServer";
import type { MatchItem } from "@/features/chat/types/chatTypes";
import { ApiError } from "@/lib/api/errors";
import type { AuthUser } from "@/lib/auth/authTypes";

interface ChatViewProps {
  user: AuthUser;
  uuid: string;
}

async function loadMatch(uuid: string): Promise<MatchItem> {
  try {
    return await fetchMatch(uuid);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}

export async function ChatView({ user, uuid }: ChatViewProps) {
  const match = await loadMatch(uuid);
  const page = await fetchLatestMessages(uuid);

  return (
    <AppShell user={user}>
      <ChatRoom
        key={match.uuid}
        scope="matches"
        uuid={match.uuid}
        petName={match.other_pet.name}
        header={<ChatHeader match={match} />}
        initial={page.data}
        initialHasMore={page.meta.current_page < page.meta.last_page}
      />
    </AppShell>
  );
}
