import "server-only";

import type { CatalogItem, Pet } from "@/features/pet/types/petTypes";
import type { FeedPet, MatchFilters } from "@/features/match/types/matchTypes";
import { matchFiltersToQuery } from "@/features/match/utils/matchFilters";
import { ApiError } from "@/lib/api/errors";
import { apiServer, apiServerPage } from "@/lib/api/server";

const ADOPTION_SLUG = "adoption";
const MAX_PETS = 100;

export async function fetchEligiblePets(): Promise<Pet[]> {
  const [page, goals] = await Promise.all([
    apiServerPage<Pet>("pets", { query: { page: 1, per_page: MAX_PETS, sort: "name", direction: "asc", "filters[is_active]": 1 } }),
    apiServer<CatalogItem[]>("selects/goals"),
  ]);

  const adoptionGoalId = goals.find((goal) => goal.slug === ADOPTION_SLUG)?.id;

  return page.data.filter((pet) => pet.goal.id !== adoptionGoalId);
}

export async function fetchMatchCatalogs(): Promise<{ species: CatalogItem[]; sizes: CatalogItem[] }> {
  const [species, sizes] = await Promise.all([apiServer<CatalogItem[]>("selects/species"), apiServer<CatalogItem[]>("selects/sizes")]);

  return { species, sizes };
}

export async function fetchFeed(filters: MatchFilters): Promise<{ items: FeedPet[]; error: string | null }> {
  try {
    const page = await apiServerPage<FeedPet>(`pets/${filters.petUuid}/feed`, { query: matchFiltersToQuery(filters) });

    return { items: page.data, error: null };
  } catch (error) {
    if (error instanceof ApiError && error.isValidation) return { items: [], error: error.message };
    throw error;
  }
}
