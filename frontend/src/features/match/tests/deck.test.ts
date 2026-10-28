import { describe, expect, it } from "vitest";

import type { FeedPet } from "@/features/match/types/matchTypes";
import { createDeck, deckReducer, isDeckFinished, needsRefill } from "@/features/match/utils/deck";

function pet(id: string): FeedPet {
  return {
    uuid: id,
    name: id,
    gender: "male",
    color: null,
    bio: null,
    birth_date: null,
    is_neutered: false,
    is_vaccinated: false,
    distance_km: null,
    species: { id: 1, name: "Cachorro" },
    breed: null,
    size: null,
    goal: { id: 1, name: "Amizade" },
    photos: [],
  };
}

const ids = (state: { deck: FeedPet[] }) => state.deck.map((item) => item.uuid);

describe("deckReducer", () => {
  it("cria o baralho marcando todos como vistos", () => {
    const state = createDeck([pet("a"), pet("b")]);

    expect(ids(state)).toEqual(["a", "b"]);
    expect(state.seen).toEqual(["a", "b"]);
    expect(state.exhausted).toBe(false);
  });

  it("remove o cartão ao deslizar", () => {
    const state = deckReducer(createDeck([pet("a"), pet("b")]), { type: "swiped", pet: pet("a") });

    expect(ids(state)).toEqual(["b"]);
  });

  it("devolve o cartão ao topo quando o envio falha, sem duplicar", () => {
    let state = createDeck([pet("a"), pet("b")]);
    state = deckReducer(state, { type: "swiped", pet: pet("a") });
    state = deckReducer(state, { type: "restore", pet: pet("a") });
    state = deckReducer(state, { type: "restore", pet: pet("a") });

    expect(ids(state)).toEqual(["a", "b"]);
  });

  it("acrescenta só os pets ainda não vistos e ignora repetidos na mesma página", () => {
    let state = createDeck([pet("a")]);
    state = deckReducer(state, { type: "swiped", pet: pet("a") });
    state = deckReducer(state, { type: "refill-finished", items: [pet("a"), pet("b"), pet("b"), pet("c")], inFlight: 0 });

    expect(ids(state)).toEqual(["b", "c"]);
    expect(state.seen).toEqual(["a", "b", "c"]);
    expect(state.exhausted).toBe(false);
  });

  it("marca como esgotado quando não vem ninguém novo e não há swipes pendentes", () => {
    let state = createDeck([pet("a")]);
    state = deckReducer(state, { type: "swiped", pet: pet("a") });
    state = deckReducer(state, { type: "refill-finished", items: [pet("a")], inFlight: 0 });

    expect(state.exhausted).toBe(true);
    expect(isDeckFinished(state)).toBe(true);
  });

  it("não marca como esgotado se ainda há swipes pendentes", () => {
    let state = createDeck([pet("a")]);
    state = deckReducer(state, { type: "swiped", pet: pet("a") });
    state = deckReducer(state, { type: "refill-finished", items: [pet("a")], inFlight: 2 });

    expect(state.exhausted).toBe(false);
    expect(isDeckFinished(state)).toBe(false);
  });

  it("não termina enquanto ainda restam cartões, mesmo esgotado", () => {
    const state = deckReducer(createDeck([pet("a"), pet("b")]), { type: "refill-finished", items: [], inFlight: 0 });

    expect(state.exhausted).toBe(true);
    expect(isDeckFinished(state)).toBe(false);
  });

  it("controla o estado de carregamento", () => {
    let state = deckReducer(createDeck([]), { type: "refill-started" });
    expect(state.isRefilling).toBe(true);

    state = deckReducer(state, { type: "refill-failed" });
    expect(state.isRefilling).toBe(false);
    expect(state.exhausted).toBe(false);
  });
});

describe("needsRefill", () => {
  it("pede mais quando restam poucos cartões", () => {
    expect(needsRefill(createDeck([pet("a"), pet("b"), pet("c")]))).toBe(true);
    expect(needsRefill(createDeck([pet("a"), pet("b"), pet("c"), pet("d")]))).toBe(false);
  });

  it("não pede enquanto carrega nem depois de esgotar", () => {
    const loading = deckReducer(createDeck([pet("a")]), { type: "refill-started" });
    const done = deckReducer(createDeck([pet("a")]), { type: "refill-finished", items: [], inFlight: 0 });

    expect(needsRefill(loading)).toBe(false);
    expect(needsRefill(done)).toBe(false);
  });
});
