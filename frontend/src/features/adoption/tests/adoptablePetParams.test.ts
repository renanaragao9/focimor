import { describe, expect, it } from "vitest";

import { adoptablePetHref, adoptablePetsHref, adoptablePetsToQuery, hasAdoptableFilters, parseAdoptablePetParams } from "@/features/adoption/utils/adoptablePetParams";

describe("adoptablePetParams", () => {
  it("lê filtros e página da URL", () => {
    expect(parseAdoptablePetParams({})).toEqual({ search: "", speciesId: "", sizeId: "", gender: "", page: 1 });
    expect(parseAdoptablePetParams({ search: " rex ", especie: "2", porte: "3", sexo: "female", page: "4" })).toEqual({ search: "rex", speciesId: "2", sizeId: "3", gender: "female", page: 4 });
    expect(parseAdoptablePetParams({ sexo: "x", especie: "abc" })).toMatchObject({ gender: "", speciesId: "" });
  });

  it("monta href sem parâmetros vazios", () => {
    expect(adoptablePetsHref()).toBe("/adocao");
    expect(adoptablePetsHref({ speciesId: "1", gender: "male", page: 2 })).toBe("/adocao?especie=1&sexo=male&page=2");
    expect(adoptablePetHref("u1")).toBe("/adocao/u1");
  });

  it("monta a query da API e detecta filtros", () => {
    expect(adoptablePetsToQuery({ search: "", speciesId: "1", sizeId: "", gender: "male", page: 2 })).toMatchObject({
      page: 2,
      "filters[species_id]": "1",
      "filters[gender]": "male",
      search: undefined,
    });
    expect(hasAdoptableFilters({ search: "", speciesId: "", sizeId: "", gender: "", page: 3 })).toBe(false);
    expect(hasAdoptableFilters({ search: "a", speciesId: "", sizeId: "", gender: "", page: 1 })).toBe(true);
  });
});
