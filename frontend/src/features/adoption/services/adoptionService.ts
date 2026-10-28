import type { AdoptionStatus, AdoptionSwipeResult } from "@/features/adoption/types/adoptionTypes";
import type { Adoption } from "@/features/adoption/types/adoptionTypes";
import type { FeedPet, MatchFilters, SwipeAction } from "@/features/match/types/matchTypes";
import { matchFiltersToQuery } from "@/features/match/utils/matchFilters";
import { api, apiPage } from "@/lib/api/client";

export const adoptionService = {
  feed: (filters: MatchFilters) => apiPage<FeedPet>("adoptions/feed", { query: matchFiltersToQuery(filters) }),
  swipe: (petUuid: string, action: SwipeAction, direct = false) => api<AdoptionSwipeResult>("adoptions/swipes", { method: "POST", json: { pet_uuid: petUuid, action, direct } }),
  decide: (uuid: string, status: Extract<AdoptionStatus, "accepted" | "declined" | "adopted">) => api<Adoption>(`adoptions/${uuid}/decision`, { method: "PUT", json: { status } }),
  cancel: (uuid: string) => api<Adoption>(`adoptions/${uuid}`, { method: "DELETE" }),
};
