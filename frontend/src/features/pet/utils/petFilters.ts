import { DEFAULT_PET_FILTERS, PETS_PER_PAGE, type PetFilters, type PetStatusFilter } from "@/features/pet/types/petTypes";
import type { QueryParams } from "@/lib/api/types";
import { positiveInt, readParam, type RawSearchParams } from "@/lib/searchParams";

const STATUSES: readonly PetStatusFilter[] = ["active", "inactive"];
const MAX_SEARCH_LENGTH = 100;

export function parsePetFilters(raw: RawSearchParams): PetFilters {
  const status = readParam(raw.status);

  return {
    search: readParam(raw.search).trim().slice(0, MAX_SEARCH_LENGTH),
    speciesId: String(positiveInt(readParam(raw.species)) ?? ""),
    status: (STATUSES as readonly string[]).includes(status) ? (status as PetStatusFilter) : "",
    page: positiveInt(readParam(raw.page)) ?? 1,
  };
}

export function petFiltersToQuery(filters: PetFilters): QueryParams {
  return {
    page: filters.page,
    per_page: PETS_PER_PAGE,
    sort: "created_at",
    direction: "desc",
    search: filters.search || undefined,
    "filters[species_id]": filters.speciesId || undefined,
    "filters[is_active]": filters.status === "" ? undefined : filters.status === "active" ? 1 : 0,
  };
}

export function petFiltersToSearch(filters: PetFilters): string {
  const params = new URLSearchParams();
  if (filters.search !== DEFAULT_PET_FILTERS.search) params.set("search", filters.search);
  if (filters.speciesId !== DEFAULT_PET_FILTERS.speciesId) params.set("species", filters.speciesId);
  if (filters.status !== DEFAULT_PET_FILTERS.status) params.set("status", filters.status);
  if (filters.page !== DEFAULT_PET_FILTERS.page) params.set("page", String(filters.page));
  return params.toString();
}

export function petsHref(filters: PetFilters): string {
  const search = petFiltersToSearch(filters);
  return search ? `/meus-pets?${search}` : "/meus-pets";
}

export function hasActiveFilters(filters: PetFilters): boolean {
  return filters.search !== "" || filters.speciesId !== "" || filters.status !== "";
}
