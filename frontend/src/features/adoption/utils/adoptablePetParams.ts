import type { QueryParams } from "@/lib/api/types";
import { positiveInt, readParam, type RawSearchParams } from "@/lib/searchParams";

export const ADOPTABLE_PER_PAGE = 12;
const MAX_SEARCH_LENGTH = 100;

export interface AdoptablePetParams {
  search: string;
  speciesId: string;
  sizeId: string;
  gender: "" | "male" | "female";
  page: number;
}

export const EMPTY_ADOPTABLE_PARAMS: AdoptablePetParams = { search: "", speciesId: "", sizeId: "", gender: "", page: 1 };

export function parseAdoptablePetParams(raw: RawSearchParams): AdoptablePetParams {
  const gender = readParam(raw.sexo);

  return {
    search: readParam(raw.search).trim().slice(0, MAX_SEARCH_LENGTH),
    speciesId: String(positiveInt(readParam(raw.especie)) ?? ""),
    sizeId: String(positiveInt(readParam(raw.porte)) ?? ""),
    gender: gender === "male" || gender === "female" ? gender : "",
    page: positiveInt(readParam(raw.page)) ?? 1,
  };
}

export function adoptablePetsToQuery(params: AdoptablePetParams): QueryParams {
  return {
    page: params.page,
    per_page: ADOPTABLE_PER_PAGE,
    search: params.search || undefined,
    "filters[species_id]": params.speciesId || undefined,
    "filters[size_id]": params.sizeId || undefined,
    "filters[gender]": params.gender || undefined,
  };
}

export function adoptablePetsHref(params: Partial<AdoptablePetParams> = {}): string {
  const search = new URLSearchParams();
  if (params.search) search.set("search", params.search);
  if (params.speciesId) search.set("especie", params.speciesId);
  if (params.sizeId) search.set("porte", params.sizeId);
  if (params.gender) search.set("sexo", params.gender);
  if (params.page && params.page > 1) search.set("page", String(params.page));

  const query = search.toString();
  return query ? `/adocao?${query}` : "/adocao";
}

export function hasAdoptableFilters(params: AdoptablePetParams): boolean {
  return params.search !== "" || params.speciesId !== "" || params.sizeId !== "" || params.gender !== "";
}

export function adoptablePetHref(uuid: string): string {
  return `/adocao/${uuid}`;
}
