import { ADOPTION_STATUSES, ADOPTIONS_PER_PAGE, type AdoptionsParams, type AdoptionStatus } from "@/features/adoption/types/adoptionTypes";
import type { QueryParams } from "@/lib/api/types";
import { positiveInt, readParam, type RawSearchParams } from "@/lib/searchParams";

export function parseAdoptionsParams(raw: RawSearchParams): AdoptionsParams {
  const status = readParam(raw.status);

  return {
    tab: readParam(raw.aba) === "recebidos" ? "recebidos" : "meus",
    status: (ADOPTION_STATUSES as readonly string[]).includes(status) ? (status as AdoptionStatus) : "",
    page: positiveInt(readParam(raw.page)) ?? 1,
  };
}

export function adoptionsToQuery(params: AdoptionsParams): QueryParams {
  return { page: params.page, per_page: ADOPTIONS_PER_PAGE, sort: "created_at", direction: "desc", "filters[status]": params.status || undefined };
}

export function adoptionsHref(params: Partial<AdoptionsParams> = {}): string {
  const search = new URLSearchParams();
  if (params.tab === "recebidos") search.set("aba", "recebidos");
  if (params.status) search.set("status", params.status);
  if (params.page && params.page > 1) search.set("page", String(params.page));

  const query = search.toString();
  return query ? `/adocoes?${query}` : "/adocoes";
}

export function adoptionHref(uuid: string): string {
  return `/adocoes/${uuid}`;
}
