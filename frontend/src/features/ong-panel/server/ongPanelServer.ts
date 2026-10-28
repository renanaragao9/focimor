import "server-only";

import type { MyOng, OngMember } from "@/features/ong-panel/types/ongPanelTypes";
import { panelQuery } from "@/features/ong-panel/utils/panelParams";
import type { OngCampaign, OngPost } from "@/features/ong/types/ongTypes";
import type { Pet } from "@/features/pet/types/petTypes";
import { apiServer, apiServerPage } from "@/lib/api/server";

export function fetchMyOngs() {
  return apiServer<MyOng[]>("my-ongs");
}

export function fetchMyOng(uuid: string) {
  return apiServer<MyOng>(`my-ongs/${uuid}`);
}

export function fetchOngMembers(uuid: string) {
  return apiServer<OngMember[]>(`my-ongs/${uuid}/members`);
}

export function fetchOngPanelPets(uuid: string, page: number) {
  return apiServerPage<Pet>(`my-ongs/${uuid}/pets`, { query: panelQuery(page) });
}

export function fetchOngPanelPosts(uuid: string, page: number) {
  return apiServerPage<OngPost>(`my-ongs/${uuid}/posts`, { query: panelQuery(page) });
}

export function fetchOngPanelCampaigns(uuid: string, page: number) {
  return apiServerPage<OngCampaign>(`my-ongs/${uuid}/campaigns`, { query: panelQuery(page) });
}

export function fetchOngPanelPost(uuid: string, postUuid: string) {
  return apiServer<OngPost>(`my-ongs/${uuid}/posts/${postUuid}`);
}
