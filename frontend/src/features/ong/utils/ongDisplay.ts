export function locationLabel(city: string | null, state: string | null): string | null {
  const parts = [city, state].filter((part): part is string => Boolean(part));
  return parts.length > 0 ? parts.join(" - ") : null;
}

export function phoneHref(phone: string): string {
  return `tel:${phone.replace(/[^\d+]/g, "")}`;
}

export function excerpt(text: string, max = 140): string {
  const clean = text.replace(/\s+/g, " ").trim();
  return clean.length <= max ? clean : `${clean.slice(0, max).trimEnd()}…`;
}

export function postDate(publishedAt: string | null): string | null {
  return publishedAt ? publishedAt.slice(0, 10) : null;
}
