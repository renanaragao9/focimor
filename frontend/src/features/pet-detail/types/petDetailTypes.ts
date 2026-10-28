import type { CatalogItem, Pet } from "@/features/pet/types/petTypes";

export type PetTab = "detalhes" | "vacinas" | "saude" | "pesos";

export const PET_TABS: ReadonlyArray<{ key: PetTab; label: string }> = [
  { key: "detalhes", label: "Detalhes" },
  { key: "vacinas", label: "Vacinas" },
  { key: "saude", label: "Saúde" },
  { key: "pesos", label: "Pesos e medidas" },
];

export const RECORDS_PER_PAGE = 10;

export interface PetDetail {
  microchip: string | null;
  pedigree_number: string | null;
  coat: string | null;
  blood_type: string | null;
  temperament: string | null;
  allergies: string | null;
  chronic_conditions: string | null;
  diet: string | null;
  vet_name: string | null;
  vet_clinic: string | null;
  vet_phone: string | null;
  notes: string | null;
  updated_at: string | null;
}

export type PetWithDetail = Pet & { detail: PetDetail | null };

export interface Vaccine {
  id: number;
  name: string;
  applied_at: string;
  next_dose_at: string | null;
  batch: string | null;
  veterinarian: string | null;
  notes: string | null;
}

export interface HealthRecord {
  id: number;
  title: string;
  performed_at: string;
  next_due_at: string | null;
  veterinarian: string | null;
  cost: string | null;
  description: string | null;
  type?: { id: number; name: string };
}

export interface Weight {
  id: number;
  weight_kg: string;
  height_cm: string | null;
  width_cm: string | null;
  measured_at: string;
}

export type HealthRecordType = CatalogItem;

export interface PetDetailFormValues {
  microchip: string;
  pedigreeNumber: string;
  coat: string;
  bloodType: string;
  temperament: string;
  allergies: string;
  chronicConditions: string;
  diet: string;
  vetName: string;
  vetClinic: string;
  vetPhone: string;
  notes: string;
}

export const EMPTY_DETAIL_FORM: PetDetailFormValues = {
  microchip: "",
  pedigreeNumber: "",
  coat: "",
  bloodType: "",
  temperament: "",
  allergies: "",
  chronicConditions: "",
  diet: "",
  vetName: "",
  vetClinic: "",
  vetPhone: "",
  notes: "",
};

export type PetDetailPayload = Record<keyof Omit<PetDetail, "updated_at">, string | null>;

export interface VaccineFormValues {
  name: string;
  appliedAt: string;
  nextDoseAt: string;
  batch: string;
  veterinarian: string;
  notes: string;
}

export const EMPTY_VACCINE_FORM: VaccineFormValues = { name: "", appliedAt: "", nextDoseAt: "", batch: "", veterinarian: "", notes: "" };

export interface VaccinePayload {
  name: string;
  applied_at: string;
  next_dose_at: string | null;
  batch: string | null;
  veterinarian: string | null;
  notes: string | null;
}

export interface HealthRecordFormValues {
  typeId: string;
  title: string;
  performedAt: string;
  nextDueAt: string;
  veterinarian: string;
  cost: string;
  description: string;
}

export const EMPTY_HEALTH_RECORD_FORM: HealthRecordFormValues = { typeId: "", title: "", performedAt: "", nextDueAt: "", veterinarian: "", cost: "", description: "" };

export interface HealthRecordPayload {
  health_record_type_id: number;
  title: string;
  performed_at: string;
  next_due_at: string | null;
  veterinarian: string | null;
  cost: number | null;
  description: string | null;
}

export interface WeightFormValues {
  weightKg: string;
  heightCm: string;
  widthCm: string;
  measuredAt: string;
}

export const EMPTY_WEIGHT_FORM: WeightFormValues = { weightKg: "", heightCm: "", widthCm: "", measuredAt: "" };

export interface WeightPayload {
  weight_kg: number;
  height_cm: number | null;
  width_cm: number | null;
  measured_at: string;
}
