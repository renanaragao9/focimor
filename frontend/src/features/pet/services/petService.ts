import type { Breed, Pet, PetPayload, PetPhoto } from "@/features/pet/types/petTypes";
import { api } from "@/lib/api/client";

export const petService = {
  create: (payload: PetPayload) => api<Pet>("pets", { method: "POST", json: payload }),
  update: (uuid: string, payload: PetPayload) => api<Pet>(`pets/${uuid}`, { method: "PUT", json: payload }),
  remove: (uuid: string) => api<null>(`pets/${uuid}`, { method: "DELETE" }),
  breeds: (speciesId: number, search = "") => api<Breed[]>("selects/breeds", { query: { species_id: speciesId, search: search || undefined } }),
  photos: (uuid: string) => api<PetPhoto[]>(`pets/${uuid}/photos`),
  uploadPhoto: (uuid: string, file: File) => {
    const body = new FormData();
    body.set("photo", file);
    return api<PetPhoto>(`pets/${uuid}/photos`, { method: "POST", body });
  },
  setPrimaryPhoto: (uuid: string, photoId: number) => api<PetPhoto>(`pets/${uuid}/photos/${photoId}`, { method: "PUT", json: { is_primary: true } }),
  removePhoto: (uuid: string, photoId: number) => api<null>(`pets/${uuid}/photos/${photoId}`, { method: "DELETE" }),
};
