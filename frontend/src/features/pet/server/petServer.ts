import "server-only";

import type { CatalogItem, Pet, PetCatalogs, PetFilters } from "@/features/pet/types/petTypes";
import { petFiltersToQuery } from "@/features/pet/utils/petFilters";
import { apiServer, apiServerPage } from "@/lib/api/server";

export function fetchPets(filters: PetFilters) {
  return apiServerPage<Pet>("pets", { query: petFiltersToQuery(filters) });
}

export async function fetchPetCatalogs(): Promise<PetCatalogs> {
  const [species, sizes, goals] = await Promise.all([apiServer<CatalogItem[]>("selects/species"), apiServer<CatalogItem[]>("selects/sizes"), apiServer<CatalogItem[]>("selects/goals")]);

  return { species, sizes, goals };
}
