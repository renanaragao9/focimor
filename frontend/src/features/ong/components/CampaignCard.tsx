import { CalendarClock, PiggyBank } from "lucide-react";
import type { ReactNode } from "react";

import { Badge } from "@/components/ui/badge";
import { CampaignProgress } from "@/features/ong/components/CampaignProgress";
import type { OngCampaign } from "@/features/ong/types/ongTypes";
import { CAMPAIGN_STATUS_CLASSES } from "@/features/ong/utils/campaignDisplay";
import { formatBrl } from "@/lib/numbers";
import { cn } from "@/lib/utils";

interface CampaignCardProps {
  campaign: OngCampaign;
  actions?: ReactNode;
}

export function CampaignCard({ campaign, actions }: CampaignCardProps) {
  return (
    <article className="flex h-full flex-col gap-3 rounded-sm border bg-card p-4 shadow-sm">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div className="flex min-w-0 items-start gap-3">
          <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-soft">
            <PiggyBank className="size-5 text-brand" aria-hidden="true" />
          </span>
          <div className="flex min-w-0 flex-col">
            <h3 className="text-base font-bold sm:truncate">{campaign.title}</h3>
            {campaign.ends_at && (
              <span className="flex items-center gap-1 text-xs text-muted-foreground">
                <CalendarClock className="size-3" aria-hidden="true" /> Até {campaign.ends_at}
              </span>
            )}
          </div>
        </div>
        <div className="flex shrink-0 items-baseline gap-2 rounded-sm bg-success/10 px-3 py-2 sm:flex-col sm:items-end sm:gap-0 sm:bg-transparent sm:p-0">
          <span className="text-lg font-black tabular-nums text-success">{formatBrl(campaign.raised_amount)}</span>
          <span className="text-xs text-muted-foreground">arrecadado</span>
        </div>
      </div>

      {campaign.description && <p className="line-clamp-3 text-sm text-muted-foreground">{campaign.description}</p>}

      <div className="mt-auto flex flex-col gap-3">
        <CampaignProgress campaign={campaign} />
        <div className="flex items-center justify-between gap-2">
          <Badge variant="secondary" className={cn("shrink-0", CAMPAIGN_STATUS_CLASSES[campaign.status])}>
            {campaign.status_label}
          </Badge>
          {actions}
        </div>
      </div>
    </article>
  );
}
