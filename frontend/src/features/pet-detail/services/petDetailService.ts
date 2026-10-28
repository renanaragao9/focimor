import type { HealthRecord, HealthRecordPayload, PetDetail, PetDetailPayload, Vaccine, VaccinePayload, Weight, WeightPayload } from "@/features/pet-detail/types/petDetailTypes";
import { api } from "@/lib/api/client";

export const detailService = {
  save: (petUuid: string, payload: PetDetailPayload) => api<PetDetail>(`pets/${petUuid}/detail`, { method: "PUT", json: payload }),
};

export const vaccineService = {
  create: (petUuid: string, payload: VaccinePayload) => api<Vaccine>(`pets/${petUuid}/vaccines`, { method: "POST", json: payload }),
  update: (petUuid: string, id: number, payload: VaccinePayload) => api<Vaccine>(`pets/${petUuid}/vaccines/${id}`, { method: "PUT", json: payload }),
  remove: (petUuid: string, id: number) => api<null>(`pets/${petUuid}/vaccines/${id}`, { method: "DELETE" }),
};

export const healthRecordService = {
  create: (petUuid: string, payload: HealthRecordPayload) => api<HealthRecord>(`pets/${petUuid}/health-records`, { method: "POST", json: payload }),
  update: (petUuid: string, id: number, payload: HealthRecordPayload) => api<HealthRecord>(`pets/${petUuid}/health-records/${id}`, { method: "PUT", json: payload }),
  remove: (petUuid: string, id: number) => api<null>(`pets/${petUuid}/health-records/${id}`, { method: "DELETE" }),
};

export const weightService = {
  create: (petUuid: string, payload: WeightPayload) => api<Weight>(`pets/${petUuid}/weights`, { method: "POST", json: payload }),
  update: (petUuid: string, id: number, payload: WeightPayload) => api<Weight>(`pets/${petUuid}/weights/${id}`, { method: "PUT", json: payload }),
  remove: (petUuid: string, id: number) => api<null>(`pets/${petUuid}/weights/${id}`, { method: "DELETE" }),
};
