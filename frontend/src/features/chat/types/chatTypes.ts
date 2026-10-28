import type { NamedRef, PetGender, PetPhoto } from "@/features/pet/types/petTypes";

export interface MatchPet {
  uuid: string;
  name: string;
  gender: PetGender;
  bio: string | null;
  birth_date: string | null;
  is_neutered: boolean;
  is_vaccinated: boolean;
  distance_km: number | null;
  species: NamedRef;
  breed?: NamedRef | null;
  size?: NamedRef | null;
  photos: PetPhoto[];
}

export interface ChatMessage {
  uuid: string;
  body: string;
  is_mine: boolean;
  read_at: string | null;
  created_at: string;
  pending?: boolean;
}

export interface MatchItem {
  uuid: string;
  matched_at: string;
  my_pet: { uuid: string; name: string };
  other_pet: MatchPet;
  unread_count?: number;
  last_message?: ChatMessage | null;
}

export type ChatScope = "matches" | "adoptions";

export interface PetOption {
  uuid: string;
  name: string;
}

export const MATCHES_PER_PAGE = 12;
export const MESSAGES_PER_PAGE = 30;
export const MAX_MESSAGE_LENGTH = 2000;
export const POLL_INTERVAL_MS = 5000;
export const NEAR_BOTTOM_PX = 120;
