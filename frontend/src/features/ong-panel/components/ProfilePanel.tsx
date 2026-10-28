"use client";

import { Loader2, LocateFixed, Save } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";
import { toast } from "sonner";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { TextAreaField, TextField } from "@/components/FormFields";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { Button } from "@/components/ui/button";
import { FieldGroup } from "@/components/ui/field";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { type MyOng, type OngProfileValues } from "@/features/ong-panel/types/ongPanelTypes";
import { isAdmin } from "@/features/ong-panel/utils/panelDisplay";
import { PROFILE_API_FIELDS, profileToValues, toProfilePayload, validateProfile, type ProfileErrors } from "@/features/ong-panel/validators/panelValidators";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";

export function ProfilePanel({ ong }: { ong: MyOng }) {
  const router = useRouter();
  const canEdit = isAdmin(ong);
  const [values, setValues] = useState<OngProfileValues>(() => profileToValues(ong));
  const [errors, setErrors] = useState<ProfileErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);
  const [isLocating, setIsLocating] = useState(false);

  function bind<K extends keyof OngProfileValues>(key: K) {
    return { value: values[key], error: errors[key], disabled: isSaving || !canEdit, onChange: (value: string) => setValues((current) => ({ ...current, [key]: value })) };
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validateProfile(values);
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      await ongPanelService.updateProfile(ong.uuid, toProfilePayload(values));
      toast.success("Dados da ONG atualizados.");
      router.refresh();
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, PROFILE_API_FIELDS));
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível salvar. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  function fillFromBrowser(): void {
    if (!navigator.geolocation) return void toast.error("Seu navegador não suporta localização.");
    setIsLocating(true);
    navigator.geolocation.getCurrentPosition(
      ({ coords }) => {
        setValues((current) => ({ ...current, latitude: coords.latitude.toFixed(6), longitude: coords.longitude.toFixed(6) }));
        setIsLocating(false);
      },
      (error) => {
        toast.error(error.code === error.PERMISSION_DENIED ? "Permita o acesso à localização no navegador." : "Não foi possível obter a localização.");
        setIsLocating(false);
      },
      { enableHighAccuracy: true, timeout: 10000 },
    );
  }

  return (
    <form onSubmit={handleSubmit} noValidate className="flex flex-col gap-6 rounded-sm border bg-card p-5">
      {!canEdit && (
        <Alert>
          <AlertDescription>Só administradores da ONG podem editar os dados e a imagem de perfil.</AlertDescription>
        </Alert>
      )}
      {ong.status !== "verified" && (
        <Alert>
          <AlertDescription>Sua ONG ainda não foi verificada. Enquanto isso, não é possível cadastrar pets nem publicar posts.</AlertDescription>
        </Alert>
      )}

      <div className="flex items-center justify-between gap-3">
        <div>
          <h2 className="text-xl font-semibold">Dados da ONG</h2>
          <p className="text-sm text-muted-foreground">
            {ong.name}
            {ong.cnpj && ` · CNPJ ${ong.cnpj}`}
          </p>
        </div>
      </div>

      <FieldGroup className="gap-5">
        {formError && <FormErrorAlert message={formError} />}
        <TextAreaField id="ong-description" label="Descrição" rows={4} placeholder="Conte a história e o trabalho da ONG" {...bind("description")} />
        <div className="grid gap-5 sm:grid-cols-2">
          <TextField id="ong-email" label="E-mail de contato" type="email" placeholder="contato@ong.org" {...bind("email")} />
          <TextField id="ong-phone" label="Telefone" placeholder="(85) 99999-0000" {...bind("phone")} />
        </div>
        <TextField id="ong-address" label="Endereço" placeholder="Rua, número, bairro" {...bind("address")} />
        <div className="grid gap-5 sm:grid-cols-3">
          <TextField id="ong-city" label="Cidade" className="sm:col-span-2" {...bind("city")} />
          <TextField id="ong-state" label="UF" maxLength={2} placeholder="CE" {...bind("state")} />
        </div>
        <div className="grid gap-5 sm:grid-cols-2">
          <TextField id="ong-lat" label="Latitude" inputMode="decimal" placeholder="-3.7319" hint="Usada para mostrar a distância dos pets." {...bind("latitude")} />
          <TextField id="ong-lon" label="Longitude" inputMode="decimal" placeholder="-38.5267" {...bind("longitude")} />
          {canEdit && (
            <Button type="button" variant="outline" size="sm" className="w-fit cursor-pointer sm:col-span-2" disabled={isLocating || isSaving} onClick={fillFromBrowser}>
              {isLocating ? <Loader2 className="animate-spin" /> : <LocateFixed />} Usar minha localização
            </Button>
          )}
        </div>
        <TextField id="ong-pix" label="Chave Pix" {...bind("pixKey")} />
        <TextAreaField id="ong-donation" label="Como ajudar" rows={3} placeholder="Doações, voluntariado, lista de itens..." {...bind("donationInfo")} />

        {canEdit && (
          <Button type="submit" className="w-full cursor-pointer sm:ml-auto sm:w-auto" disabled={isSaving}>
            {isSaving ? <Loader2 className="animate-spin" /> : <Save />}
            {isSaving ? "Salvando..." : "Salvar alterações"}
          </Button>
        )}
      </FieldGroup>
    </form>
  );
}
