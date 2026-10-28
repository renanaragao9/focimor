import { MATCHES_PER_PAGE } from "@/features/chat/types/chatTypes";
import type { QueryParams } from "@/lib/api/types";
import { positiveInt, readParam, type RawSearchParams } from "@/lib/searchParams";
import { isUuid } from "@/lib/uuid";

export interface MatchesParams {
  petUuid: string;
  page: number;
}

export function parseMatchesParams(raw: RawSearchParams): MatchesParams {
  const pet = readParam(raw.pet);

  return { petUuid: isUuid(pet) ? pet : "", page: positiveInt(readParam(raw.page)) ?? 1 };
}

export function matchesToQuery(params: MatchesParams): QueryParams {
  return { page: params.page, per_page: MATCHES_PER_PAGE, sort: "matched_at", direction: "desc", "filters[pet_uuid]": params.petUuid || undefined };
}

export function matchesHref(params: Partial<MatchesParams> = {}): string {
  const search = new URLSearchParams();
  if (params.petUuid) search.set("pet", params.petUuid);
  if (params.page && params.page > 1) search.set("page", String(params.page));

  const query = search.toString();
  return query ? `/matches?${query}` : "/matches";
}

export function chatHref(matchUuid: string): string {
  return `/matches/${matchUuid}`;
}
