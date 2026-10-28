import type { NamedRef, PetGender, PetPhoto } from "@/features/pet/types/petTypes";

export type SwipeAction = "like" | "dislike" | "superlike";

export interface FeedPet {
  uuid: string;
  name: string;
  gender: PetGender;
  color: string | null;
  bio: string | null;
  birth_date: string | null;
  is_neutered: boolean;
  is_vaccinated: boolean;
  distance_km: number | null;
  species: NamedRef;
  breed: NamedRef | null;
  size: NamedRef | null;
  goal: NamedRef;
  photos: PetPhoto[];
  ong?: { uuid: string; name: string; city: string | null; state: string | null; is_verified: boolean } | null;
}

export interface MatchInfo {
  uuid: string;
  matched_at: string;
  my_pet: { uuid: string; name: string };
  other_pet: FeedPet;
}

export interface SwipeResult {
  swipe: { action: SwipeAction; target_pet_uuid: string };
  matched: boolean;
  match: MatchInfo | null;
}

export interface MatchFilters {
  petUuid: string;
  speciesId: string;
  sizeId: string;
  gender: "" | PetGender;
  maxDistanceKm: string;
}

export const DEFAULT_MATCH_FILTERS: MatchFilters = { petUuid: "", speciesId: "", sizeId: "", gender: "", maxDistanceKm: "" };

export const FEED_PAGE_SIZE = 10;
export const DISTANCE_OPTIONS_KM = [10, 25, 50, 100, 300] as const;
export const REFILL_THRESHOLD = 3;
