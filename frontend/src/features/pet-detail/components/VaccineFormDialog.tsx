"use client";

import { useState, type FormEvent } from "react";
import { toast } from "sonner";

import { DateField, TextAreaField, TextField } from "@/components/FormFields";
import { RecordFormDialog } from "@/components/RecordFormDialog";
import { vaccineService } from "@/features/pet-detail/services/petDetailService";
import { EMPTY_VACCINE_FORM, type Vaccine, type VaccineFormValues } from "@/features/pet-detail/types/petDetailTypes";
import { VACCINE_API_FIELDS, toVaccinePayload, vaccineToFormValues, validateVaccine, type VaccineErrors } from "@/features/pet-detail/validators/petDetailValidators";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";
import { todayIso } from "@/lib/dates";

interface VaccineFormDialogProps {
  petUuid: string;
  vaccine: Vaccine | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved: () => void;
}

export function VaccineFormDialog({ petUuid, vaccine, open, onOpenChange, onSaved }: VaccineFormDialogProps) {
  const [values, setValues] = useState<VaccineFormValues>(vaccine ? vaccineToFormValues(vaccine) : EMPTY_VACCINE_FORM);
  const [errors, setErrors] = useState<VaccineErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  function bind<K extends keyof VaccineFormValues>(key: K) {
    return { value: values[key], error: errors[key], disabled: isSaving, onChange: (value: string) => setValues((current) => ({ ...current, [key]: value })) };
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validateVaccine(values, todayIso(new Date()));
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      const payload = toVaccinePayload(values);
      if (vaccine) await vaccineService.update(petUuid, vaccine.id, payload);
      else await vaccineService.create(petUuid, payload);
      toast.success(vaccine ? "Vacina atualizada." : "Vacina registrada.");
      onSaved();
      onOpenChange(false);
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, VACCINE_API_FIELDS));
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível salvar a vacina. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <RecordFormDialog
      open={open}
      onOpenChange={onOpenChange}
      title={vaccine ? "Editar vacina" : "Nova vacina"}
      description="Registre a aplicação e, se houver, a data da próxima dose."
      submitLabel={vaccine ? "Salvar alterações" : "Registrar vacina"}
      isSaving={isSaving}
      formError={formError}
      onSubmit={handleSubmit}
    >
      <TextField id="vaccine-name" label="Vacina" required placeholder="Ex.: V10, antirrábica" {...bind("name")} />
      <div className="grid gap-5 sm:grid-cols-2">
        <DateField id="vaccine-applied" label="Aplicada em" required max={todayIso(new Date())} {...bind("appliedAt")} />
        <DateField id="vaccine-next" label="Próxima dose" {...bind("nextDoseAt")} />
      </div>
      <div className="grid gap-5 sm:grid-cols-2">
      <TextField id="vaccine-batch" label="Lote" placeholder="Ex.: 2024A123" {...bind("batch")} />
        <TextField id="vaccine-vet" label="Veterinário" placeholder="Ex.: Dra. Ana Souza" {...bind("veterinarian")} />
      </div>
      <TextAreaField id="vaccine-notes" label="Observações" placeholder="Ex.: reação, clínica ou orientação recebida" {...bind("notes")} />
    </RecordFormDialog>
  );
}
