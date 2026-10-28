import { describe, expect, it } from "vitest";

import {
  EMPTY_DETAIL_FORM,
  EMPTY_HEALTH_RECORD_FORM,
  EMPTY_VACCINE_FORM,
  EMPTY_WEIGHT_FORM,
  type HealthRecord,
  type PetDetail,
  type Vaccine,
  type Weight,
} from "@/features/pet-detail/types/petDetailTypes";
import {
  detailToFormValues,
  healthRecordToFormValues,
  toDetailPayload,
  toHealthRecordPayload,
  toVaccinePayload,
  toWeightPayload,
  vaccineToFormValues,
  validateDetail,
  validateHealthRecord,
  validateVaccine,
  validateWeight,
  weightToFormValues,
} from "@/features/pet-detail/validators/petDetailValidators";

const TODAY = "2026-10-27";

describe("vacina", () => {
  const valid = { ...EMPTY_VACCINE_FORM, name: "V10", appliedAt: "2026-01-10" };

  it("aceita o mínimo e a próxima dose depois da aplicação", () => {
    expect(validateVaccine(valid, TODAY)).toEqual({});
    expect(validateVaccine({ ...valid, nextDoseAt: "2027-01-10" }, TODAY)).toEqual({});
  });

  it("exige nome e data de aplicação", () => {
    expect(Object.keys(validateVaccine(EMPTY_VACCINE_FORM, TODAY)).sort()).toEqual(["appliedAt", "name"]);
  });

  it("recusa aplicação futura e próxima dose antes ou igual à aplicação", () => {
    expect(validateVaccine({ ...valid, appliedAt: "2026-10-28" }, TODAY).appliedAt).toBe("A data não pode ser futura.");
    expect(validateVaccine({ ...valid, nextDoseAt: "2026-01-10" }, TODAY).nextDoseAt).toBe("A próxima dose deve ser depois da aplicação.");
    expect(validateVaccine({ ...valid, nextDoseAt: "2025-12-01" }, TODAY).nextDoseAt).toBe("A próxima dose deve ser depois da aplicação.");
  });

  it("aceita próxima dose no futuro", () => {
    expect(validateVaccine({ ...valid, nextDoseAt: "2030-01-01" }, TODAY).nextDoseAt).toBeUndefined();
  });

  it("monta o payload com nulos nos opcionais e faz a ida e volta", () => {
    expect(toVaccinePayload({ ...valid, name: " V10 ", batch: " " })).toEqual({ name: "V10", applied_at: "2026-01-10", next_dose_at: null, batch: null, veterinarian: null, notes: null });

    const vaccine: Vaccine = { id: 1, name: "Gripe", applied_at: "10/01/2026", next_dose_at: "10/01/2027", batch: "L1", veterinarian: null, notes: null };
    expect(toVaccinePayload(vaccineToFormValues(vaccine))).toMatchObject({ name: "Gripe", applied_at: "2026-01-10", next_dose_at: "2027-01-10", batch: "L1", veterinarian: null });
  });
});

describe("registro de saúde", () => {
  const valid = { ...EMPTY_HEALTH_RECORD_FORM, typeId: "1", title: "Consulta", performedAt: "2026-02-01" };

  it("aceita o mínimo", () => {
    expect(validateHealthRecord(valid, TODAY)).toEqual({});
  });

  it("exige tipo, título e data", () => {
    expect(Object.keys(validateHealthRecord(EMPTY_HEALTH_RECORD_FORM, TODAY)).sort()).toEqual(["performedAt", "title", "typeId"]);
  });

  it("valida o retorno e o custo", () => {
    expect(validateHealthRecord({ ...valid, nextDueAt: "2026-02-01" }, TODAY).nextDueAt).toBe("O retorno deve ser depois da realização.");
    expect(validateHealthRecord({ ...valid, cost: "abc" }, TODAY).cost).toBe("Informe um valor válido.");
    expect(validateHealthRecord({ ...valid, cost: "1,5" }, TODAY).cost).toBeUndefined();
    expect(validateHealthRecord({ ...valid, cost: "999999999" }, TODAY).cost).toBe("O valor é alto demais.");
  });

  it("converte custo com vírgula para número", () => {
    expect(toHealthRecordPayload({ ...valid, cost: "150,5" }).cost).toBe(150.5);
    expect(toHealthRecordPayload(valid)).toMatchObject({ health_record_type_id: 1, title: "Consulta", cost: null, next_due_at: null });
  });

  it("preenche o formulário a partir do registro da API", () => {
    const record: HealthRecord = { id: 1, title: "Exame", performed_at: "01/02/2026", next_due_at: null, veterinarian: "Ana", cost: "150.50", description: null, type: { id: 2, name: "Exame" } };

    expect(healthRecordToFormValues(record)).toEqual({ typeId: "2", title: "Exame", performedAt: "2026-02-01", nextDueAt: "", veterinarian: "Ana", cost: "150,5", description: "" });
  });
});

describe("peso e medidas", () => {
  const valid = { ...EMPTY_WEIGHT_FORM, weightKg: "12,5", measuredAt: "2026-01-10" };

  it("aceita peso com vírgula e altura e largura opcionais", () => {
    expect(validateWeight(valid, TODAY)).toEqual({});
    expect(validateWeight({ ...valid, heightCm: "40", widthCm: "25,5" }, TODAY)).toEqual({});
  });

  it("exige peso e data", () => {
    expect(Object.keys(validateWeight(EMPTY_WEIGHT_FORM, TODAY)).sort()).toEqual(["measuredAt", "weightKg"]);
  });

  it("recusa zero, texto, valores enormes e data futura", () => {
    expect(validateWeight({ ...valid, weightKg: "0" }, TODAY).weightKg).toBe("Informe um valor maior que zero.");
    expect(validateWeight({ ...valid, weightKg: "abc" }, TODAY).weightKg).toBe("Informe um valor maior que zero.");
    expect(validateWeight({ ...valid, weightKg: "10000" }, TODAY).weightKg).toBe("O valor é alto demais.");
    expect(validateWeight({ ...valid, heightCm: "-3" }, TODAY).heightCm).toBe("Informe um valor maior que zero.");
    expect(validateWeight({ ...valid, measuredAt: "2026-10-28" }, TODAY).measuredAt).toBe("A data não pode ser futura.");
  });

  it("monta o payload numérico e faz a ida e volta", () => {
    expect(toWeightPayload({ ...valid, heightCm: "40" })).toEqual({ weight_kg: 12.5, height_cm: 40, width_cm: null, measured_at: "2026-01-10" });

    const weight: Weight = { id: 1, weight_kg: "12.50", height_cm: null, width_cm: "25.50", measured_at: "10/01/2026" };
    expect(weightToFormValues(weight)).toEqual({ weightKg: "12,5", heightCm: "", widthCm: "25,5", measuredAt: "2026-01-10" });
  });
});

describe("ficha do pet", () => {
  it("aceita tudo vazio e limita os tamanhos", () => {
    expect(validateDetail(EMPTY_DETAIL_FORM)).toEqual({});
    expect(validateDetail({ ...EMPTY_DETAIL_FORM, microchip: "a".repeat(256) }).microchip).toContain("255");
    expect(validateDetail({ ...EMPTY_DETAIL_FORM, allergies: "a".repeat(2001) }).allergies).toContain("2000");
    expect(validateDetail({ ...EMPTY_DETAIL_FORM, notes: "a".repeat(5001) }).notes).toContain("5000");
  });

  it("envia nulo para campos vazios (permite limpar)", () => {
    const payload = toDetailPayload({ ...EMPTY_DETAIL_FORM, microchip: " 123 ", vetName: "  " });

    expect(payload.microchip).toBe("123");
    expect(payload.vet_name).toBeNull();
    expect(Object.values(payload).filter((value) => value === null)).toHaveLength(11);
  });

  it("preenche o formulário, inclusive sem ficha ainda", () => {
    expect(detailToFormValues(null)).toEqual(EMPTY_DETAIL_FORM);

    const detail: PetDetail = { ...toDetailPayload({ ...EMPTY_DETAIL_FORM, coat: "Curta", vetClinic: "Vida Animal" }), updated_at: "01/01/2026 10:00:00" };
    expect(detailToFormValues(detail)).toMatchObject({ coat: "Curta", vetClinic: "Vida Animal", microchip: "" });
  });
});
