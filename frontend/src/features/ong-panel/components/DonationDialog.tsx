"use client";

import { useState, type FormEvent } from "react";
import { toast } from "sonner";

import { TextField } from "@/components/FormFields";
import { RecordFormDialog } from "@/components/RecordFormDialog";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { validateDonation } from "@/features/ong-panel/validators/panelValidators";
import type { OngCampaign } from "@/features/ong/types/ongTypes";
import { ApiError } from "@/lib/api/errors";
import { formatBrl, parseDecimal } from "@/lib/numbers";

interface DonationDialogProps {
  ongUuid: string;
  campaign: OngCampaign | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved: () => void;
}

export function DonationDialog({ ongUuid, campaign, open, onOpenChange, onSaved }: DonationDialogProps) {
  const [amount, setAmount] = useState("");
  const [error, setError] = useState<string | undefined>();
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);
    if (!campaign) return;

    const found = validateDonation(amount);
    setError(found);
    if (found) return;

    setIsSaving(true);
    try {
      await ongPanelService.updateCampaign(ongUuid, campaign.uuid, { raised_amount: Number(campaign.raised_amount) + (parseDecimal(amount) ?? 0) });
      toast.success("Valor somado ao arrecadado.");
      onSaved();
      onOpenChange(false);
    } catch (caught) {
      setFormError(caught instanceof ApiError ? caught.message : "Não foi possível atualizar o valor. Tente novamente.");
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <RecordFormDialog
      open={open}
      onOpenChange={onOpenChange}
      title="Registrar doação"
      description={campaign ? `Some um valor recebido a "${campaign.title}". Hoje: ${formatBrl(campaign.raised_amount)}.` : ""}
      submitLabel="Somar ao arrecadado"
      isSaving={isSaving}
      formError={formError}
      onSubmit={handleSubmit}
    >
      <TextField id="donation-amount" label="Valor recebido (R$)" required inputMode="decimal" placeholder="Ex.: 150" value={amount} error={error} disabled={isSaving} onChange={setAmount} />
    </RecordFormDialog>
  );
}
