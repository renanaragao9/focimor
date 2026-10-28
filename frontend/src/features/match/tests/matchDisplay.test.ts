import { describe, expect, it } from "vitest";

import type { FeedPet } from "@/features/match/types/matchTypes";
import { actionLabel, cardTags, distanceLabel } from "@/features/match/utils/matchDisplay";

function pet(overrides: Partial<FeedPet> = {}): FeedPet {
  return {
    uuid: "u",
    name: "Luna",
    gender: "female",
    color: null,
    bio: null,
    birth_date: null,
    is_neutered: false,
    is_vaccinated: false,
    distance_km: null,
    species: { id: 1, name: "Gato" },
    breed: null,
    size: null,
    goal: { id: 2, name: "Amizade" },
    photos: [],
    ...overrides,
  };
}

describe("distanceLabel", () => {
  it("formata a distância em pt-BR", () => {
    expect(distanceLabel(3.2)).toBe("a 3,2 km");
    expect(distanceLabel(120)).toBe("a 120 km");
    expect(distanceLabel(0.4)).toBe("a menos de 1 km");
    expect(distanceLabel(null)).toBeNull();
  });
});

describe("actionLabel", () => {
  it("nomeia cada ação", () => {
    expect(actionLabel("like")).toBe("Curtir");
    expect(actionLabel("dislike")).toBe("Passar");
    expect(actionLabel("superlike")).toBe("Super curtir");
  });
});

describe("cardTags", () => {
  it("lista porte, vacinação, castração e objetivo, só o que existe", () => {
    expect(cardTags(pet())).toEqual(["Amizade"]);
    expect(cardTags(pet({ size: { id: 1, name: "Pequeno" }, is_vaccinated: true, is_neutered: true }))).toEqual(["Pequeno", "Vacinado", "Castrado", "Amizade"]);
  });
});
