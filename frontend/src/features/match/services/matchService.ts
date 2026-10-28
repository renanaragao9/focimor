import type { FeedPet, MatchFilters, SwipeAction, SwipeResult } from "@/features/match/types/matchTypes";
import { matchFiltersToQuery } from "@/features/match/utils/matchFilters";
import { api, apiPage } from "@/lib/api/client";

export const matchService = {
  feed: (petUuid: string, filters: MatchFilters) => apiPage<FeedPet>(`pets/${petUuid}/feed`, { query: matchFiltersToQuery(filters) }),
  swipe: (petUuid: string, targetPetUuid: string, action: SwipeAction) => api<SwipeResult>(`pets/${petUuid}/swipes`, { method: "POST", json: { target_pet_uuid: targetPetUuid, action } }),
};
