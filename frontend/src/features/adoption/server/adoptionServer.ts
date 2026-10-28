import "server-only";

import type { Adoption, AdoptionsParams } from "@/features/adoption/types/adoptionTypes";
import { adoptionsToQuery } from "@/features/adoption/utils/adoptionParams";
import type { FeedPet, MatchFilters } from "@/features/match/types/matchTypes";
import { matchFiltersToQuery } from "@/features/match/utils/matchFilters";
import { ApiError } from "@/lib/api/errors";
import { apiServer, apiServerPage } from "@/lib/api/server";

export async function fetchAdoptionFeed(filters: MatchFilters): Promise<{ items: FeedPet[]; error: string | null }> {
  try {
    const page = await apiServerPage<FeedPet>("adoptions/feed", { query: matchFiltersToQuery(filters) });

    return { items: page.data, error: null };
  } catch (error) {
    if (error instanceof ApiError && error.isValidation) return { items: [], error: error.message };
    throw error;
  }
}

export function fetchAdoptions(params: AdoptionsParams) {
  return apiServerPage<Adoption>(params.tab === "recebidos" ? "adoptions/received" : "adoptions", { query: adoptionsToQuery(params) });
}

export function fetchAdoption(uuid: string) {
  return apiServer<Adoption>(`adoptions/${uuid}`);
}
