export type PetGender = "male" | "female";

export interface CatalogItem {
  id: number;
  name: string;
  slug: string;
}

export interface Breed extends CatalogItem {
  species_id: number;
}

export interface PetPhoto {
  id: number;
  url: string;
  order: number;
  is_primary: boolean;
}

export interface NamedRef {
  id: number;
  name: string;
}

export interface Pet {
  uuid: string;
  name: string;
  gender: PetGender;
  color: string | null;
  bio: string | null;
  birth_date: string | null;
  is_neutered: boolean;
  is_vaccinated: boolean;
  is_active: boolean;
  species: NamedRef;
  breed: NamedRef | null;
  size: NamedRef | null;
  goal: NamedRef;
  photos: PetPhoto[];
}

export interface PetCatalogs {
  species: CatalogItem[];
  sizes: CatalogItem[];
  goals: CatalogItem[];
}

export type PetStatusFilter = "" | "active" | "inactive";

export interface PetFilters {
  search: string;
  speciesId: string;
  status: PetStatusFilter;
  page: number;
}

export const DEFAULT_PET_FILTERS: PetFilters = { search: "", speciesId: "", status: "", page: 1 };

export interface PetFormValues {
  name: string;
  gender: PetGender | "";
  birthDate: string;
  speciesId: string;
  breedId: string;
  sizeId: string;
  goalId: string;
  color: string;
  bio: string;
  isNeutered: boolean;
  isVaccinated: boolean;
  isActive: boolean;
}

export const EMPTY_PET_FORM: PetFormValues = {
  name: "",
  gender: "",
  birthDate: "",
  speciesId: "",
  breedId: "",
  sizeId: "",
  goalId: "",
  color: "",
  bio: "",
  isNeutered: false,
  isVaccinated: false,
  isActive: true,
};

export interface PetPayload {
  name: string;
  gender: PetGender;
  birth_date: string | null;
  species_id: number;
  breed_id: number | null;
  size_id: number | null;
  goal_id: number;
  color: string | null;
  bio: string | null;
  is_neutered: boolean;
  is_vaccinated: boolean;
  is_active: boolean;
}

export const GENDER_OPTIONS: ReadonlyArray<{ value: PetGender; label: string }> = [
  { value: "male", label: "Macho" },
  { value: "female", label: "Fêmea" },
];

export const PETS_PER_PAGE = 12;
export const MAX_PET_PHOTOS = 10;
export const MAX_PHOTO_BYTES = 5 * 1024 * 1024;
export const PHOTO_TYPES = ["image/jpeg", "image/png", "image/webp"] as const;
