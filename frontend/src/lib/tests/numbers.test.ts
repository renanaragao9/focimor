import { describe, expect, it } from "vitest";

import { formatBrl, formatDecimal, parseDecimal, toDecimalInput } from "@/lib/numbers";

describe("parseDecimal", () => {
  it("aceita vírgula ou ponto como separador decimal", () => {
    expect(parseDecimal("12,5")).toBe(12.5);
    expect(parseDecimal("12.5")).toBe(12.5);
    expect(parseDecimal(" 8 ")).toBe(8);
    expect(parseDecimal("0,01")).toBe(0.01);
  });

  it("entende ponto como separador de milhar", () => {
    expect(parseDecimal("1.234,56")).toBe(1234.56);
    expect(parseDecimal("1.234")).toBe(1234);
  });

  it("recusa texto, negativos e vazio", () => {
    expect(parseDecimal("")).toBeNull();
    expect(parseDecimal("abc")).toBeNull();
    expect(parseDecimal("-3")).toBeNull();
    expect(parseDecimal("1,2,3")).toBeNull();
  });
});

describe("formatação", () => {
  it("formata decimais em pt-BR sem zeros sobrando", () => {
    expect(formatDecimal("12.50")).toBe("12,5");
    expect(formatDecimal("40.00")).toBe("40");
    expect(formatDecimal(1234.567)).toBe("1.234,57");
    expect(formatDecimal(null)).toBe("");
    expect(formatDecimal("x")).toBe("");
  });

  it("formata moeda em reais", () => {
    expect(formatBrl("150.50").replace(/\s/g, " ")).toBe("R$ 150,50");
    expect(formatBrl(null)).toBe("");
  });

  it("gera o valor do campo de edição sem separador de milhar", () => {
    expect(toDecimalInput("12.50")).toBe("12,5");
    expect(toDecimalInput("1234.00")).toBe("1234");
    expect(toDecimalInput(null)).toBe("");
  });
});
