import { describe, expect, it } from "vitest";

import type { Weight } from "@/features/pet-detail/types/petDetailTypes";
import { dueLabel, dueStatus, summarizeMeasures, weightTrend } from "@/features/pet-detail/utils/petDetailDisplay";

const NOW = new Date(2026, 9, 27);

function weight(kg: string, height: string | null = null, width: string | null = null): Weight {
  return { id: 1, weight_kg: kg, height_cm: height, width_cm: width, measured_at: "01/01/2026" };
}

describe("dueStatus", () => {
  it("classifica vencida, em breve e em dia", () => {
    expect(dueStatus("20/10/2026", NOW)).toEqual({ status: "overdue", days: -7 });
    expect(dueStatus("27/10/2026", NOW)).toEqual({ status: "soon", days: 0 });
    expect(dueStatus("26/11/2026", NOW)).toEqual({ status: "soon", days: 30 });
    expect(dueStatus("27/11/2026", NOW)).toEqual({ status: "ok", days: 31 });
  });

  it("devolve nulo sem data", () => {
    expect(dueStatus(null, NOW)).toBeNull();
    expect(dueStatus("", NOW)).toBeNull();
  });
});

describe("dueLabel", () => {
  it("descreve o vencimento em português", () => {
    expect(dueLabel({ status: "overdue", days: -1 })).toBe("Venceu ontem");
    expect(dueLabel({ status: "overdue", days: -7 })).toBe("Venceu há 7 dias");
    expect(dueLabel({ status: "soon", days: 0 })).toBe("Vence hoje");
    expect(dueLabel({ status: "soon", days: 1 })).toBe("Vence amanhã");
    expect(dueLabel({ status: "ok", days: 45 })).toBe("Em 45 dias");
  });
});

describe("weightTrend", () => {
  it("mostra ganho, perda e estabilidade", () => {
    expect(weightTrend(weight("12.50"), weight("12.00"))).toEqual({ kg: 0.5, label: "+0,5 kg" });
    expect(weightTrend(weight("11.25"), weight("12.00"))).toEqual({ kg: -0.75, label: "-0,75 kg" });
    expect(weightTrend(weight("12.00"), weight("12.00"))).toEqual({ kg: 0, label: "Sem variação" });
  });

  it("evita erro de ponto flutuante", () => {
    expect(weightTrend(weight("0.30"), weight("0.10"))).toEqual({ kg: 0.2, label: "+0,2 kg" });
  });

  it("devolve nulo sem medição para comparar", () => {
    expect(weightTrend(weight("12"), undefined)).toBeNull();
    expect(weightTrend(undefined, weight("12"))).toBeNull();
  });
});

describe("summarizeMeasures", () => {
  it("junta só as medidas informadas", () => {
    expect(summarizeMeasures(weight("10", "40.00", "25.50"))).toBe("40 cm de altura · 25,5 cm de largura");
    expect(summarizeMeasures(weight("10", null, "25"))).toBe("25 cm de largura");
    expect(summarizeMeasures(weight("10"))).toBe("");
  });
});
