"use client";

import { Loader2, Search } from "lucide-react";
import Link from "next/link";
import { useState, type FormEvent } from "react";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { Button } from "@/components/ui/button";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { InputGroup, InputGroupInput } from "@/components/ui/input-group";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { ongRegisterService } from "@/features/ong-register/services/ongRegisterService";
import type { PreRegistrationStatusInfo } from "@/features/ong-register/types/ongRegisterTypes";
import { formatProgressDate, statusInfo } from "@/features/ong-register/utils/statusInfo";
import { toContactValue, validateContact, type ContactType } from "@/features/ong-register/validators/ongRegisterValidators";
import { ApiError } from "@/lib/api/errors";
import { maskPhone } from "@/lib/masks";

export function StatusLookup() {
  const [type, setType] = useState<ContactType>("email");
  const [contact, setContact] = useState("");
  const [error, setError] = useState<string | undefined>();
  const [formError, setFormError] = useState<string | null>(null);
  const [result, setResult] = useState<PreRegistrationStatusInfo | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);
    setResult(null);

    const found = validateContact(type, contact);
    setError(found);
    if (found) return;

    setIsLoading(true);
    try {
      setResult(await ongRegisterService.status(toContactValue(type, contact)));
    } catch (caught) {
      if (caught instanceof ApiError && caught.isValidation) setError(caught.firstError("contact") ?? caught.message);
      else setFormError(caught instanceof ApiError && caught.status === 429 ? "Muitas tentativas. Aguarde alguns minutos e tente novamente." : "Não foi possível consultar agora. Tente novamente.");
    } finally {
      setIsLoading(false);
    }
  }

  const info = result ? statusInfo(result.status) : null;
  const Icon = info?.icon;
  const updated = result ? formatProgressDate(result.updated_at) : null;

  return (
    <div className="flex flex-col gap-6">
      <form onSubmit={handleSubmit} noValidate>
        <FieldGroup className="gap-5">
          {formError && <FormErrorAlert message={formError} />}

          <Field>
            <FieldLabel htmlFor="status-type">Consultar por</FieldLabel>
            <Select
              value={type}
              onValueChange={(value) => {
                setType(value as ContactType);
                setContact("");
                setError(undefined);
                setResult(null);
              }}
            >
              <SelectTrigger id="status-type" className="w-full cursor-pointer">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="email">E-mail</SelectItem>
                <SelectItem value="phone">Telefone</SelectItem>
              </SelectContent>
            </Select>
          </Field>

          <Field data-invalid={Boolean(error)}>
            <FieldLabel htmlFor="status-contact">{type === "email" ? "E-mail do pré-cadastro" : "Telefone do pré-cadastro"}</FieldLabel>
            <InputGroup className="rounded-sm">
              <InputGroupInput
                id="status-contact"
                type={type === "email" ? "email" : "tel"}
                inputMode={type === "email" ? "email" : "numeric"}
                placeholder={type === "email" ? "voce@ong.org" : "00 00000-0000"}
                value={contact}
                onChange={(event) => setContact(type === "phone" ? maskPhone(event.target.value) : event.target.value)}
                aria-invalid={Boolean(error)}
                disabled={isLoading}
              />
            </InputGroup>
            {error && <FieldError>{error}</FieldError>}
          </Field>

          <Button type="submit" size="lg" className="h-12 w-full rounded-sm text-base font-semibold" disabled={isLoading}>
            {isLoading ? <Loader2 className="animate-spin" /> : <Search />}
            {isLoading ? "Consultando..." : "Consultar"}
          </Button>
        </FieldGroup>
      </form>

      {result && info && Icon && (
        <div className="flex flex-col items-center gap-3 rounded-sm border bg-muted/30 p-5 text-center" role="status">
          <span className="flex size-12 items-center justify-center rounded-full bg-brand-soft">
            <Icon className="size-6 text-brand" aria-hidden="true" />
          </span>
          <h2 className="text-xl font-bold">{info.label}</h2>
          <p className="text-sm text-muted-foreground">{info.description}</p>
          {updated && <p className="text-xs text-muted-foreground">Atualizado em {updated}</p>}
          {result.status === "approved" && (
            <Button asChild>
              <Link href="/minhas-ongs">Ir para Minha ONG</Link>
            </Button>
          )}
        </div>
      )}

      <p className="text-center text-sm text-muted-foreground">
        Ainda não enviou?{" "}
        <Link href="/ongs/cadastro" className="font-semibold text-primary hover:underline">
          Faça o pré-cadastro
        </Link>
      </p>
    </div>
  );
}
