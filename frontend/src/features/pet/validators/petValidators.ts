import type { Pet, PetFormValues, PetPayload } from "@/features/pet/types/petTypes";
import { brToIso, isIsoDate } from "@/lib/dates";

export type PetFormErrors = Partial<Record<keyof PetFormValues, string>>;

const MIN_BIRTH_YEAR = 1900;
const MAX_BIO_LENGTH = 2000;
const MAX_COLOR_LENGTH = 50;
const MAX_NAME_LENGTH = 255;

export function validatePetForm(values: PetFormValues, todayIso: string): PetFormErrors {
  const errors: PetFormErrors = {};
  const name = values.name.trim();

  if (!name) errors.name = "Informe o nome do pet.";
  else if (name.length > MAX_NAME_LENGTH) errors.name = `O nome pode ter no máximo ${MAX_NAME_LENGTH} caracteres.`;

  if (!values.gender) errors.gender = "Selecione o sexo.";
  if (!values.speciesId) errors.speciesId = "Selecione a espécie.";
  if (!values.goalId) errors.goalId = "Selecione o objetivo.";

  if (values.birthDate) {
    if (!isIsoDate(values.birthDate) || Number(values.birthDate.slice(0, 4)) < MIN_BIRTH_YEAR) errors.birthDate = "Informe uma data válida.";
    else if (values.birthDate > todayIso) errors.birthDate = "A data de nascimento não pode ser futura.";
  }

  if (values.color.trim().length > MAX_COLOR_LENGTH) errors.color = `A cor pode ter no máximo ${MAX_COLOR_LENGTH} caracteres.`;
  if (values.bio.length > MAX_BIO_LENGTH) errors.bio = `A bio pode ter no máximo ${MAX_BIO_LENGTH} caracteres.`;

  return errors;
}

export function toPetPayload(values: PetFormValues): PetPayload {
  const bio = values.bio.trim();
  const color = values.color.trim();

  return {
    name: values.name.trim(),
    gender: values.gender as PetPayload["gender"],
    birth_date: values.birthDate || null,
    species_id: Number(values.speciesId),
    breed_id: values.breedId ? Number(values.breedId) : null,
    size_id: values.sizeId ? Number(values.sizeId) : null,
    goal_id: Number(values.goalId),
    color: color || null,
    bio: bio || null,
    is_neutered: values.isNeutered,
    is_vaccinated: values.isVaccinated,
    is_active: values.isActive,
  };
}

export function petToFormValues(pet: Pet): PetFormValues {
  return {
    name: pet.name,
    gender: pet.gender,
    birthDate: brToIso(pet.birth_date),
    speciesId: String(pet.species.id),
    breedId: pet.breed ? String(pet.breed.id) : "",
    sizeId: pet.size ? String(pet.size.id) : "",
    goalId: String(pet.goal.id),
    color: pet.color ?? "",
    bio: pet.bio ?? "",
    isNeutered: pet.is_neutered,
    isVaccinated: pet.is_vaccinated,
    isActive: pet.is_active,
  };
}

export const PET_API_FIELDS: ReadonlyArray<[keyof PetFormValues, string]> = [
  ["name", "name"],
  ["gender", "gender"],
  ["birthDate", "birth_date"],
  ["speciesId", "species_id"],
  ["breedId", "breed_id"],
  ["sizeId", "size_id"],
  ["goalId", "goal_id"],
  ["color", "color"],
  ["bio", "bio"],
];
