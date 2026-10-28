import { describe, expect, it } from "vitest";

import type { PetPhoto } from "@/features/pet/types/petTypes";
import { ageLabel, genderLabel, petSubtitle, primaryPhoto } from "@/features/pet/utils/petDisplay";

const NOW = new Date(2026, 9, 27);

function photo(id: number, isPrimary: boolean): PetPhoto {
  return { id, url: `http://x/${id}.jpg`, order: id, is_primary: isPrimary };
}

describe("ageLabel", () => {
  it("mostra anos, meses e menos de um mês", () => {
    expect(ageLabel("10/05/2022", NOW)).toBe("4 anos");
    expect(ageLabel("27/10/2025", NOW)).toBe("1 ano");
    expect(ageLabel("27/05/2026", NOW)).toBe("5 meses");
    expect(ageLabel("27/09/2026", NOW)).toBe("1 mês");
    expect(ageLabel("20/10/2026", NOW)).toBe("Menos de 1 mês");
  });

  it("só completa o mês no dia certo", () => {
    expect(ageLabel("28/09/2026", NOW)).toBe("Menos de 1 mês");
    expect(ageLabel("28/10/2025", NOW)).toBe("11 meses");
  });

  it("devolve nulo sem data ou com data futura", () => {
    expect(ageLabel(null, NOW)).toBeNull();
    expect(ageLabel("01/01/2030", NOW)).toBeNull();
  });
});

describe("rótulos", () => {
  it("traduz o sexo", () => {
    expect(genderLabel("male")).toBe("Macho");
    expect(genderLabel("female")).toBe("Fêmea");
  });

  it("monta o subtítulo com ou sem raça", () => {
    expect(petSubtitle({ species: { id: 1, name: "Cachorro" }, breed: { id: 2, name: "Poodle" } })).toBe("Cachorro · Poodle");
    expect(petSubtitle({ species: { id: 1, name: "Gato" }, breed: null })).toBe("Gato");
  });
});

describe("primaryPhoto", () => {
  it("prefere a foto principal, senão a primeira, senão nada", () => {
    expect(primaryPhoto([photo(1, false), photo(2, true)])?.id).toBe(2);
    expect(primaryPhoto([photo(1, false), photo(2, false)])?.id).toBe(1);
    expect(primaryPhoto([])).toBeNull();
  });
});
