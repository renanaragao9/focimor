import { describe, expect, it } from "vitest";

import { EMPTY_PET_FORM, type Pet, type PetFormValues } from "@/features/pet/types/petTypes";
import { petToFormValues, toPetPayload, validatePetForm } from "@/features/pet/validators/petValidators";

const TODAY = "2026-10-27";
const valid: PetFormValues = { ...EMPTY_PET_FORM, name: "Thor", gender: "male", speciesId: "1", goalId: "2" };

describe("validatePetForm", () => {
  it("aceita o mínimo obrigatório", () => {
    expect(validatePetForm(valid, TODAY)).toEqual({});
  });

  it("aponta os obrigatórios vazios", () => {
    expect(Object.keys(validatePetForm(EMPTY_PET_FORM, TODAY)).sort()).toEqual(["gender", "goalId", "name", "speciesId"]);
  });

  it("recusa nome só com espaços ou longo demais", () => {
    expect(validatePetForm({ ...valid, name: "   " }, TODAY).name).toBe("Informe o nome do pet.");
    expect(validatePetForm({ ...valid, name: "a".repeat(256) }, TODAY).name).toContain("255");
  });

  it("valida a data de nascimento", () => {
    expect(validatePetForm({ ...valid, birthDate: "2026-10-28" }, TODAY).birthDate).toBe("A data de nascimento não pode ser futura.");
    expect(validatePetForm({ ...valid, birthDate: "2026-10-27" }, TODAY).birthDate).toBeUndefined();
    expect(validatePetForm({ ...valid, birthDate: "1850-01-01" }, TODAY).birthDate).toBe("Informe uma data válida.");
    expect(validatePetForm({ ...valid, birthDate: "2023-02-30" }, TODAY).birthDate).toBe("Informe uma data válida.");
  });

  it("limita a cor a 50 caracteres", () => {
    expect(validatePetForm({ ...valid, color: "a".repeat(51) }, TODAY).color).toContain("50");
    expect(validatePetForm({ ...valid, color: "a".repeat(50) }, TODAY).color).toBeUndefined();
  });

  it("limita a bio a 2000 caracteres", () => {
    expect(validatePetForm({ ...valid, bio: "a".repeat(2001) }, TODAY).bio).toContain("2000");
    expect(validatePetForm({ ...valid, bio: "a".repeat(2000) }, TODAY).bio).toBeUndefined();
  });
});

describe("toPetPayload", () => {
  it("converte para os nomes e tipos da API", () => {
    expect(toPetPayload({ ...valid, name: "  Thor ", breedId: "5", sizeId: "3", birthDate: "2022-05-10", color: " Caramelo ", bio: "  Dócil ", isVaccinated: true })).toEqual({
      name: "Thor",
      gender: "male",
      birth_date: "2022-05-10",
      species_id: 1,
      breed_id: 5,
      size_id: 3,
      goal_id: 2,
      color: "Caramelo",
      bio: "Dócil",
      is_neutered: false,
      is_vaccinated: true,
      is_active: true,
    });
  });

  it("envia nulo para os opcionais vazios (permite limpar o campo)", () => {
    const payload = toPetPayload(valid);

    expect(payload.breed_id).toBeNull();
    expect(payload.size_id).toBeNull();
    expect(payload.birth_date).toBeNull();
    expect(payload.bio).toBeNull();
    expect(payload.color).toBeNull();
  });
});

describe("petToFormValues", () => {
  const pet: Pet = {
    uuid: "u",
    name: "Luna",
    gender: "female",
    color: null,
    bio: null,
    birth_date: "10/05/2022",
    is_neutered: true,
    is_vaccinated: false,
    is_active: true,
    species: { id: 2, name: "Gato" },
    breed: null,
    size: { id: 1, name: "Pequeno" },
    goal: { id: 3, name: "Amizade" },
    photos: [],
  };

  it("preenche o formulário a partir do pet da API", () => {
    expect(petToFormValues(pet)).toEqual({
      name: "Luna",
      gender: "female",
      birthDate: "2022-05-10",
      speciesId: "2",
      breedId: "",
      sizeId: "1",
      goalId: "3",
      color: "",
      bio: "",
      isNeutered: true,
      isVaccinated: false,
      isActive: true,
    });
  });

  it("ida e volta gera o payload equivalente", () => {
    expect(toPetPayload(petToFormValues(pet))).toMatchObject({ name: "Luna", gender: "female", birth_date: "2022-05-10", species_id: 2, size_id: 1, goal_id: 3, breed_id: null });
  });
});
