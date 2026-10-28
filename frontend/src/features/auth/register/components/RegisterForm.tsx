"use client";

import { Eye, EyeOff, Loader2, UserPlus } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { Button } from "@/components/ui/button";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from "@/components/ui/input-group";
import { PasswordRules } from "@/features/auth/register/components/PasswordRules";
import { registerService } from "@/features/auth/register/services/registerService";
import { EMPTY_REGISTER, type RegisterValues } from "@/features/auth/register/types/registerTypes";
import { toRegisterPayload, validateRegister, type RegisterErrors } from "@/features/auth/register/validators/registerValidators";
import { ApiError } from "@/lib/api/errors";

const API_FIELDS: ReadonlyArray<[keyof RegisterValues, string]> = [
  ["name", "name"],
  ["email", "email"],
  ["password", "password"],
];

export function RegisterForm() {
  const router = useRouter();
  const [values, setValues] = useState<RegisterValues>(EMPTY_REGISTER);
  const [errors, setErrors] = useState<RegisterErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);

  function setValue<K extends keyof RegisterValues>(key: K, value: RegisterValues[K]): void {
    setValues((current) => ({ ...current, [key]: value }));
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validateRegister(values);
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsLoading(true);
    try {
      await registerService.register(toRegisterPayload(values));
      router.push(`/verificar-email?email=${encodeURIComponent(values.email.trim().toLowerCase())}`);
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(Object.fromEntries(API_FIELDS.map(([key, apiKey]) => [key, caught.firstError(apiKey)]).filter(([, message]) => message)) as RegisterErrors);
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível criar a conta. Tente novamente.");
      }
      setIsLoading(false);
    }
  }

  return (
    <form onSubmit={handleSubmit} noValidate>
      <FieldGroup className="gap-5">
        {formError && <FormErrorAlert message={formError} />}

        <Field data-invalid={Boolean(errors.name)}>
          <FieldLabel htmlFor="register-name">Nome *</FieldLabel>
          <InputGroup className="h-12 rounded-sm">
            <InputGroupInput
              className="text-base"
              id="register-name"
              autoComplete="name"
              placeholder="Como você quer ser chamado"
              value={values.name}
              onChange={(event) => setValue("name", event.target.value)}
              aria-invalid={Boolean(errors.name)}
              disabled={isLoading}
            />
          </InputGroup>
          {errors.name && <FieldError>{errors.name}</FieldError>}
        </Field>

        <Field data-invalid={Boolean(errors.email)}>
          <FieldLabel htmlFor="register-email">E-mail *</FieldLabel>
          <InputGroup className="h-12 rounded-sm">
            <InputGroupInput
              className="text-base"
              id="register-email"
              type="email"
              autoComplete="email"
              inputMode="email"
              placeholder="voce@email.com"
              value={values.email}
              onChange={(event) => setValue("email", event.target.value)}
              aria-invalid={Boolean(errors.email)}
              disabled={isLoading}
            />
          </InputGroup>
          {errors.email && <FieldError>{errors.email}</FieldError>}
        </Field>

        <Field data-invalid={Boolean(errors.password)}>
          <FieldLabel htmlFor="register-password">Senha *</FieldLabel>
          <InputGroup className="h-12 rounded-sm">
            <InputGroupInput
              className="text-base"
              id="register-password"
              type={showPassword ? "text" : "password"}
              autoComplete="new-password"
              placeholder="Crie uma senha"
              value={values.password}
              onChange={(event) => setValue("password", event.target.value)}
              aria-invalid={Boolean(errors.password)}
              disabled={isLoading}
            />
            <InputGroupAddon align="inline-end">
              <InputGroupButton size="icon-sm" className="rounded-sm" aria-label={showPassword ? "Ocultar senha" : "Mostrar senha"} onClick={() => setShowPassword((visible) => !visible)}>
                {showPassword ? <EyeOff /> : <Eye />}
              </InputGroupButton>
            </InputGroupAddon>
          </InputGroup>
          <PasswordRules password={values.password} />
          {errors.password && <FieldError>{errors.password}</FieldError>}
        </Field>

        <Field data-invalid={Boolean(errors.confirmPassword)}>
          <FieldLabel htmlFor="register-confirm">Confirmar senha *</FieldLabel>
          <InputGroup className="h-12 rounded-sm">
            <InputGroupInput
              className="text-base"
              id="register-confirm"
              type={showPassword ? "text" : "password"}
              autoComplete="new-password"
              placeholder="Repita a senha"
              value={values.confirmPassword}
              onChange={(event) => setValue("confirmPassword", event.target.value)}
              aria-invalid={Boolean(errors.confirmPassword)}
              disabled={isLoading}
            />
          </InputGroup>
          {errors.confirmPassword && <FieldError>{errors.confirmPassword}</FieldError>}
        </Field>

        <Button type="submit" size="lg" className="h-12 w-full rounded-sm text-base font-semibold" disabled={isLoading}>
          {isLoading ? <Loader2 className="animate-spin" /> : <UserPlus />}
          {isLoading ? "Criando conta..." : "Criar conta"}
        </Button>

        <p className="text-center text-sm text-muted-foreground">
          Já tem conta?{" "}
          <Link href="/entrar" className="font-semibold text-primary hover:underline">
            Entrar
          </Link>
        </p>
      </FieldGroup>
    </form>
  );
}
