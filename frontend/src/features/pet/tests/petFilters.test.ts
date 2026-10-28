import { describe, expect, it } from "vitest";

import { DEFAULT_PET_FILTERS } from "@/features/pet/types/petTypes";
import { hasActiveFilters, parsePetFilters, petFiltersToQuery, petFiltersToSearch, petsHref } from "@/features/pet/utils/petFilters";

describe("parsePetFilters", () => {
  it("usa os padrões quando a URL não tem parâmetros", () => {
    expect(parsePetFilters({})).toEqual(DEFAULT_PET_FILTERS);
  });

  it("lê busca, espécie, situação e página", () => {
    expect(parsePetFilters({ search: "  Thor ", species: "2", status: "active", page: "3" })).toEqual({ search: "Thor", speciesId: "2", status: "active", page: 3 });
  });

  it("ignora valores inválidos e usa o primeiro de arrays", () => {
    expect(parsePetFilters({ species: "abc", status: "hack", page: "-4" })).toEqual(DEFAULT_PET_FILTERS);
    expect(parsePetFilters({ page: ["2", "9"] }).page).toBe(2);
    expect(parsePetFilters({ page: "1.5" }).page).toBe(1);
  });

  it("limita o tamanho da busca", () => {
    expect(parsePetFilters({ search: "a".repeat(300) }).search).toHaveLength(100);
  });
});

describe("petFiltersToQuery", () => {
  it("monta a query no formato que a API espera", () => {
    expect(petFiltersToQuery({ search: "Rex", speciesId: "1", status: "inactive", page: 2 })).toEqual({
      page: 2,
      per_page: 12,
      sort: "created_at",
      direction: "desc",
      search: "Rex",
      "filters[species_id]": "1",
      "filters[is_active]": 0,
    });
  });

  it("omite filtros vazios", () => {
    const query = petFiltersToQuery(DEFAULT_PET_FILTERS);

    expect(query.search).toBeUndefined();
    expect(query["filters[species_id]"]).toBeUndefined();
    expect(query["filters[is_active]"]).toBeUndefined();
  });

  it("converte situação ativa em 1", () => {
    expect(petFiltersToQuery({ ...DEFAULT_PET_FILTERS, status: "active" })["filters[is_active]"]).toBe(1);
  });
});

describe("URL da lista", () => {
  it("só inclui parâmetros diferentes do padrão", () => {
    expect(petFiltersToSearch(DEFAULT_PET_FILTERS)).toBe("");
    expect(petFiltersToSearch({ search: "Rex", speciesId: "1", status: "active", page: 2 })).toBe("search=Rex&species=1&status=active&page=2");
  });

  it("monta o href e codifica a busca", () => {
    expect(petsHref(DEFAULT_PET_FILTERS)).toBe("/meus-pets");
    expect(petsHref({ ...DEFAULT_PET_FILTERS, search: "são josé" })).toBe("/meus-pets?search=s%C3%A3o+jos%C3%A9");
  });

  it("ida e volta preserva os filtros", () => {
    const filters = { search: "Luna", speciesId: "2", status: "inactive" as const, page: 4 };
    const params = Object.fromEntries(new URLSearchParams(petFiltersToSearch(filters)));

    expect(parsePetFilters(params)).toEqual(filters);
  });

  it("detecta filtros ativos (a página não conta)", () => {
    expect(hasActiveFilters({ ...DEFAULT_PET_FILTERS, page: 5 })).toBe(false);
    expect(hasActiveFilters({ ...DEFAULT_PET_FILTERS, status: "active" })).toBe(true);
  });
});
