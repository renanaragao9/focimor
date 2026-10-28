import { describe, expect, it } from "vitest";

import { isPetUuid, parsePetDetailParams, petDetailHref } from "@/features/pet-detail/utils/petDetailParams";

const UUID = "3f2b8c1e-4d5a-4e6f-8a9b-0c1d2e3f4a5b";

describe("isPetUuid", () => {
  it("aceita só UUID", () => {
    expect(isPetUuid(UUID)).toBe(true);
    expect(isPetUuid(UUID.toUpperCase())).toBe(true);
    expect(isPetUuid("1")).toBe(false);
    expect(isPetUuid(`${UUID}/../x`)).toBe(false);
    expect(isPetUuid("")).toBe(false);
  });
});

describe("parsePetDetailParams", () => {
  it("usa a aba Detalhes por padrão", () => {
    expect(parsePetDetailParams({})).toEqual({ tab: "detalhes", page: 1, typeId: "" });
  });

  it("lê aba, página e tipo", () => {
    expect(parsePetDetailParams({ aba: "saude", page: "3", tipo: "2" })).toEqual({ tab: "saude", page: 3, typeId: "2" });
  });

  it("ignora valores inválidos", () => {
    expect(parsePetDetailParams({ aba: "hack", page: "-1", tipo: "abc" })).toEqual({ tab: "detalhes", page: 1, typeId: "" });
    expect(parsePetDetailParams({ aba: ["pesos", "vacinas"] }).tab).toBe("pesos");
  });
});

describe("petDetailHref", () => {
  it("monta a URL só com o que difere do padrão", () => {
    expect(petDetailHref(UUID)).toBe(`/meus-pets/${UUID}`);
    expect(petDetailHref(UUID, { tab: "detalhes", page: 1 })).toBe(`/meus-pets/${UUID}`);
    expect(petDetailHref(UUID, { tab: "vacinas" })).toBe(`/meus-pets/${UUID}?aba=vacinas`);
    expect(petDetailHref(UUID, { tab: "saude", typeId: "2", page: 3 })).toBe(`/meus-pets/${UUID}?aba=saude&tipo=2&page=3`);
  });

  it("ida e volta preserva os parâmetros", () => {
    const params = { tab: "saude" as const, page: 2, typeId: "4" };
    const href = petDetailHref(UUID, params);

    expect(parsePetDetailParams(Object.fromEntries(new URLSearchParams(href.split("?")[1])))).toEqual(params);
  });
});
