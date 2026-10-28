import { campaignHint } from "@/features/ong/utils/campaignDisplay";
import type { OngCampaign } from "@/features/ong/types/ongTypes";

export function CampaignProgress({ campaign }: { campaign: OngCampaign }) {
  const percent = campaign.progress_percent;

  return (
    <div className="flex flex-col gap-1.5">
      <div className="flex items-center justify-between gap-2 text-xs">
        <span className="truncate font-semibold">Meta</span>
        <span className="shrink-0 tabular-nums text-muted-foreground">{percent}%</span>
      </div>
      <div className="h-2 overflow-hidden rounded-full bg-muted" role="progressbar" aria-valuenow={percent} aria-valuemin={0} aria-valuemax={100} aria-label={`Progresso de ${campaign.title}`}>
        <div className="h-full rounded-full bg-linear-to-r from-brand to-sky" style={{ width: `${percent}%` }} />
      </div>
      <span className="text-xs text-muted-foreground">{campaignHint(campaign)}</span>
    </div>
  );
}
