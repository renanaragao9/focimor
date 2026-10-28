import type { CampaignPayload, MyOng, OngMember, OngProfilePayload, OngRole, PostPayload } from "@/features/ong-panel/types/ongPanelTypes";
import type { OngCampaign, OngPost } from "@/features/ong/types/ongTypes";
import type { Pet, PetPayload } from "@/features/pet/types/petTypes";
import { api } from "@/lib/api/client";

function upload<T>(path: string, field: string, file: File): Promise<T> {
  const body = new FormData();
  body.set(field, file);
  return api<T>(path, { method: "POST", body });
}

export const ongPanelService = {
  updateProfile: (uuid: string, payload: OngProfilePayload) => api<MyOng>(`my-ongs/${uuid}`, { method: "PUT", json: payload }),
  uploadLogo: (uuid: string, file: File) => upload<MyOng>(`my-ongs/${uuid}/logo`, "logo", file),

  addMember: (uuid: string, email: string, role: OngRole) => api<OngMember>(`my-ongs/${uuid}/members`, { method: "POST", json: { email: email.trim(), role } }),
  setMemberRole: (uuid: string, userId: number, role: OngRole) => api<OngMember>(`my-ongs/${uuid}/members/${userId}`, { method: "PUT", json: { role } }),
  removeMember: (uuid: string, userId: number) => api<null>(`my-ongs/${uuid}/members/${userId}`, { method: "DELETE" }),

  createPost: (uuid: string, payload: PostPayload) => api<OngPost>(`my-ongs/${uuid}/posts`, { method: "POST", json: payload }),
  updatePost: (uuid: string, postUuid: string, payload: PostPayload) => api<OngPost>(`my-ongs/${uuid}/posts/${postUuid}`, { method: "PUT", json: payload }),
  removePost: (uuid: string, postUuid: string) => api<null>(`my-ongs/${uuid}/posts/${postUuid}`, { method: "DELETE" }),
  uploadCover: (uuid: string, postUuid: string, file: File) => upload<OngPost>(`my-ongs/${uuid}/posts/${postUuid}/cover`, "cover", file),

  createCampaign: (uuid: string, payload: CampaignPayload) => api<OngCampaign>(`my-ongs/${uuid}/campaigns`, { method: "POST", json: payload }),
  updateCampaign: (uuid: string, campaignUuid: string, payload: Partial<CampaignPayload>) => api<OngCampaign>(`my-ongs/${uuid}/campaigns/${campaignUuid}`, { method: "PUT", json: payload }),
  removeCampaign: (uuid: string, campaignUuid: string) => api<null>(`my-ongs/${uuid}/campaigns/${campaignUuid}`, { method: "DELETE" }),

  createPet: (uuid: string, payload: PetPayload) => api<Pet>(`my-ongs/${uuid}/pets`, { method: "POST", json: payload }),
};
