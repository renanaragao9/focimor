import "server-only";

import { apiServerPage } from "@/lib/api/server";

export interface WelcomeCounts {
  pets: number | null;
  matches: number | null;
  adoptions: number | null;
  ongs: number | null;
}

async function total(path: string): Promise<number | null> {
  try {
    const page = await apiServerPage<unknown>(path, { query: { page: 1, per_page: 1 } });
    return page.meta.total;
  } catch {
    return null;
  }
}

export async function fetchWelcomeCounts(): Promise<WelcomeCounts> {
  const [pets, matches, adoptions, ongs] = await Promise.all([total("pets"), total("matches"), total("adoptions"), total("my-ongs")]);
  return { pets, matches, adoptions, ongs };
}
