import { ONG_PETS_PER_PAGE, ONGS_PER_PAGE, POSTS_PER_PAGE, type OngTab } from "@/features/ong/types/ongTypes";
import type { QueryParams } from "@/lib/api/types";
import { positiveInt, readParam, type RawSearchParams } from "@/lib/searchParams";

const MAX_SEARCH_LENGTH = 100;

export interface ListParams {
  search: string;
  page: number;
}

export interface OngDetailParams {
  tab: OngTab;
  page: number;
}

export function parseListParams(raw: RawSearchParams): ListParams {
  return { search: readParam(raw.search).trim().slice(0, MAX_SEARCH_LENGTH), page: positiveInt(readParam(raw.page)) ?? 1 };
}

export function parseOngDetailParams(raw: RawSearchParams): OngDetailParams {
  const tab = readParam(raw.aba);

  return { tab: tab === "posts" || tab === "caixinhas" ? tab : "pets", page: positiveInt(readParam(raw.page)) ?? 1 };
}

export function ongsToQuery(params: ListParams): QueryParams {
  return { page: params.page, per_page: ONGS_PER_PAGE, sort: "name", direction: "asc", search: params.search || undefined };
}

export function postsToQuery(params: ListParams): QueryParams {
  return { page: params.page, per_page: POSTS_PER_PAGE, sort: "published_at", direction: "desc", search: params.search || undefined };
}

export function ongPetsToQuery(page: number): QueryParams {
  return { page, per_page: ONG_PETS_PER_PAGE };
}

export function ongPostsToQuery(page: number): QueryParams {
  return { page, per_page: POSTS_PER_PAGE, sort: "published_at", direction: "desc" };
}

export function listHref(basePath: "/ongs" | "/posts", params: Partial<ListParams> = {}): string {
  const search = new URLSearchParams();
  if (params.search) search.set("search", params.search);
  if (params.page && params.page > 1) search.set("page", String(params.page));

  const query = search.toString();
  return query ? `${basePath}?${query}` : basePath;
}

export function ongHref(uuid: string, params: Partial<OngDetailParams> = {}): string {
  const search = new URLSearchParams();
  if (params.tab && params.tab !== "pets") search.set("aba", params.tab);
  if (params.page && params.page > 1) search.set("page", String(params.page));

  const query = search.toString();
  return query ? `/ongs/${uuid}?${query}` : `/ongs/${uuid}`;
}

export function postHref(uuid: string): string {
  return `/posts/${uuid}`;
}
