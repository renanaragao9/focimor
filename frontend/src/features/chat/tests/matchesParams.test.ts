import { describe, expect, it } from "vitest";

import { chatHref, matchesHref, matchesToQuery, parseMatchesParams } from "@/features/chat/utils/matchesParams";

const UUID = "0b9b3a52-6f1c-4c1e-9d3e-5a4f2f8d7c11";

describe("parseMatchesParams", () => {
  it("lê pet e página", () => {
    expect(parseMatchesParams({ pet: UUID, page: "3" })).toEqual({ petUuid: UUID, page: 3 });
  });

  it("ignora valores inválidos", () => {
    expect(parseMatchesParams({ pet: "abc", page: "-2" })).toEqual({ petUuid: "", page: 1 });
  });
});

describe("matchesToQuery", () => {
  it("monta a consulta da API", () => {
    expect(matchesToQuery({ petUuid: UUID, page: 2 })).toMatchObject({ page: 2, sort: "matched_at", direction: "desc", "filters[pet_uuid]": UUID });
    expect(matchesToQuery({ petUuid: "", page: 1 })["filters[pet_uuid]"]).toBeUndefined();
  });
});

describe("links", () => {
  it("monta a URL da lista e do chat", () => {
    expect(matchesHref()).toBe("/matches");
    expect(matchesHref({ petUuid: UUID, page: 2 })).toBe(`/matches?pet=${UUID}&page=2`);
    expect(chatHref(UUID)).toBe(`/matches/${UUID}`);
  });
});
