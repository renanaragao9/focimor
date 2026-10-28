"use client";

import { HandCoins, Pencil, PiggyBank, Plus, Trash2 } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Pagination } from "@/components/Pagination";
import { Button } from "@/components/ui/button";
import { CampaignFormDialog } from "@/features/ong-panel/components/CampaignFormDialog";
import { DonationDialog } from "@/features/ong-panel/components/DonationDialog";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { panelHref } from "@/features/ong-panel/utils/panelParams";
import { CampaignCard } from "@/features/ong/components/CampaignCard";
import { EmptyState } from "@/features/ong/components/EmptyState";
import type { OngCampaign } from "@/features/ong/types/ongTypes";
import { useRecordDialogs } from "@/hooks/use-record-dialogs";
import type { ApiPage } from "@/lib/api/types";

interface CampaignsPanelProps {
  ongUuid: string;
  page: ApiPage<OngCampaign>;
  canPublish: boolean;
}

export function CampaignsPanel({ ongUuid, page, canPublish }: CampaignsPanelProps) {
  const router = useRouter();
  const dialogs = useRecordDialogs<OngCampaign>();
  const [donationFor, setDonationFor] = useState<OngCampaign | null>(null);
  const [donationKey, setDonationKey] = useState(0);

  return (
    <>
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="text-xl font-semibold">Caixinhas</h2>
          <p className="text-sm text-muted-foreground">
            {canPublish ? "Campanhas de arrecadação que aparecem na página pública da ONG. Atualize o valor arrecadado para mostrar o progresso." : "Disponível depois que a ONG for verificada."}
          </p>
        </div>
        <Button className="cursor-pointer" disabled={!canPublish} onClick={dialogs.openCreate}>
          <Plus /> Nova caixinha
        </Button>
      </div>

      {page.data.length === 0 ? (
        <EmptyState icon={PiggyBank} title="Nenhuma caixinha ainda" text="Crie uma caixinha com a meta de uma reforma, um tratamento ou uma compra de ração e acompanhe o progresso." />
      ) : (
        <>
          <ul className="grid gap-3 md:grid-cols-2">
            {page.data.map((campaign) => (
              <li key={campaign.uuid}>
                <CampaignCard
                  campaign={campaign}
                  actions={
                    <div className="flex items-center gap-1">
                      <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        className="cursor-pointer"
                        disabled={campaign.status === "cancelled"}
                        onClick={() => {
                          setDonationKey((current) => current + 1);
                          setDonationFor(campaign);
                        }}
                      >
                        <HandCoins /> Registrar doação
                      </Button>
                      <Button type="button" variant="ghost" size="icon-sm" className="cursor-pointer" aria-label={`Editar ${campaign.title}`} onClick={() => dialogs.openEdit(campaign, campaign.uuid)}>
                        <Pencil />
                      </Button>
                      <Button
                        type="button"
                        variant="ghost"
                        size="icon-sm"
                        className="cursor-pointer text-destructive hover:text-destructive"
                        aria-label={`Excluir ${campaign.title}`}
                        onClick={() => dialogs.setItemToDelete(campaign)}
                      >
                        <Trash2 />
                      </Button>
                    </div>
                  }
                />
              </li>
            ))}
          </ul>
          <Pagination meta={page.meta} buildHref={(target) => panelHref(ongUuid, { tab: "caixinhas", page: target })} />
        </>
      )}

      <CampaignFormDialog key={dialogs.formKey} ongUuid={ongUuid} campaign={dialogs.formItem} open={dialogs.formOpen} onOpenChange={dialogs.setFormOpen} onSaved={() => router.refresh()} />
      <DonationDialog
        key={`donation-${donationKey}`}
        ongUuid={ongUuid}
        campaign={donationFor}
        open={donationFor !== null}
        onOpenChange={(open) => !open && setDonationFor(null)}
        onSaved={() => router.refresh()}
      />

      <DeleteDialog
        name={dialogs.itemToDelete?.title ?? null}
        title="Excluir caixinha"
        warning="A caixinha e o progresso dela deixam de aparecer na página da ONG."
        successMessage="Caixinha excluída."
        errorMessage="Não foi possível excluir a caixinha."
        onConfirm={async () => {
          if (dialogs.itemToDelete) await ongPanelService.removeCampaign(ongUuid, dialogs.itemToDelete.uuid);
        }}
        onDeleted={() => router.refresh()}
        onOpenChange={(open) => !open && dialogs.setItemToDelete(null)}
      />
    </>
  );
}
