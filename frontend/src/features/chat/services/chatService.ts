import { MESSAGES_PER_PAGE, type ChatMessage, type ChatScope } from "@/features/chat/types/chatTypes";
import { api, apiPage } from "@/lib/api/client";

export function chatServiceFor(scope: ChatScope) {
  return {
    messages: (uuid: string, page: number) => apiPage<ChatMessage>(`${scope}/${uuid}/messages`, { query: { page, per_page: MESSAGES_PER_PAGE, sort: "created_at", direction: "desc" } }),
    send: (uuid: string, body: string) => api<ChatMessage>(`${scope}/${uuid}/messages`, { method: "POST", json: { body } }),
    markRead: (uuid: string) => api<{ read: number }>(`${scope}/${uuid}/messages/read`, { method: "PUT" }),
  };
}

export const chatService = {
  ...chatServiceFor("matches"),
  unmatch: (matchUuid: string) => api<null>(`matches/${matchUuid}`, { method: "DELETE" }),
};
