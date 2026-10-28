import { PET_TABS, type PetTab } from "@/features/pet-detail/types/petDetailTypes";
import { isUuid } from "@/lib/uuid";
import { positiveInt, readParam, type RawSearchParams } from "@/lib/searchParams";

export interface PetDetailParams {
  tab: PetTab;
  page: number;
  typeId: string;
}

export function isPetUuid(value: string): boolean {
  return isUuid(value);
}

export function parsePetDetailParams(raw: RawSearchParams): PetDetailParams {
  const tab = readParam(raw.aba);

  return {
    tab: PET_TABS.some((item) => item.key === tab) ? (tab as PetTab) : "detalhes",
    page: positiveInt(readParam(raw.page)) ?? 1,
    typeId: String(positiveInt(readParam(raw.tipo)) ?? ""),
  };
}

export function petDetailHref(uuid: string, params: Partial<PetDetailParams> = {}): string {
  const search = new URLSearchParams();
  if (params.tab && params.tab !== "detalhes") search.set("aba", params.tab);
  if (params.typeId) search.set("tipo", params.typeId);
  if (params.page && params.page > 1) search.set("page", String(params.page));

  const query = search.toString();
  return query ? `/meus-pets/${uuid}?${query}` : `/meus-pets/${uuid}`;
}
