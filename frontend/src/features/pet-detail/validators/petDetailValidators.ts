import {
  EMPTY_DETAIL_FORM,
  type HealthRecord,
  type HealthRecordFormValues,
  type HealthRecordPayload,
  type PetDetail,
  type PetDetailFormValues,
  type PetDetailPayload,
  type Vaccine,
  type VaccineFormValues,
  type VaccinePayload,
  type Weight,
  type WeightFormValues,
  type WeightPayload,
} from "@/features/pet-detail/types/petDetailTypes";
import { brToIso, isIsoDate } from "@/lib/dates";
import { parseDecimal, toDecimalInput } from "@/lib/numbers";

const MIN_YEAR = 1900;
const MAX_SHORT = 255;
const MAX_MEDIUM = 2000;
const MAX_LONG = 5000;
const MAX_COST = 99_999_999.99;
const MAX_MEASURE = 9999.99;

function blankToNull(value: string): string | null {
  const trimmed = value.trim();
  return trimmed === "" ? null : trimmed;
}

function dateError(value: string, todayIso: string, required: boolean, requiredMessage: string): string | undefined {
  if (!value) return required ? requiredMessage : undefined;
  if (!isIsoDate(value) || Number(value.slice(0, 4)) < MIN_YEAR) return "Informe uma data válida.";
  if (value > todayIso) return "A data não pode ser futura.";
  return undefined;
}

function tooLong(value: string, max: number, label: string): string | undefined {
  return value.trim().length > max ? `${label} pode ter no máximo ${max} caracteres.` : undefined;
}

function compact<T extends Record<string, string | undefined>>(errors: T): Partial<T> {
  return Object.fromEntries(Object.entries(errors).filter(([, message]) => message !== undefined)) as Partial<T>;
}

export type DetailErrors = Partial<Record<keyof PetDetailFormValues, string>>;
export type VaccineErrors = Partial<Record<keyof VaccineFormValues, string>>;
export type HealthRecordErrors = Partial<Record<keyof HealthRecordFormValues, string>>;
export type WeightErrors = Partial<Record<keyof WeightFormValues, string>>;

export function validateDetail(values: PetDetailFormValues): DetailErrors {
  return compact({
    microchip: tooLong(values.microchip, MAX_SHORT, "O microchip"),
    pedigreeNumber: tooLong(values.pedigreeNumber, MAX_SHORT, "O número do pedigree"),
    coat: tooLong(values.coat, MAX_SHORT, "A pelagem"),
    bloodType: tooLong(values.bloodType, MAX_SHORT, "O tipo sanguíneo"),
    temperament: tooLong(values.temperament, MAX_SHORT, "O temperamento"),
    allergies: tooLong(values.allergies, MAX_MEDIUM, "As alergias"),
    chronicConditions: tooLong(values.chronicConditions, MAX_MEDIUM, "As condições crônicas"),
    diet: tooLong(values.diet, MAX_MEDIUM, "A alimentação"),
    vetName: tooLong(values.vetName, MAX_SHORT, "O nome do veterinário"),
    vetClinic: tooLong(values.vetClinic, MAX_SHORT, "A clínica"),
    vetPhone: tooLong(values.vetPhone, MAX_SHORT, "O telefone"),
    notes: tooLong(values.notes, MAX_LONG, "As observações"),
  });
}

export function toDetailPayload(values: PetDetailFormValues): PetDetailPayload {
  return {
    microchip: blankToNull(values.microchip),
    pedigree_number: blankToNull(values.pedigreeNumber),
    coat: blankToNull(values.coat),
    blood_type: blankToNull(values.bloodType),
    temperament: blankToNull(values.temperament),
    allergies: blankToNull(values.allergies),
    chronic_conditions: blankToNull(values.chronicConditions),
    diet: blankToNull(values.diet),
    vet_name: blankToNull(values.vetName),
    vet_clinic: blankToNull(values.vetClinic),
    vet_phone: blankToNull(values.vetPhone),
    notes: blankToNull(values.notes),
  };
}

export function detailToFormValues(detail: PetDetail | null): PetDetailFormValues {
  if (!detail) return EMPTY_DETAIL_FORM;

  return {
    microchip: detail.microchip ?? "",
    pedigreeNumber: detail.pedigree_number ?? "",
    coat: detail.coat ?? "",
    bloodType: detail.blood_type ?? "",
    temperament: detail.temperament ?? "",
    allergies: detail.allergies ?? "",
    chronicConditions: detail.chronic_conditions ?? "",
    diet: detail.diet ?? "",
    vetName: detail.vet_name ?? "",
    vetClinic: detail.vet_clinic ?? "",
    vetPhone: detail.vet_phone ?? "",
    notes: detail.notes ?? "",
  };
}

export const DETAIL_API_FIELDS: ReadonlyArray<readonly [keyof PetDetailFormValues, string]> = [
  ["microchip", "microchip"],
  ["pedigreeNumber", "pedigree_number"],
  ["coat", "coat"],
  ["bloodType", "blood_type"],
  ["temperament", "temperament"],
  ["allergies", "allergies"],
  ["chronicConditions", "chronic_conditions"],
  ["diet", "diet"],
  ["vetName", "vet_name"],
  ["vetClinic", "vet_clinic"],
  ["vetPhone", "vet_phone"],
  ["notes", "notes"],
];

export function validateVaccine(values: VaccineFormValues, todayIso: string): VaccineErrors {
  const name = values.name.trim();
  const appliedError = dateError(values.appliedAt, todayIso, true, "Informe a data de aplicação.");
  let nextDoseError: string | undefined;

  if (values.nextDoseAt) {
    if (!isIsoDate(values.nextDoseAt) || Number(values.nextDoseAt.slice(0, 4)) < MIN_YEAR) nextDoseError = "Informe uma data válida.";
    else if (!appliedError && values.nextDoseAt <= values.appliedAt) nextDoseError = "A próxima dose deve ser depois da aplicação.";
  }

  return compact({
    name: !name ? "Informe o nome da vacina." : tooLong(name, MAX_SHORT, "O nome"),
    appliedAt: appliedError,
    nextDoseAt: nextDoseError,
    batch: tooLong(values.batch, MAX_SHORT, "O lote"),
    veterinarian: tooLong(values.veterinarian, MAX_SHORT, "O nome do veterinário"),
    notes: tooLong(values.notes, MAX_MEDIUM, "As observações"),
  });
}

export function toVaccinePayload(values: VaccineFormValues): VaccinePayload {
  return {
    name: values.name.trim(),
    applied_at: values.appliedAt,
    next_dose_at: values.nextDoseAt || null,
    batch: blankToNull(values.batch),
    veterinarian: blankToNull(values.veterinarian),
    notes: blankToNull(values.notes),
  };
}

export function vaccineToFormValues(vaccine: Vaccine): VaccineFormValues {
  return {
    name: vaccine.name,
    appliedAt: brToIso(vaccine.applied_at),
    nextDoseAt: brToIso(vaccine.next_dose_at),
    batch: vaccine.batch ?? "",
    veterinarian: vaccine.veterinarian ?? "",
    notes: vaccine.notes ?? "",
  };
}

export const VACCINE_API_FIELDS: ReadonlyArray<readonly [keyof VaccineFormValues, string]> = [
  ["name", "name"],
  ["appliedAt", "applied_at"],
  ["nextDoseAt", "next_dose_at"],
  ["batch", "batch"],
  ["veterinarian", "veterinarian"],
  ["notes", "notes"],
];

export function validateHealthRecord(values: HealthRecordFormValues, todayIso: string): HealthRecordErrors {
  const title = values.title.trim();
  const performedError = dateError(values.performedAt, todayIso, true, "Informe a data.");
  let nextDueError: string | undefined;
  let costError: string | undefined;

  if (values.nextDueAt) {
    if (!isIsoDate(values.nextDueAt) || Number(values.nextDueAt.slice(0, 4)) < MIN_YEAR) nextDueError = "Informe uma data válida.";
    else if (!performedError && values.nextDueAt <= values.performedAt) nextDueError = "O retorno deve ser depois da realização.";
  }

  if (values.cost.trim()) {
    const cost = parseDecimal(values.cost);
    if (cost === null) costError = "Informe um valor válido.";
    else if (cost > MAX_COST) costError = "O valor é alto demais.";
  }

  return compact({
    typeId: values.typeId ? undefined : "Selecione o tipo.",
    title: !title ? "Informe o título." : tooLong(title, MAX_SHORT, "O título"),
    performedAt: performedError,
    nextDueAt: nextDueError,
    veterinarian: tooLong(values.veterinarian, MAX_SHORT, "O nome do veterinário"),
    cost: costError,
    description: tooLong(values.description, MAX_LONG, "A descrição"),
  });
}

export function toHealthRecordPayload(values: HealthRecordFormValues): HealthRecordPayload {
  return {
    health_record_type_id: Number(values.typeId),
    title: values.title.trim(),
    performed_at: values.performedAt,
    next_due_at: values.nextDueAt || null,
    veterinarian: blankToNull(values.veterinarian),
    cost: values.cost.trim() ? parseDecimal(values.cost) : null,
    description: blankToNull(values.description),
  };
}

export function healthRecordToFormValues(record: HealthRecord): HealthRecordFormValues {
  return {
    typeId: record.type ? String(record.type.id) : "",
    title: record.title,
    performedAt: brToIso(record.performed_at),
    nextDueAt: brToIso(record.next_due_at),
    veterinarian: record.veterinarian ?? "",
    cost: toDecimalInput(record.cost),
    description: record.description ?? "",
  };
}

export const HEALTH_RECORD_API_FIELDS: ReadonlyArray<readonly [keyof HealthRecordFormValues, string]> = [
  ["typeId", "health_record_type_id"],
  ["title", "title"],
  ["performedAt", "performed_at"],
  ["nextDueAt", "next_due_at"],
  ["veterinarian", "veterinarian"],
  ["cost", "cost"],
  ["description", "description"],
];

function measureError(value: string, required: boolean, requiredMessage: string): string | undefined {
  if (!value.trim()) return required ? requiredMessage : undefined;

  const parsed = parseDecimal(value);
  if (parsed === null || parsed <= 0) return "Informe um valor maior que zero.";
  if (parsed > MAX_MEASURE) return "O valor é alto demais.";
  return undefined;
}

export function validateWeight(values: WeightFormValues, todayIso: string): WeightErrors {
  return compact({
    weightKg: measureError(values.weightKg, true, "Informe o peso."),
    heightCm: measureError(values.heightCm, false, ""),
    widthCm: measureError(values.widthCm, false, ""),
    measuredAt: dateError(values.measuredAt, todayIso, true, "Informe a data da medição."),
  });
}

export function toWeightPayload(values: WeightFormValues): WeightPayload {
  return {
    weight_kg: parseDecimal(values.weightKg) ?? 0,
    height_cm: values.heightCm.trim() ? parseDecimal(values.heightCm) : null,
    width_cm: values.widthCm.trim() ? parseDecimal(values.widthCm) : null,
    measured_at: values.measuredAt,
  };
}

export function weightToFormValues(weight: Weight): WeightFormValues {
  return {
    weightKg: toDecimalInput(weight.weight_kg),
    heightCm: toDecimalInput(weight.height_cm),
    widthCm: toDecimalInput(weight.width_cm),
    measuredAt: brToIso(weight.measured_at),
  };
}

export const WEIGHT_API_FIELDS: ReadonlyArray<readonly [keyof WeightFormValues, string]> = [
  ["weightKg", "weight_kg"],
  ["heightCm", "height_cm"],
  ["widthCm", "width_cm"],
  ["measuredAt", "measured_at"],
];
