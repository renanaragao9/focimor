import { describe, expect, it } from "vitest";

import { dayKey, dayLabel, formatClock, listStamp, parseApiDateTime, toApiDateTime } from "@/lib/dateTime";

const UTC = "UTC";

describe("parseApiDateTime", () => {
  it("lê dd/mm/aaaa HH:mm:ss como UTC", () => {
    expect(parseApiDateTime("05/03/2026 14:30:15")?.toISOString()).toBe("2026-03-05T14:30:15.000Z");
  });

  it("devolve nulo para vazio ou formato inesperado", () => {
    expect(parseApiDateTime(null)).toBeNull();
    expect(parseApiDateTime("2026-03-05 14:30:15")).toBeNull();
    expect(parseApiDateTime("05/03/2026")).toBeNull();
  });
});

describe("toApiDateTime", () => {
  it("é o inverso de parseApiDateTime", () => {
    expect(toApiDateTime(parseApiDateTime("05/03/2026 04:07:09") as Date)).toBe("05/03/2026 04:07:09");
  });
});

describe("rótulos de data", () => {
  const now = new Date("2026-03-05T12:00:00Z");

  it("formata hora e chave do dia", () => {
    expect(formatClock(new Date("2026-03-05T14:30:00Z"), UTC)).toBe("14:30");
    expect(dayKey(new Date("2026-03-05T14:30:00Z"), UTC)).toBe("2026-03-05");
  });

  it("usa Hoje, Ontem e a data completa", () => {
    expect(dayLabel(new Date("2026-03-05T01:00:00Z"), now, UTC)).toBe("Hoje");
    expect(dayLabel(new Date("2026-03-04T23:00:00Z"), now, UTC)).toBe("Ontem");
    expect(dayLabel(new Date("2026-02-01T10:00:00Z"), now, UTC)).toBe("01/02/2026");
  });

  it("na lista mostra hora, Ontem ou dd/mm", () => {
    expect(listStamp(new Date("2026-03-05T09:05:00Z"), now, UTC)).toBe("09:05");
    expect(listStamp(new Date("2026-03-04T09:05:00Z"), now, UTC)).toBe("Ontem");
    expect(listStamp(new Date("2026-02-01T09:05:00Z"), now, UTC)).toBe("01/02");
  });

  it("respeita o fuso informado na virada do dia", () => {
    const late = new Date("2026-03-05T02:00:00Z");
    expect(dayKey(late, "America/Sao_Paulo")).toBe("2026-03-04");
  });
});
