import { Info } from "lucide-react";

import { CampaignCard } from "@/features/ong/components/CampaignCard";
import type { OngCampaign } from "@/features/ong/types/ongTypes";

export function CampaignsList({ campaigns, hasPix }: { campaigns: OngCampaign[]; hasPix: boolean }) {
  return (
    <>
      <p className="flex items-start gap-1.5 text-sm text-muted-foreground">
        <Info className="mt-0.5 size-4 shrink-0" aria-hidden="true" />
        {hasPix
          ? "Para contribuir, use o Pix e os contatos da ONG acima. O progresso é atualizado pela própria ONG."
          : "Para contribuir, fale com a ONG pelos contatos acima. O progresso é atualizado pela própria ONG."}
      </p>
      <ul className="grid gap-3 md:grid-cols-2">
        {campaigns.map((campaign) => (
          <li key={campaign.uuid}>
            <CampaignCard campaign={campaign} />
          </li>
        ))}
      </ul>
    </>
  );
}
