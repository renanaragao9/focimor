import { daysBetween, brToIso, todayIso } from "@/lib/dates";
import { formatDecimal } from "@/lib/numbers";
import type { Weight } from "@/features/pet-detail/types/petDetailTypes";

export type DueStatus = "overdue" | "soon" | "ok";

export const DUE_SOON_DAYS = 30;

export function dueStatus(dueBr: string | null, now: Date): { status: DueStatus; days: number } | null {
  const dueIso = brToIso(dueBr);
  if (!dueIso) return null;

  const days = daysBetween(todayIso(now), dueIso);
  if (days < 0) return { status: "overdue", days };
  return { status: days <= DUE_SOON_DAYS ? "soon" : "ok", days };
}

export function dueLabel(due: { status: DueStatus; days: number }): string {
  if (due.status === "overdue") return due.days === -1 ? "Venceu ontem" : `Venceu há ${Math.abs(due.days)} dias`;
  if (due.days === 0) return "Vence hoje";
  return due.days === 1 ? "Vence amanhã" : `Em ${due.days} dias`;
}

export function weightTrend(latest: Weight | undefined, previous: Weight | undefined): { kg: number; label: string } | null {
  if (!latest || !previous) return null;

  const kg = Math.round((Number(latest.weight_kg) - Number(previous.weight_kg)) * 100) / 100;
  if (!Number.isFinite(kg)) return null;
  if (kg === 0) return { kg, label: "Sem variação" };
  return { kg, label: `${kg > 0 ? "+" : "-"}${formatDecimal(Math.abs(kg))} kg` };
}

export function summarizeMeasures(weight: Weight): string {
  const parts = [weight.height_cm ? `${formatDecimal(weight.height_cm)} cm de altura` : null, weight.width_cm ? `${formatDecimal(weight.width_cm)} cm de largura` : null];
  return parts.filter(Boolean).join(" · ");
}
