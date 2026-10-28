import type { CampaignStatus, OngCampaign } from "@/features/ong/types/ongTypes";
import { formatBrl } from "@/lib/numbers";

export const CAMPAIGN_STATUS_CLASSES: Record<CampaignStatus, string> = {
  active: "bg-sun-soft text-sun",
  completed: "bg-mint-soft text-mint",
  cancelled: "bg-muted text-muted-foreground",
};

export function campaignHint(campaign: Pick<OngCampaign, "status" | "goal_amount" | "remaining_amount">): string {
  if (campaign.status === "cancelled") return "Caixinha cancelada";
  if (Number(campaign.remaining_amount) === 0) return `Meta de ${formatBrl(campaign.goal_amount)} batida`;
  return `Faltam ${formatBrl(campaign.remaining_amount)} de ${formatBrl(campaign.goal_amount)}`;
}

export function resolveCampaignStatus(raised: number, goal: number, cancelled: boolean): CampaignStatus {
  if (cancelled) return "cancelled";
  return raised >= goal ? "completed" : "active";
}
