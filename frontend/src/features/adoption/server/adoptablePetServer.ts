import "server-only";

import { adoptablePetsToQuery, type AdoptablePetParams } from "@/features/adoption/utils/adoptablePetParams";
import type { FeedPet } from "@/features/match/types/matchTypes";
import type { CatalogItem } from "@/features/pet/types/petTypes";
import { apiPublic, apiPublicPage } from "@/lib/api/server";

const REVALIDATE_SECONDS = 60;
const CATALOG_REVALIDATE_SECONDS = 3600;

export function fetchAdoptablePets(params: AdoptablePetParams) {
  return apiPublicPage<FeedPet>("adoptable-pets", { query: adoptablePetsToQuery(params) }, REVALIDATE_SECONDS);
}

export function fetchAdoptablePet(uuid: string) {
  return apiPublic<FeedPet>(`adoptable-pets/${uuid}`, {}, REVALIDATE_SECONDS);
}

export async function fetchAdoptableCatalogs(): Promise<{ species: CatalogItem[]; sizes: CatalogItem[] }> {
  const [species, sizes] = await Promise.all([apiPublic<CatalogItem[]>("selects/species", {}, CATALOG_REVALIDATE_SECONDS), apiPublic<CatalogItem[]>("selects/sizes", {}, CATALOG_REVALIDATE_SECONDS)]);

  return { species, sizes };
}
