"use client";

import { Eye, EyeOff, Loader2, LockKeyhole, LogIn, Mail } from "lucide-react";
import { useRouter, useSearchParams } from "next/navigation";
import { useState, type FormEvent, type ReactNode } from "react";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { Button } from "@/components/ui/button";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from "@/components/ui/input-group";
import { ApiError } from "@/lib/api/errors";
import type { LoginCredentials } from "@/lib/auth/authTypes";
import { resolveRedirect, validateCredentials, type CredentialFieldErrors } from "@/lib/auth/credentials";

interface CredentialsFormProps {
  authenticate: (credentials: LoginCredentials) => Promise<unknown>;
  defaultDestination: string;
  areaPrefix: string | readonly string[];
  footer?: ReactNode;
  onAuthError?: (error: ApiError, email: string) => boolean;
}

export function CredentialsForm({ authenticate, defaultDestination, areaPrefix, footer, onAuthError }: CredentialsFormProps) {
  const router = useRouter();
  const searchParams = useSearchParams();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [fieldErrors, setFieldErrors] = useState<CredentialFieldErrors>({});
  const [formError, setFormError] = useState<string | null>(null);

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const errors = validateCredentials({ email, password });
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    setIsLoading(true);
    try {
      await authenticate({ email: email.trim(), password });
      router.replace(resolveRedirect(searchParams.get("redirect"), defaultDestination, areaPrefix));
      router.refresh();
    } catch (error) {
      if (error instanceof ApiError && onAuthError?.(error, email.trim())) return;
      setFormError(error instanceof ApiError ? error.message : "Não foi possível entrar. Tente novamente.");
      setIsLoading(false);
    }
  }

  return (
    <form onSubmit={handleSubmit} noValidate>
      <FieldGroup className="gap-6">
        {formError && <FormErrorAlert message={formError} />}

        <Field data-invalid={Boolean(fieldErrors.email)}>
          <FieldLabel htmlFor="email" className="text-base">
            E-mail
          </FieldLabel>
          <InputGroup className="h-12 rounded-sm">
            <InputGroupAddon>
              <Mail className="text-brand" />
            </InputGroupAddon>
            <InputGroupInput
              className="text-base"
              id="email"
              type="email"
              autoComplete="email"
              inputMode="email"
              placeholder="voce@email.com"
              value={email}
              onChange={(event) => setEmail(event.target.value)}
              aria-invalid={Boolean(fieldErrors.email)}
              disabled={isLoading}
            />
          </InputGroup>
          {fieldErrors.email && <FieldError>{fieldErrors.email}</FieldError>}
        </Field>

        <Field data-invalid={Boolean(fieldErrors.password)}>
          <FieldLabel htmlFor="password" className="text-base">
            Senha
          </FieldLabel>
          <InputGroup className="h-12 rounded-sm">
            <InputGroupAddon>
              <LockKeyhole className="text-brand" />
            </InputGroupAddon>
            <InputGroupInput
              className="text-base"
              id="password"
              type={showPassword ? "text" : "password"}
              autoComplete="current-password"
              placeholder="Sua senha"
              value={password}
              onChange={(event) => setPassword(event.target.value)}
              aria-invalid={Boolean(fieldErrors.password)}
              disabled={isLoading}
            />
            <InputGroupAddon align="inline-end">
              <InputGroupButton size="icon-sm" className="rounded-sm" aria-label={showPassword ? "Ocultar senha" : "Mostrar senha"} onClick={() => setShowPassword((visible) => !visible)}>
                {showPassword ? <EyeOff /> : <Eye />}
              </InputGroupButton>
            </InputGroupAddon>
          </InputGroup>
          {fieldErrors.password && <FieldError>{fieldErrors.password}</FieldError>}
        </Field>

        <Button type="submit" size="lg" className="h-12 w-full rounded-sm text-base font-semibold" disabled={isLoading}>
          {isLoading ? <Loader2 className="animate-spin" /> : <LogIn />}
          {isLoading ? "Entrando..." : "Entrar"}
        </Button>

        {footer}
      </FieldGroup>
    </form>
  );
}
