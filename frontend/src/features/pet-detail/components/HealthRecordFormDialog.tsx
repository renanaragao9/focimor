"use client";

import { useState, type FormEvent } from "react";
import { toast } from "sonner";

import { DateField, TextAreaField, TextField } from "@/components/FormFields";
import { RecordFormDialog } from "@/components/RecordFormDialog";
import { Field, FieldError, FieldLabel } from "@/components/ui/field";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { healthRecordService } from "@/features/pet-detail/services/petDetailService";
import { EMPTY_HEALTH_RECORD_FORM, type HealthRecord, type HealthRecordFormValues, type HealthRecordType } from "@/features/pet-detail/types/petDetailTypes";
import {
  HEALTH_RECORD_API_FIELDS,
  healthRecordToFormValues,
  toHealthRecordPayload,
  validateHealthRecord,
  type HealthRecordErrors,
} from "@/features/pet-detail/validators/petDetailValidators";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";
import { todayIso } from "@/lib/dates";

interface HealthRecordFormDialogProps {
  petUuid: string;
  record: HealthRecord | null;
  types: HealthRecordType[];
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved: () => void;
}

export function HealthRecordFormDialog({ petUuid, record, types, open, onOpenChange, onSaved }: HealthRecordFormDialogProps) {
  const [values, setValues] = useState<HealthRecordFormValues>(record ? healthRecordToFormValues(record) : EMPTY_HEALTH_RECORD_FORM);
  const [errors, setErrors] = useState<HealthRecordErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  function bind<K extends Exclude<keyof HealthRecordFormValues, "typeId">>(key: K) {
    return { value: values[key], error: errors[key], disabled: isSaving, onChange: (value: string) => setValues((current) => ({ ...current, [key]: value })) };
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validateHealthRecord(values, todayIso(new Date()));
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      const payload = toHealthRecordPayload(values);
      if (record) await healthRecordService.update(petUuid, record.id, payload);
      else await healthRecordService.create(petUuid, payload);
      toast.success(record ? "Registro atualizado." : "Registro adicionado.");
      onSaved();
      onOpenChange(false);
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, HEALTH_RECORD_API_FIELDS));
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível salvar o registro. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <RecordFormDialog
      open={open}
      onOpenChange={onOpenChange}
      title={record ? "Editar registro de saúde" : "Novo registro de saúde"}
      description="Consultas, exames, cirurgias, medicações e outros cuidados."
      submitLabel={record ? "Salvar alterações" : "Adicionar registro"}
      isSaving={isSaving}
      formError={formError}
      onSubmit={handleSubmit}
    >
      <div className="grid gap-5 sm:grid-cols-2">
        <Field data-invalid={Boolean(errors.typeId)}>
          <FieldLabel htmlFor="record-type">Tipo *</FieldLabel>
          <Select value={values.typeId} onValueChange={(value) => setValues((current) => ({ ...current, typeId: value }))} disabled={isSaving}>
            <SelectTrigger id="record-type" className="w-full cursor-pointer" aria-invalid={Boolean(errors.typeId)}>
              <SelectValue placeholder="Selecione" />
            </SelectTrigger>
            <SelectContent>
              {types.map((type) => (
                <SelectItem key={type.id} value={String(type.id)}>
                  {type.name}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          {errors.typeId && <FieldError>{errors.typeId}</FieldError>}
        </Field>
        <TextField id="record-title" label="Título" required placeholder="Ex.: Consulta anual" {...bind("title")} />
      </div>
      <div className="grid gap-5 sm:grid-cols-2">
        <DateField id="record-performed" label="Realizado em" required max={todayIso(new Date())} {...bind("performedAt")} />
        <DateField id="record-next" label="Próximo retorno" {...bind("nextDueAt")} />
      </div>
      <div className="grid gap-5 sm:grid-cols-2">
        <TextField id="record-vet" label="Veterinário" placeholder="Ex.: Dr. Carlos Mendes" {...bind("veterinarian")} />
        <TextField id="record-cost" label="Custo (R$)" inputMode="decimal" placeholder="0,00" {...bind("cost")} />
      </div>
      <TextAreaField id="record-description" label="Descrição" rows={4} placeholder="Ex.: sintomas, diagnóstico, exames e orientações" {...bind("description")} />
    </RecordFormDialog>
  );
}
