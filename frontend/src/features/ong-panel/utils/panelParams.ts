import { PANEL_PER_PAGE, type PanelTab } from "@/features/ong-panel/types/ongPanelTypes";
import type { QueryParams } from "@/lib/api/types";
import { positiveInt, readParam, type RawSearchParams } from "@/lib/searchParams";

const TABS: readonly PanelTab[] = ["dados", "pets", "caixinhas", "posts", "membros"];

export interface PanelParams {
  tab: PanelTab;
  page: number;
}

export function parsePanelParams(raw: RawSearchParams): PanelParams {
  const tab = readParam(raw.aba);

  return { tab: (TABS as readonly string[]).includes(tab) ? (tab as PanelTab) : "dados", page: positiveInt(readParam(raw.page)) ?? 1 };
}

export function panelQuery(page: number): QueryParams {
  return { page, per_page: PANEL_PER_PAGE, sort: "created_at", direction: "desc" };
}

export function panelHref(uuid: string, params: Partial<PanelParams> = {}): string {
  const search = new URLSearchParams();
  if (params.tab && params.tab !== "dados") search.set("aba", params.tab);
  if (params.page && params.page > 1) search.set("page", String(params.page));

  const query = search.toString();
  return query ? `/minhas-ongs/${uuid}?${query}` : `/minhas-ongs/${uuid}`;
}
