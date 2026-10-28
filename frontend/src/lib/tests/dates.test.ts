import { describe, expect, it } from "vitest";

import { brToIso, daysBetween, isIsoDate, todayIso } from "@/lib/dates";

describe("brToIso", () => {
  it("converte dd/mm/aaaa para aaaa-mm-dd", () => {
    expect(brToIso("10/05/2022")).toBe("2022-05-10");
  });

  it("devolve vazio para nulo ou formato inesperado", () => {
    expect(brToIso(null)).toBe("");
    expect(brToIso("2022-05-10")).toBe("");
    expect(brToIso("1/5/22")).toBe("");
  });
});

describe("todayIso", () => {
  it("usa o dia local com zeros à esquerda", () => {
    expect(todayIso(new Date(2026, 0, 5, 23, 30))).toBe("2026-01-05");
    expect(todayIso(new Date(2026, 10, 27))).toBe("2026-11-27");
  });
});

describe("isIsoDate", () => {
  it("aceita só datas reais", () => {
    expect(isIsoDate("2024-02-29")).toBe(true);
    expect(isIsoDate("2023-02-29")).toBe(false);
    expect(isIsoDate("2024-13-01")).toBe(false);
    expect(isIsoDate("10/05/2022")).toBe(false);
    expect(isIsoDate("")).toBe(false);
  });
});

describe("daysBetween", () => {
  it("conta dias corridos, inclusive entre meses e anos", () => {
    expect(daysBetween("2026-10-27", "2026-10-27")).toBe(0);
    expect(daysBetween("2026-10-27", "2026-11-26")).toBe(30);
    expect(daysBetween("2026-12-30", "2027-01-02")).toBe(3);
    expect(daysBetween("2026-10-27", "2026-10-20")).toBe(-7);
  });
});
