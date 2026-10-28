import { describe, expect, it } from "vitest";

import { exitOffset, isTap, overlayOpacities, resolveSwipe, rotationFor, stepPhoto, tapZone } from "@/features/match/utils/swipeGesture";

describe("resolveSwipe", () => {
  it("curte arrastando para a direita e passa para a esquerda, acima do limite", () => {
    expect(resolveSwipe(100, 0)).toBe("like");
    expect(resolveSwipe(260, 30)).toBe("like");
    expect(resolveSwipe(-100, 0)).toBe("dislike");
    expect(resolveSwipe(-260, -30)).toBe("dislike");
  });

  it("não decide abaixo do limite", () => {
    expect(resolveSwipe(99, 0)).toBeNull();
    expect(resolveSwipe(-99, 10)).toBeNull();
    expect(resolveSwipe(0, 0)).toBeNull();
  });

  it("super curte arrastando para cima, desde que o movimento seja mais vertical", () => {
    expect(resolveSwipe(0, -120)).toBe("superlike");
    expect(resolveSwipe(40, -200)).toBe("superlike");
    expect(resolveSwipe(0, -119)).toBeNull();
    expect(resolveSwipe(200, -130)).toBe("like");
    expect(resolveSwipe(0, 200)).toBeNull();
  });
});

describe("rotationFor", () => {
  it("gira proporcionalmente e limita em 15 graus", () => {
    expect(rotationFor(0)).toBe(0);
    expect(rotationFor(150)).toBe(7.5);
    expect(rotationFor(-150)).toBe(-7.5);
    expect(rotationFor(1000)).toBe(15);
    expect(rotationFor(-1000)).toBe(-15);
  });
});

describe("overlayOpacities", () => {
  it("mostra o carimbo de curtir ou passar conforme o lado", () => {
    expect(overlayOpacities(50, 0)).toEqual({ like: 0.5, dislike: 0, superlike: 0 });
    expect(overlayOpacities(-100, 0)).toEqual({ like: 0, dislike: 1, superlike: 0 });
    expect(overlayOpacities(500, 0).like).toBe(1);
  });

  it("mostra o super quando o movimento é mais vertical para cima", () => {
    expect(overlayOpacities(10, -60)).toEqual({ like: 0, dislike: 0, superlike: 0.5 });
    expect(overlayOpacities(0, 0)).toEqual({ like: 0, dislike: 0, superlike: 0 });
  });

  it("não mostra super ao arrastar para baixo", () => {
    expect(overlayOpacities(0, 200).superlike).toBe(0);
  });
});

describe("toque e fotos", () => {
  it("diferencia toque de arrasto", () => {
    expect(isTap(2, 3)).toBe(true);
    expect(isTap(6, 0)).toBe(true);
    expect(isTap(5, 5)).toBe(false);
  });

  it("divide a área do cartão em anterior, próxima e nenhuma", () => {
    expect(tapZone(0.1)).toBe("previous");
    expect(tapZone(0.5)).toBe("none");
    expect(tapZone(0.9)).toBe("next");
  });

  it("anda entre as fotos sem passar dos limites", () => {
    expect(stepPhoto(0, 3, "next")).toBe(1);
    expect(stepPhoto(2, 3, "next")).toBe(2);
    expect(stepPhoto(0, 3, "previous")).toBe(0);
    expect(stepPhoto(2, 3, "previous")).toBe(1);
    expect(stepPhoto(1, 3, "none")).toBe(1);
    expect(stepPhoto(0, 1, "next")).toBe(0);
  });
});

describe("exitOffset", () => {
  it("manda o cartão para fora da tela na direção do gesto", () => {
    expect(exitOffset("like")).toEqual({ x: 1000, y: 0 });
    expect(exitOffset("dislike")).toEqual({ x: -1000, y: 0 });
    expect(exitOffset("superlike")).toEqual({ x: 0, y: -1000 });
  });
});
