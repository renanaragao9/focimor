import { DEFAULT_MATCH_FILTERS, DISTANCE_OPTIONS_KM, FEED_PAGE_SIZE, type MatchFilters } from "@/features/match/types/matchTypes";
import type { QueryParams } from "@/lib/api/types";
import { positiveInt, readParam, type RawSearchParams } from "@/lib/searchParams";
import { isUuid } from "@/lib/uuid";

export function parseMatchFilters(raw: RawSearchParams): MatchFilters {
  const pet = readParam(raw.pet);
  const gender = readParam(raw.sexo);
  const distance = positiveInt(readParam(raw.dist));

  return {
    petUuid: isUuid(pet) ? pet : "",
    speciesId: String(positiveInt(readParam(raw.especie)) ?? ""),
    sizeId: String(positiveInt(readParam(raw.porte)) ?? ""),
    gender: gender === "male" || gender === "female" ? gender : "",
    maxDistanceKm: distance !== null && (DISTANCE_OPTIONS_KM as readonly number[]).includes(distance) ? String(distance) : "",
  };
}

export function matchFiltersToQuery(filters: MatchFilters): QueryParams {
  return {
    page: 1,
    per_page: FEED_PAGE_SIZE,
    "filters[species_id]": filters.speciesId || undefined,
    "filters[size_id]": filters.sizeId || undefined,
    "filters[gender]": filters.gender || undefined,
    "filters[max_distance_km]": filters.maxDistanceKm || undefined,
  };
}

export function matchHref(filters: MatchFilters, basePath: "/match" | "/adocao/descobrir" = "/match"): string {
  const params = new URLSearchParams();
  if (filters.petUuid !== DEFAULT_MATCH_FILTERS.petUuid) params.set("pet", filters.petUuid);
  if (filters.speciesId !== DEFAULT_MATCH_FILTERS.speciesId) params.set("especie", filters.speciesId);
  if (filters.sizeId !== DEFAULT_MATCH_FILTERS.sizeId) params.set("porte", filters.sizeId);
  if (filters.gender !== DEFAULT_MATCH_FILTERS.gender) params.set("sexo", filters.gender);
  if (filters.maxDistanceKm !== DEFAULT_MATCH_FILTERS.maxDistanceKm) params.set("dist", filters.maxDistanceKm);

  const query = params.toString();
  return query ? `${basePath}?${query}` : basePath;
}

export function hasMatchFilters(filters: MatchFilters): boolean {
  return filters.speciesId !== "" || filters.sizeId !== "" || filters.gender !== "" || filters.maxDistanceKm !== "";
}

export function matchBoardKey(filters: MatchFilters): string {
  return [filters.petUuid, filters.speciesId, filters.sizeId, filters.gender, filters.maxDistanceKm].join("|");
}
