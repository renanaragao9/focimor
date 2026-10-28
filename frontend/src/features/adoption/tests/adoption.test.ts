import { describe, expect, it } from "vitest";

import { availableActions, chatAccess, counterpartLabel } from "@/features/adoption/utils/adoptionDisplay";
import { adoptionsHref, adoptionsToQuery, parseAdoptionsParams } from "@/features/adoption/utils/adoptionParams";
import type { FeedPet } from "@/features/match/types/matchTypes";

describe("adoptionParams", () => {
  it("lê aba, situação e página", () => {
    expect(parseAdoptionsParams({})).toEqual({ tab: "meus", status: "", page: 1 });
    expect(parseAdoptionsParams({ aba: "recebidos", status: "pending", page: "2" })).toEqual({ tab: "recebidos", status: "pending", page: 2 });
    expect(parseAdoptionsParams({ status: "x" }).status).toBe("");
  });

  it("monta href e query", () => {
    expect(adoptionsHref()).toBe("/adocoes");
    expect(adoptionsHref({ tab: "recebidos", status: "accepted", page: 3 })).toBe("/adocoes?aba=recebidos&status=accepted&page=3");
    expect(adoptionsToQuery({ tab: "meus", status: "adopted", page: 1 })).toMatchObject({ "filters[status]": "adopted", sort: "created_at" });
  });
});

describe("adoptionDisplay", () => {
  it("define as ações por papel e situação", () => {
    expect(availableActions({ role: "owner", status: "pending" })).toEqual(["accept", "decline"]);
    expect(availableActions({ role: "owner", status: "accepted" })).toEqual(["adopt", "decline"]);
    expect(availableActions({ role: "adopter", status: "pending" })).toEqual(["cancel"]);
    expect(availableActions({ role: "adopter", status: "accepted" })).toEqual(["cancel"]);
    expect(availableActions({ role: "owner", status: "adopted" })).toEqual([]);
    expect(availableActions({ role: "adopter", status: "declined" })).toEqual([]);
  });

  it("libera o chat só depois de aceito", () => {
    expect(chatAccess("pending")).toBe("closed");
    expect(chatAccess("accepted")).toBe("open");
    expect(chatAccess("adopted")).toBe("read-only");
    expect(chatAccess("cancelled")).toBe("closed");
  });

  it("nomeia a outra parte", () => {
    const pet = { ong: { name: "Amigo Fiel" } } as FeedPet;
    expect(counterpartLabel({ role: "adopter", pet, adopter: undefined })).toBe("Amigo Fiel");
    expect(counterpartLabel({ role: "owner", pet, adopter: { name: "Ana" } })).toBe("Ana quer adotar");
  });
});
