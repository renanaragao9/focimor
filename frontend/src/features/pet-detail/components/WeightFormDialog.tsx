"use client";

import { useState, type FormEvent } from "react";
import { toast } from "sonner";

import { DateField, TextField } from "@/components/FormFields";
import { RecordFormDialog } from "@/components/RecordFormDialog";
import { weightService } from "@/features/pet-detail/services/petDetailService";
import { EMPTY_WEIGHT_FORM, type Weight, type WeightFormValues } from "@/features/pet-detail/types/petDetailTypes";
import { WEIGHT_API_FIELDS, toWeightPayload, validateWeight, weightToFormValues, type WeightErrors } from "@/features/pet-detail/validators/petDetailValidators";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";
import { todayIso } from "@/lib/dates";

interface WeightFormDialogProps {
  petUuid: string;
  weight: Weight | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved: () => void;
}

export function WeightFormDialog({ petUuid, weight, open, onOpenChange, onSaved }: WeightFormDialogProps) {
  const [values, setValues] = useState<WeightFormValues>(weight ? weightToFormValues(weight) : { ...EMPTY_WEIGHT_FORM, measuredAt: todayIso(new Date()) });
  const [errors, setErrors] = useState<WeightErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  function bind<K extends keyof WeightFormValues>(key: K) {
    return { value: values[key], error: errors[key], disabled: isSaving, onChange: (value: string) => setValues((current) => ({ ...current, [key]: value })) };
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validateWeight(values, todayIso(new Date()));
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      const payload = toWeightPayload(values);
      if (weight) await weightService.update(petUuid, weight.id, payload);
      else await weightService.create(petUuid, payload);
      toast.success(weight ? "Medição atualizada." : "Medição registrada.");
      onSaved();
      onOpenChange(false);
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, WEIGHT_API_FIELDS));
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível salvar a medição. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <RecordFormDialog
      open={open}
      onOpenChange={onOpenChange}
      title={weight ? "Editar medição" : "Nova medição"}
      description="Peso, altura e largura do pet em uma data."
      submitLabel={weight ? "Salvar alterações" : "Registrar medição"}
      isSaving={isSaving}
      formError={formError}
      onSubmit={handleSubmit}
    >
      <div className="grid gap-5 sm:grid-cols-2">
        <TextField id="weight-kg" label="Peso (kg)" required inputMode="decimal" placeholder="Ex.: 12,5" {...bind("weightKg")} />
        <DateField id="weight-date" label="Medido em" required max={todayIso(new Date())} {...bind("measuredAt")} />
      </div>
      <div className="grid gap-5 sm:grid-cols-2">
        <TextField id="weight-height" label="Altura (cm)" inputMode="decimal" placeholder="Ex.: 45" {...bind("heightCm")} />
        <TextField id="weight-width" label="Largura (cm)" inputMode="decimal" placeholder="Ex.: 30" {...bind("widthCm")} />
      </div>
    </RecordFormDialog>
  );
}
