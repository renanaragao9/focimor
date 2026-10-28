"use client";

import { Loader2, Send } from "lucide-react";
import Link from "next/link";
import { useState, type FormEvent } from "react";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { InputGroup, InputGroupInput } from "@/components/ui/input-group";
import { Textarea } from "@/components/ui/textarea";
import { ongRegisterService } from "@/features/ong-register/services/ongRegisterService";
import { EMPTY_PRE_REGISTER, type PreRegisterValues } from "@/features/ong-register/types/ongRegisterTypes";
import { PRE_REGISTER_API_FIELDS, toPreRegisterPayload, validatePreRegister, type PreRegisterErrors } from "@/features/ong-register/validators/ongRegisterValidators";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";
import { maskCnpj, maskPhone } from "@/lib/masks";

interface RegisterFormProps {
  onRegistered: (email: string) => void;
}

const TOO_MANY_ATTEMPTS = 429;

export function RegisterForm({ onRegistered }: RegisterFormProps) {
  const [values, setValues] = useState<PreRegisterValues>(EMPTY_PRE_REGISTER);
  const [errors, setErrors] = useState<PreRegisterErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  function setValue<K extends keyof PreRegisterValues>(key: K, value: PreRegisterValues[K]): void {
    setValues((current) => ({ ...current, [key]: value }));
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validatePreRegister(values);
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsLoading(true);
    try {
      await ongRegisterService.preRegister(toPreRegisterPayload(values));
      onRegistered(values.email.trim());
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, PRE_REGISTER_API_FIELDS));
        setFormError(caught.status === TOO_MANY_ATTEMPTS ? "Muitas tentativas. Aguarde alguns minutos e tente novamente." : caught.message);
      } else {
        setFormError("Não foi possível enviar o pré-cadastro. Tente novamente.");
      }
      setIsLoading(false);
    }
  }

  return (
    <form onSubmit={handleSubmit} noValidate>
      <FieldGroup className="gap-6">
        {formError && <FormErrorAlert message={formError} />}

        <Field data-invalid={Boolean(errors.name)}>
          <FieldLabel htmlFor="ong-register-name">Nome da ONG *</FieldLabel>
          <InputGroup className="rounded-sm">
            <InputGroupInput
              id="ong-register-name"
              autoComplete="organization"
              placeholder="Ex.: Patas do Bem"
              value={values.name}
              onChange={(event) => setValue("name", event.target.value)}
              aria-invalid={Boolean(errors.name)}
              disabled={isLoading}
            />
          </InputGroup>
          {errors.name && <FieldError>{errors.name}</FieldError>}
        </Field>

        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <Field data-invalid={Boolean(errors.email)}>
            <FieldLabel htmlFor="ong-register-email">E-mail *</FieldLabel>
            <InputGroup className="rounded-sm">
              <InputGroupInput
                id="ong-register-email"
                type="email"
                autoComplete="email"
                inputMode="email"
                placeholder="voce@ong.org"
                value={values.email}
                onChange={(event) => setValue("email", event.target.value)}
                aria-invalid={Boolean(errors.email)}
                disabled={isLoading}
              />
            </InputGroup>
            {errors.email ? <FieldError>{errors.email}</FieldError> : <p className="text-xs text-muted-foreground">Use o e-mail da conta que vai administrar a ONG.</p>}
          </Field>

          <Field data-invalid={Boolean(errors.phone)}>
            <FieldLabel htmlFor="ong-register-phone">Telefone *</FieldLabel>
            <InputGroup className="rounded-sm">
              <InputGroupInput
                id="ong-register-phone"
                type="tel"
                autoComplete="tel"
                inputMode="numeric"
                placeholder="00 00000-0000"
                value={values.phone}
                onChange={(event) => setValue("phone", maskPhone(event.target.value))}
                aria-invalid={Boolean(errors.phone)}
                disabled={isLoading}
              />
            </InputGroup>
            {errors.phone && <FieldError>{errors.phone}</FieldError>}
          </Field>
        </div>

        <Field data-invalid={Boolean(errors.cnpj)}>
          <FieldLabel htmlFor="ong-register-cnpj">CNPJ</FieldLabel>
          <InputGroup className="rounded-sm">
            <InputGroupInput
              id="ong-register-cnpj"
              inputMode="numeric"
              placeholder="00.000.000/0000-00"
              value={values.cnpj}
              onChange={(event) => setValue("cnpj", maskCnpj(event.target.value))}
              aria-invalid={Boolean(errors.cnpj)}
              disabled={isLoading}
            />
          </InputGroup>
          {errors.cnpj ? <FieldError>{errors.cnpj}</FieldError> : <p className="text-xs text-muted-foreground">Opcional. Ajuda a agilizar a análise.</p>}
        </Field>

        <div className="grid grid-cols-[1fr_6rem] gap-6">
          <Field data-invalid={Boolean(errors.city)}>
            <FieldLabel htmlFor="ong-register-city">Cidade *</FieldLabel>
            <InputGroup className="rounded-sm">
              <InputGroupInput
                id="ong-register-city"
                autoComplete="address-level2"
                placeholder="Ex.: Fortaleza"
                value={values.city}
                onChange={(event) => setValue("city", event.target.value)}
                aria-invalid={Boolean(errors.city)}
                disabled={isLoading}
              />
            </InputGroup>
            {errors.city && <FieldError>{errors.city}</FieldError>}
          </Field>

          <Field data-invalid={Boolean(errors.state)}>
            <FieldLabel htmlFor="ong-register-state">UF *</FieldLabel>
            <InputGroup className="rounded-sm">
              <InputGroupInput
                id="ong-register-state"
                autoComplete="address-level1"
                placeholder="CE"
                maxLength={2}
                className="text-center uppercase"
                value={values.state}
                onChange={(event) => setValue("state", event.target.value.replace(/[^a-zA-Z]/g, "").toUpperCase())}
                aria-invalid={Boolean(errors.state)}
                disabled={isLoading}
              />
            </InputGroup>
            {errors.state && <FieldError>{errors.state}</FieldError>}
          </Field>
        </div>

        <Field data-invalid={Boolean(errors.description)}>
          <FieldLabel htmlFor="ong-register-description">Conte sobre a ONG</FieldLabel>
          <Textarea
            id="ong-register-description"
            rows={3}
            placeholder="Há quanto tempo atua, quantos animais cuida, como ajuda..."
            value={values.description}
            onChange={(event) => setValue("description", event.target.value)}
            aria-invalid={Boolean(errors.description)}
            disabled={isLoading}
          />
          {errors.description && <FieldError>{errors.description}</FieldError>}
        </Field>

        <Field data-invalid={Boolean(errors.lgpd)}>
          <div className="flex items-start gap-2">
            <Checkbox
              id="ong-register-lgpd"
              checked={values.lgpd}
              onCheckedChange={(checked) => setValue("lgpd", checked === true)}
              aria-invalid={Boolean(errors.lgpd)}
              disabled={isLoading}
              className="mt-0.5"
            />
            <FieldLabel htmlFor="ong-register-lgpd" className="font-normal">
              Concordo com a Política de Privacidade e o uso dos meus dados para análise do cadastro (LGPD).
            </FieldLabel>
          </div>
          {errors.lgpd && <FieldError>{errors.lgpd}</FieldError>}
        </Field>

        <Button type="submit" size="lg" className="h-12 w-full rounded-sm text-base font-semibold" disabled={isLoading}>
          {isLoading ? <Loader2 className="animate-spin" /> : <Send />}
          {isLoading ? "Enviando..." : "Enviar pré-cadastro"}
        </Button>

        <p className="text-center text-sm text-muted-foreground">
          Já enviou?{" "}
          <Link href="/ongs/cadastro/status" className="font-semibold text-primary hover:underline">
            Acompanhe o seu pré-cadastro
          </Link>
        </p>
      </FieldGroup>
    </form>
  );
}
