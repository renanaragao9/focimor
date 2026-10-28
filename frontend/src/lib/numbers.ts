const BRL = new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" });

export function parseDecimal(value: string): number | null {
  const normalized = value.trim().replace(/\s/g, "").replace(/\.(?=\d{3}(\D|$))/g, "").replace(",", ".");
  if (!/^\d+(\.\d+)?$/.test(normalized)) return null;

  const parsed = Number(normalized);
  return Number.isFinite(parsed) ? parsed : null;
}

export function formatDecimal(value: string | number | null, maxFractionDigits = 2): string {
  if (value === null || value === "") return "";

  const parsed = typeof value === "number" ? value : Number(value);
  if (!Number.isFinite(parsed)) return "";

  return parsed.toLocaleString("pt-BR", { maximumFractionDigits: maxFractionDigits });
}

export function formatBrl(value: string | number | null): string {
  if (value === null || value === "") return "";

  const parsed = typeof value === "number" ? value : Number(value);
  return Number.isFinite(parsed) ? BRL.format(parsed) : "";
}

export function toDecimalInput(value: string | number | null): string {
  return formatDecimal(value, 2).replace(/\./g, "");
}
