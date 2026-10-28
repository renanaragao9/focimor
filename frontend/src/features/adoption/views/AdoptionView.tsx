import { Hourglass, MessageCircleOff } from "lucide-react";
import { notFound } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { AdoptionActions } from "@/features/adoption/components/AdoptionActions";
import { AdoptionChatPanel } from "@/features/adoption/components/AdoptionChatPanel";
import { AdoptionChatHeader } from "@/features/adoption/components/AdoptionChatHeader";
import { AdoptionSummary } from "@/features/adoption/components/AdoptionSummary";
import { fetchAdoption } from "@/features/adoption/server/adoptionServer";
import type { Adoption } from "@/features/adoption/types/adoptionTypes";
import { chatAccess } from "@/features/adoption/utils/adoptionDisplay";
import { ChatRoom } from "@/features/chat/components/ChatRoom";
import { fetchLatestMessages } from "@/features/chat/server/chatServer";
import { EmptyState } from "@/features/ong/components/EmptyState";
import { ApiError } from "@/lib/api/errors";
import type { AuthUser } from "@/lib/auth/authTypes";

async function loadAdoption(uuid: string): Promise<Adoption> {
  try {
    return await fetchAdoption(uuid);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}

async function AdoptionChat({ adoption }: { adoption: Adoption }) {
  const access = chatAccess(adoption.status);

  if (access === "closed") {
    return adoption.status === "pending" ? (
      <EmptyState
        icon={Hourglass}
        title="Aguardando resposta"
        text={adoption.role === "owner" ? "Aceite o interesse para abrir a conversa." : "O chat abre quando o responsável aceitar o seu interesse."}
      />
    ) : (
      <EmptyState icon={MessageCircleOff} title="Sem conversa" text="Este interesse foi encerrado, então não há chat." />
    );
  }

  const page = await fetchLatestMessages(adoption.uuid, "adoptions");
  const title = adoption.role === "owner" ? (adoption.adopter?.name ?? "Interessado") : (adoption.pet.ong?.name ?? "Tutor do pet");

  return (
    <AdoptionChatPanel title={title}>
      <ChatRoom
        key={adoption.uuid}
        scope="adoptions"
        uuid={adoption.uuid}
        petName={adoption.pet.name}
        header={<AdoptionChatHeader title={title} />}
        initial={page.data}
        initialHasMore={page.meta.current_page < page.meta.last_page}
        canSend={access === "open"}
        className="min-h-0 flex-1 rounded-none border-0 sm:h-[calc(100dvh-20rem)] sm:min-h-96 sm:flex-none sm:rounded-sm sm:border"
      />
    </AdoptionChatPanel>
  );
}

export async function AdoptionView({ user, uuid }: { user: AuthUser; uuid: string }) {
  const adoption = await loadAdoption(uuid);

  return (
    <AppShell user={user}>
      <AdoptionSummary adoption={adoption} />
      <AdoptionActions key={adoption.status} adoption={adoption} />
      <AdoptionChat adoption={adoption} />
    </AppShell>
  );
}
