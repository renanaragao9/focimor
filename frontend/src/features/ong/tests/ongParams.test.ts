import { describe, expect, it } from "vitest";

import { excerpt, locationLabel, phoneHref, postDate } from "@/features/ong/utils/ongDisplay";
import { listHref, ongHref, ongsToQuery, parseListParams, parseOngDetailParams } from "@/features/ong/utils/ongParams";

describe("ongParams", () => {
  it("lê busca e página da URL", () => {
    expect(parseListParams({ search: "  amigo ", page: "3" })).toEqual({ search: "amigo", page: 3 });
    expect(parseListParams({ page: "x" })).toEqual({ search: "", page: 1 });
  });

  it("aba padrão é pets", () => {
    expect(parseOngDetailParams({})).toEqual({ tab: "pets", page: 1 });
    expect(parseOngDetailParams({ aba: "posts", page: "2" })).toEqual({ tab: "posts", page: 2 });
    expect(parseOngDetailParams({ aba: "caixinhas" }).tab).toBe("caixinhas");
    expect(parseOngDetailParams({ aba: "outra" }).tab).toBe("pets");
  });

  it("monta hrefs sem parâmetros padrão", () => {
    expect(listHref("/ongs")).toBe("/ongs");
    expect(listHref("/posts", { search: "dia", page: 2 })).toBe("/posts?search=dia&page=2");
    expect(ongHref("u1")).toBe("/ongs/u1");
    expect(ongHref("u1", { tab: "posts", page: 2 })).toBe("/ongs/u1?aba=posts&page=2");
    expect(ongHref("u1", { tab: "caixinhas" })).toBe("/ongs/u1?aba=caixinhas");
  });

  it("monta a query da API", () => {
    expect(ongsToQuery({ search: "", page: 2 })).toMatchObject({ page: 2, sort: "name", direction: "asc", search: undefined });
  });
});

describe("ongDisplay", () => {
  it("formata local, telefone, trecho e data", () => {
    expect(locationLabel("Fortaleza", "CE")).toBe("Fortaleza - CE");
    expect(locationLabel(null, null)).toBeNull();
    expect(phoneHref("(85) 99999-0000")).toBe("tel:85999990000");
    expect(excerpt("a  b\nc", 10)).toBe("a b c");
    expect(excerpt("x".repeat(20), 5)).toBe("xxxxx…");
    expect(postDate("27/10/2026 10:00:00")).toBe("27/10/2026");
    expect(postDate(null)).toBeNull();
  });
});
