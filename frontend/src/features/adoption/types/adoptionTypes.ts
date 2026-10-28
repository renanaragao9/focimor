import type { ChatMessage } from "@/features/chat/types/chatTypes";
import type { FeedPet, SwipeAction } from "@/features/match/types/matchTypes";

export type AdoptionStatus = "pending" | "accepted" | "declined" | "adopted" | "cancelled";
export type AdoptionRole = "adopter" | "owner";
export type AdoptionTab = "meus" | "recebidos";

export interface Adoption {
  uuid: string;
  status: AdoptionStatus;
  status_label: string;
  role: AdoptionRole;
  created_at: string | null;
  decided_at: string | null;
  pet: FeedPet;
  adopter?: { name: string };
  unread_count?: number;
  last_message?: ChatMessage | null;
}

export interface AdoptionSwipeResult {
  swipe: { action: SwipeAction; pet_uuid: string };
  interested: boolean;
  adoption: Adoption | null;
}

export interface AdoptionsParams {
  tab: AdoptionTab;
  status: "" | AdoptionStatus;
  page: number;
}

export const ADOPTION_STATUSES: readonly AdoptionStatus[] = ["pending", "accepted", "declined", "adopted", "cancelled"];
export const ADOPTIONS_PER_PAGE = 12;
