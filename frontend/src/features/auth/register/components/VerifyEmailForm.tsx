"use client";

import { CircleCheck, Loader2, MailCheck, RefreshCw } from "lucide-react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useEffect, useState, type FormEvent } from "react";
import { toast } from "sonner";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { Button } from "@/components/ui/button";
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { registerService } from "@/features/auth/register/services/registerService";
import { RESEND_COOLDOWN_SECONDS, VERIFICATION_CODE_LENGTH } from "@/features/auth/register/types/registerTypes";
import { isValidEmail, isValidVerificationCode, maskEmail } from "@/features/auth/register/validators/registerValidators";
import { ApiError } from "@/lib/api/errors";

export function VerifyEmailForm() {
  const initialEmail = useSearchParams().get("email") ?? "";
  const [email, setEmail] = useState(initialEmail);
  const [code, setCode] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [isVerifying, setIsVerifying] = useState(false);
  const [isResending, setIsResending] = useState(false);
  const [secondsLeft, setSecondsLeft] = useState(0);
  const [isVerified, setIsVerified] = useState(false);

  useEffect(() => {
    if (secondsLeft <= 0) return;
    const timer = window.setTimeout(() => setSecondsLeft((current) => current - 1), 1000);
    return () => window.clearTimeout(timer);
  }, [secondsLeft]);

  async function handleVerify(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setError(null);
    if (!isValidEmail(email)) return setError("Informe um e-mail válido.");
    if (!isValidVerificationCode(code)) return setError(`O código tem ${VERIFICATION_CODE_LENGTH} dígitos.`);

    setIsVerifying(true);
    try {
      await registerService.verifyEmail(email.trim().toLowerCase(), code);
      setIsVerified(true);
    } catch (caught) {
      setError(caught instanceof ApiError ? caught.message : "Não foi possível verificar o e-mail. Tente novamente.");
    } finally {
      setIsVerifying(false);
    }
  }

  async function handleResend(): Promise<void> {
    setError(null);
    if (!isValidEmail(email)) return setError("Informe um e-mail válido.");

    setIsResending(true);
    try {
      await registerService.resendCode(email.trim().toLowerCase());
      toast.success("Enviamos um novo código.");
      setCode("");
      setSecondsLeft(RESEND_COOLDOWN_SECONDS);
    } catch (caught) {
      setError(caught instanceof ApiError ? caught.message : "Não foi possível reenviar o código.");
    } finally {
      setIsResending(false);
    }
  }

  if (isVerified) {
    return (
      <div className="flex flex-col items-center gap-5 py-4 text-center" role="status">
        <span className="flex size-16 items-center justify-center rounded-full bg-brand-soft">
          <CircleCheck className="size-9 text-brand" aria-hidden="true" />
        </span>
        <div className="flex flex-col gap-2">
          <h2 className="text-2xl font-bold">E-mail verificado</h2>
          <p className="text-muted-foreground">Sua conta está ativa. Entre para começar a dar match.</p>
        </div>
        <Button size="lg" className="h-12 w-full rounded-sm font-semibold" asChild>
          <Link href="/entrar">Ir para entrar</Link>
        </Button>
      </div>
    );
  }

  return (
    <form onSubmit={handleVerify} noValidate>
      <FieldGroup className="gap-5">
        {error && <FormErrorAlert message={error} />}

        {initialEmail ? (
          <p className="text-sm text-muted-foreground">
            Enviamos um código de {VERIFICATION_CODE_LENGTH} dígitos para <strong className="text-foreground">{maskEmail(initialEmail)}</strong>. Ele vale por 15 minutos.
          </p>
        ) : (
          <Field>
            <FieldLabel htmlFor="verify-email">E-mail *</FieldLabel>
            <Input
              id="verify-email"
              type="email"
              autoComplete="email"
              inputMode="email"
              className="h-12 rounded-sm text-base"
              placeholder="voce@email.com"
              value={email}
              onChange={(event) => setEmail(event.target.value)}
              disabled={isVerifying}
            />
          </Field>
        )}

        <Field data-invalid={error !== null && !isValidVerificationCode(code)}>
          <FieldLabel htmlFor="verify-code">Código de verificação *</FieldLabel>
          <Input
            id="verify-code"
            inputMode="numeric"
            autoComplete="one-time-code"
            maxLength={VERIFICATION_CODE_LENGTH}
            className="h-14 rounded-sm text-center text-2xl font-bold tracking-[0.5em] tabular-nums"
            placeholder="······"
            value={code}
            onChange={(event) => setCode(event.target.value.replace(/\D/g, "").slice(0, VERIFICATION_CODE_LENGTH))}
            disabled={isVerifying}
          />
        </Field>

        <Button type="submit" size="lg" className="h-12 w-full rounded-sm text-base font-semibold" disabled={isVerifying}>
          {isVerifying ? <Loader2 className="animate-spin" /> : <MailCheck />}
          {isVerifying ? "Verificando..." : "Verificar e-mail"}
        </Button>

        <Button type="button" variant="outline" size="lg" className="h-12 w-full rounded-sm font-semibold" disabled={isResending || secondsLeft > 0} onClick={() => void handleResend()}>
          {isResending ? <Loader2 className="animate-spin" /> : <RefreshCw />}
          {secondsLeft > 0 ? `Reenviar código em ${secondsLeft}s` : "Reenviar código"}
        </Button>

        <p className="text-center text-sm text-muted-foreground">
          <Link href="/entrar" className="font-semibold text-primary hover:underline">
            Voltar para entrar
          </Link>
        </p>
      </FieldGroup>
    </form>
  );
}
