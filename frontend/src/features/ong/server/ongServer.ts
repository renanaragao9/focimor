import "server-only";

import type { AdoptablePet, Ong, OngCampaign, OngPost } from "@/features/ong/types/ongTypes";
import { CAMPAIGNS_PER_PAGE } from "@/features/ong/types/ongTypes";
import { ongPetsToQuery, ongPostsToQuery, ongsToQuery, postsToQuery, type ListParams } from "@/features/ong/utils/ongParams";
import { apiPublic, apiPublicPage } from "@/lib/api/server";

const REVALIDATE_SECONDS = 60;

export function fetchOngs(params: ListParams) {
  return apiPublicPage<Ong>("ongs", { query: ongsToQuery(params) }, REVALIDATE_SECONDS);
}

export function fetchOng(uuid: string) {
  return apiPublic<Ong>(`ongs/${uuid}`, {}, REVALIDATE_SECONDS);
}

export function fetchOngPets(uuid: string, page: number) {
  return apiPublicPage<AdoptablePet>(`ongs/${uuid}/pets`, { query: ongPetsToQuery(page) }, REVALIDATE_SECONDS);
}

export function fetchOngPosts(uuid: string, page: number) {
  return apiPublicPage<OngPost>(`ongs/${uuid}/posts`, { query: ongPostsToQuery(page) }, REVALIDATE_SECONDS);
}

export function fetchOngCampaigns(uuid: string, page: number) {
  return apiPublicPage<OngCampaign>(`ongs/${uuid}/campaigns`, { query: { page, per_page: CAMPAIGNS_PER_PAGE } }, REVALIDATE_SECONDS);
}

export function fetchPosts(params: ListParams) {
  return apiPublicPage<OngPost>("posts", { query: postsToQuery(params) }, REVALIDATE_SECONDS);
}

export function fetchPost(uuid: string) {
  return apiPublic<OngPost>(`posts/${uuid}`, {}, REVALIDATE_SECONDS);
}
