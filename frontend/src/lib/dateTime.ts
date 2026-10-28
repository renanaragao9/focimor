const API_DATE_TIME = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})$/;

export function parseApiDateTime(value: string | null): Date | null {
  const match = value ? API_DATE_TIME.exec(value) : null;
  if (!match) return null;

  const [, day, month, year, hour, minute, second] = match;
  const date = new Date(Date.UTC(Number(year), Number(month) - 1, Number(day), Number(hour), Number(minute), Number(second)));

  return Number.isNaN(date.getTime()) ? null : date;
}

export function formatClock(date: Date, timeZone?: string): string {
  return new Intl.DateTimeFormat("pt-BR", { hour: "2-digit", minute: "2-digit", timeZone }).format(date);
}

export function dayKey(date: Date, timeZone?: string): string {
  return new Intl.DateTimeFormat("en-CA", { year: "numeric", month: "2-digit", day: "2-digit", timeZone }).format(date);
}

function formatDay(date: Date, timeZone?: string): string {
  return new Intl.DateTimeFormat("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric", timeZone }).format(date);
}

function dayDifference(date: Date, now: Date, timeZone?: string): number {
  const from = Date.parse(`${dayKey(date, timeZone)}T00:00:00Z`);
  const to = Date.parse(`${dayKey(now, timeZone)}T00:00:00Z`);

  return Math.round((to - from) / 86_400_000);
}

export function dayLabel(date: Date, now: Date, timeZone?: string): string {
  const difference = dayDifference(date, now, timeZone);
  if (difference === 0) return "Hoje";
  if (difference === 1) return "Ontem";
  return formatDay(date, timeZone);
}

export function listStamp(date: Date, now: Date, timeZone?: string): string {
  const difference = dayDifference(date, now, timeZone);
  if (difference === 0) return formatClock(date, timeZone);
  if (difference === 1) return "Ontem";
  return formatDay(date, timeZone).slice(0, 5);
}

export function toApiDateTime(date: Date): string {
  const pad = (value: number) => String(value).padStart(2, "0");

  return `${pad(date.getUTCDate())}/${pad(date.getUTCMonth() + 1)}/${date.getUTCFullYear()} ${pad(date.getUTCHours())}:${pad(date.getUTCMinutes())}:${pad(date.getUTCSeconds())}`;
}
