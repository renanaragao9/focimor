"use client";

import { useState, type FormEvent } from "react";
import { toast } from "sonner";

import { DateField, TextAreaField, TextField } from "@/components/FormFields";
import { RecordFormDialog } from "@/components/RecordFormDialog";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import type { CampaignValues } from "@/features/ong-panel/types/ongPanelTypes";
import { CAMPAIGN_API_FIELDS, EMPTY_CAMPAIGN_FORM, campaignToValues, toCampaignPayload, validateCampaign, type CampaignErrors } from "@/features/ong-panel/validators/panelValidators";
import type { OngCampaign } from "@/features/ong/types/ongTypes";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";

interface CampaignFormDialogProps {
  ongUuid: string;
  campaign: OngCampaign | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved: () => void;
}

export function CampaignFormDialog({ ongUuid, campaign, open, onOpenChange, onSaved }: CampaignFormDialogProps) {
  const [values, setValues] = useState<CampaignValues>(campaign ? campaignToValues(campaign) : EMPTY_CAMPAIGN_FORM);
  const [errors, setErrors] = useState<CampaignErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  function bind<K extends Exclude<keyof CampaignValues, "cancelled">>(key: K) {
    return { value: values[key], error: errors[key], disabled: isSaving, onChange: (value: string) => setValues((current) => ({ ...current, [key]: value })) };
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validateCampaign(values);
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      const payload = toCampaignPayload(values);
      if (campaign) await ongPanelService.updateCampaign(ongUuid, campaign.uuid, payload);
      else await ongPanelService.createCampaign(ongUuid, payload);
      toast.success(campaign ? "Caixinha atualizada." : "Caixinha criada.");
      onSaved();
      onOpenChange(false);
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, CAMPAIGN_API_FIELDS));
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível salvar a caixinha. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <RecordFormDialog
      open={open}
      onOpenChange={onOpenChange}
      title={campaign ? "Editar caixinha" : "Nova caixinha"}
      description="Defina a meta e vá atualizando o valor arrecadado. O sistema só registra o progresso, não movimenta dinheiro."
      submitLabel={campaign ? "Salvar alterações" : "Criar caixinha"}
      isSaving={isSaving}
      formError={formError}
      onSubmit={handleSubmit}
    >
      <TextField id="campaign-title" label="Título" required placeholder="Ex.: Reforma do canil" {...bind("title")} />
      <TextAreaField id="campaign-description" label="Descrição" rows={4} placeholder="Para que serve o dinheiro e como ele será usado" {...bind("description")} />
      <div className="grid gap-5 sm:grid-cols-2">
        <TextField id="campaign-goal" label="Meta (R$)" required inputMode="decimal" placeholder="Ex.: 5000" {...bind("goalAmount")} />
        <TextField id="campaign-raised" label="Arrecadado (R$)" inputMode="decimal" placeholder="Ex.: 1200" hint="Atualize sempre que receber doações." {...bind("raisedAmount")} />
      </div>
      <DateField id="campaign-ends" label="Encerra em" hint="Opcional." {...bind("endsAt")} />
      {campaign && (
        <div className="flex items-center gap-3">
          <Switch id="campaign-cancelled" checked={values.cancelled} disabled={isSaving} onCheckedChange={(cancelled) => setValues((current) => ({ ...current, cancelled }))} />
          <Label htmlFor="campaign-cancelled">Caixinha cancelada (some da página pública)</Label>
        </div>
      )}
    </RecordFormDialog>
  );
}
