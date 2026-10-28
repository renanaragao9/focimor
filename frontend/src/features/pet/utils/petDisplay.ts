import type { Pet, PetGender, PetPhoto } from "@/features/pet/types/petTypes";
import { brToIso } from "@/lib/dates";

export function genderLabel(gender: PetGender): string {
  return gender === "male" ? "Macho" : "Fêmea";
}

export function primaryPhoto(photos: readonly PetPhoto[]): PetPhoto | null {
  return photos.find((photo) => photo.is_primary) ?? photos[0] ?? null;
}

export function ageLabel(birthDate: string | null, now: Date): string | null {
  const iso = brToIso(birthDate);
  if (!iso) return null;

  const [year, month, day] = iso.split("-").map(Number);
  let months = (now.getFullYear() - year) * 12 + (now.getMonth() + 1 - month);
  if (now.getDate() < day) months -= 1;
  if (months < 0) return null;
  if (months < 1) return "Menos de 1 mês";
  if (months < 12) return months === 1 ? "1 mês" : `${months} meses`;

  const years = Math.floor(months / 12);
  return years === 1 ? "1 ano" : `${years} anos`;
}

export function petSubtitle(pet: Pick<Pet, "species" | "breed">): string {
  return pet.breed ? `${pet.species.name} · ${pet.breed.name}` : pet.species.name;
}

export interface GenderTheme {
  text: string;
  soft: string;
  solid: string;
  border: string;
  hoverBorder: string;
  gradient: string;
}

const MALE_THEME: GenderTheme = {
  text: "text-sky",
  soft: "bg-sky-soft text-sky",
  solid: "bg-sky text-white hover:bg-sky/90 dark:text-background",
  border: "border-sky",
  hoverBorder: "hover:border-sky",
  gradient: "from-sky to-info",
};

const FEMALE_THEME: GenderTheme = {
  text: "text-brand",
  soft: "bg-brand-soft text-brand-deep dark:text-brand",
  solid: "bg-brand text-brand-foreground hover:bg-brand/90",
  border: "border-brand",
  hoverBorder: "hover:border-brand",
  gradient: "from-brand to-brand-deep",
};

export function genderTheme(gender: PetGender): GenderTheme {
  return gender === "male" ? MALE_THEME : FEMALE_THEME;
}
