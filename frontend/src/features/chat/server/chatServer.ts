import "server-only";

import { MESSAGES_PER_PAGE, type ChatMessage, type ChatScope, type MatchItem, type PetOption } from "@/features/chat/types/chatTypes";
import { matchesToQuery, type MatchesParams } from "@/features/chat/utils/matchesParams";
import { apiServer, apiServerPage } from "@/lib/api/server";

export function fetchMatches(params: MatchesParams) {
  return apiServerPage<MatchItem>("matches", { query: matchesToQuery(params) });
}

export function fetchMatch(uuid: string) {
  return apiServer<MatchItem>(`matches/${uuid}`);
}

export function fetchLatestMessages(uuid: string, scope: ChatScope = "matches") {
  return apiServerPage<ChatMessage>(`${scope}/${uuid}/messages`, { query: { page: 1, per_page: MESSAGES_PER_PAGE, sort: "created_at", direction: "desc" } });
}

export async function fetchPetOptions(): Promise<PetOption[]> {
  const page = await apiServerPage<PetOption>("pets", { query: { page: 1, per_page: 100, sort: "name", direction: "asc" } });

  return page.data.map((pet) => ({ uuid: pet.uuid, name: pet.name }));
}
