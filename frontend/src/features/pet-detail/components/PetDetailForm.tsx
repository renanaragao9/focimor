"use client";

import { Fingerprint, Loader2, Salad, Save, Stethoscope, type LucideIcon } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";
import { toast } from "sonner";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { TextAreaField, TextField } from "@/components/FormFields";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { detailService } from "@/features/pet-detail/services/petDetailService";
import type { PetDetail, PetDetailFormValues } from "@/features/pet-detail/types/petDetailTypes";
import { DETAIL_API_FIELDS, detailToFormValues, toDetailPayload, validateDetail, type DetailErrors } from "@/features/pet-detail/validators/petDetailValidators";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";

interface PetDetailFormProps {
  petUuid: string;
  detail: PetDetail | null;
}

function Legend({ icon: Icon, tone, children }: { icon: LucideIcon; tone: string; children: string }) {
  return (
    <legend className="mb-3 flex items-center gap-2 text-sm font-semibold">
      <span className={`flex size-7 items-center justify-center rounded-full ${tone}`}>
        <Icon className="size-4" aria-hidden="true" />
      </span>
      {children}
    </legend>
  );
}

export function PetDetailForm({ petUuid, detail }: PetDetailFormProps) {
  const router = useRouter();
  const [values, setValues] = useState<PetDetailFormValues>(detailToFormValues(detail));
  const [errors, setErrors] = useState<DetailErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  function bind<K extends keyof PetDetailFormValues>(key: K) {
    return { value: values[key], error: errors[key], disabled: isSaving, onChange: (value: string) => setValues((current) => ({ ...current, [key]: value })) };
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validateDetail(values);
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      await detailService.save(petUuid, toDetailPayload(values));
      toast.success("Ficha do pet salva.");
      router.refresh();
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, DETAIL_API_FIELDS));
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível salvar a ficha. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <Card className="border-t-4 border-t-sun">
      <CardHeader>
        <CardTitle className="text-lg">Ficha do pet</CardTitle>
        <CardDescription>Dados que ajudam no cuidado e na identificação. Tudo é opcional.</CardDescription>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit} noValidate className="flex flex-col gap-6">
          {formError && <FormErrorAlert message={formError} />}

          <fieldset className="grid gap-5 sm:grid-cols-2">
            <Legend icon={Fingerprint} tone="bg-grape-soft text-grape">
              Identificação
            </Legend>
            <TextField id="detail-microchip" label="Microchip" placeholder="Ex.: 985112345678901" {...bind("microchip")} />
            <TextField id="detail-pedigree" label="Número do pedigree" placeholder="Ex.: CBKC 123456" {...bind("pedigreeNumber")} />
            <TextField id="detail-coat" label="Pelagem" placeholder="Ex.: curta, longa, ondulada" {...bind("coat")} />
            <TextField id="detail-blood" label="Tipo sanguíneo" placeholder="Ex.: DEA 1.1 positivo" {...bind("bloodType")} />
            <TextField id="detail-temperament" label="Temperamento" placeholder="Ex.: dócil, agitado" {...bind("temperament")} />
          </fieldset>

          <fieldset className="grid gap-5">
            <Legend icon={Salad} tone="bg-mint-soft text-mint">
              Saúde e alimentação
            </Legend>
            <TextAreaField id="detail-allergies" label="Alergias" placeholder="Ex.: frango, picada de pulga, pólen" {...bind("allergies")} />
            <TextAreaField id="detail-chronic" label="Condições crônicas" placeholder="Ex.: displasia, diabetes, problema cardíaco" {...bind("chronicConditions")} />
            <TextAreaField id="detail-diet" label="Alimentação" placeholder="Ração, horários, restrições..." {...bind("diet")} />
          </fieldset>

          <fieldset className="grid gap-5 sm:grid-cols-2">
            <Legend icon={Stethoscope} tone="bg-sky-soft text-sky">
              Veterinário
            </Legend>
            <TextField id="detail-vet-name" label="Nome" placeholder="Ex.: Dra. Mariana Souza" {...bind("vetName")} />
            <TextField id="detail-vet-clinic" label="Clínica" placeholder="Ex.: Clínica Vet Amigo" {...bind("vetClinic")} />
            <TextField id="detail-vet-phone" label="Telefone" type="tel" inputMode="tel" placeholder="(11) 91234-5678" className="sm:max-w-xs" {...bind("vetPhone")} />
          </fieldset>

          <TextAreaField id="detail-notes" label="Observações" rows={4} placeholder="Medos, manias, remédios em uso, qualquer coisa útil para quem cuidar do pet..." {...bind("notes")} />

          <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <Button type="submit" className="order-1 w-full cursor-pointer sm:order-2 sm:w-auto" disabled={isSaving}>
              {isSaving ? <Loader2 className="animate-spin" /> : <Save />}
              {isSaving ? "Salvando..." : "Salvar ficha"}
            </Button>
            <p className="order-2 text-xs text-muted-foreground sm:order-1">{detail?.updated_at ? `Atualizada em ${detail.updated_at}` : "Ainda não preenchida."}</p>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}
