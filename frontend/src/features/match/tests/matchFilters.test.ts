import { describe, expect, it } from "vitest";

import { DEFAULT_MATCH_FILTERS } from "@/features/match/types/matchTypes";
import { hasMatchFilters, matchBoardKey, matchFiltersToQuery, matchHref, parseMatchFilters } from "@/features/match/utils/matchFilters";

const UUID = "3f2b8c1e-4d5a-4e6f-8a9b-0c1d2e3f4a5b";

describe("parseMatchFilters", () => {
  it("usa o padrão sem parâmetros", () => {
    expect(parseMatchFilters({})).toEqual(DEFAULT_MATCH_FILTERS);
  });

  it("lê pet, espécie, porte, sexo e distância", () => {
    expect(parseMatchFilters({ pet: UUID, especie: "2", porte: "3", sexo: "female", dist: "50" })).toEqual({ petUuid: UUID, speciesId: "2", sizeId: "3", gender: "female", maxDistanceKm: "50" });
  });

  it("ignora valores inválidos", () => {
    expect(parseMatchFilters({ pet: "x", especie: "abc", porte: "-1", sexo: "outro", dist: "7" })).toEqual(DEFAULT_MATCH_FILTERS);
    expect(parseMatchFilters({ dist: "9999" }).maxDistanceKm).toBe("");
  });
});

describe("matchFiltersToQuery", () => {
  it("monta a query do feed com a primeira página e o tamanho fixo", () => {
    expect(matchFiltersToQuery({ petUuid: UUID, speciesId: "2", sizeId: "", gender: "male", maxDistanceKm: "25" })).toEqual({
      page: 1,
      per_page: 10,
      "filters[species_id]": "2",
      "filters[size_id]": undefined,
      "filters[gender]": "male",
      "filters[max_distance_km]": "25",
    });
  });

  it("não envia filtros vazios (a API usa a mesma espécie por padrão)", () => {
    const query = matchFiltersToQuery({ ...DEFAULT_MATCH_FILTERS, petUuid: UUID });

    expect(query["filters[species_id]"]).toBeUndefined();
    expect(query["filters[max_distance_km]"]).toBeUndefined();
  });
});

describe("URL e chave do tabuleiro", () => {
  it("só inclui o que difere do padrão", () => {
    expect(matchHref(DEFAULT_MATCH_FILTERS)).toBe("/match");
    expect(matchHref({ petUuid: UUID, speciesId: "2", sizeId: "", gender: "female", maxDistanceKm: "10" })).toBe(`/match?pet=${UUID}&especie=2&sexo=female&dist=10`);
  });

  it("ida e volta preserva os filtros", () => {
    const filters = { petUuid: UUID, speciesId: "1", sizeId: "2", gender: "male" as const, maxDistanceKm: "100" };

    expect(parseMatchFilters(Object.fromEntries(new URLSearchParams(matchHref(filters).split("?")[1])))).toEqual(filters);
  });

  it("detecta filtros ativos (o pet não conta)", () => {
    expect(hasMatchFilters({ ...DEFAULT_MATCH_FILTERS, petUuid: UUID })).toBe(false);
    expect(hasMatchFilters({ ...DEFAULT_MATCH_FILTERS, gender: "male" })).toBe(true);
  });

  it("muda a chave quando qualquer filtro muda (reinicia o baralho)", () => {
    const base = { ...DEFAULT_MATCH_FILTERS, petUuid: UUID };

    expect(matchBoardKey(base)).toBe(matchBoardKey({ ...base }));
    expect(matchBoardKey(base)).not.toBe(matchBoardKey({ ...base, sizeId: "1" }));
    expect(matchBoardKey(base)).not.toBe(matchBoardKey({ ...base, petUuid: "outro" }));
  });
});
