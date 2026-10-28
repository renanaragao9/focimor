import type { LoginCredentials } from "@/lib/auth/authTypes";

export type CredentialFieldErrors = Partial<Record<keyof LoginCredentials, string>>;

const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function validateCredentials({ email, password }: LoginCredentials): CredentialFieldErrors {
  const errors: CredentialFieldErrors = {};

  if (!email.trim()) errors.email = "Informe seu e-mail.";
  else if (!EMAIL_PATTERN.test(email.trim())) errors.email = "Informe um e-mail válido.";

  if (!password) errors.password = "Informe sua senha.";

  return errors;
}

export function resolveRedirect(target: string | null | undefined, fallback: string, areaPrefix: string | readonly string[]): string {
  if (!target || !target.startsWith("/") || target.startsWith("//")) return fallback;
  const prefixes = typeof areaPrefix === "string" ? [areaPrefix] : areaPrefix;
  const allowed = prefixes.some((prefix) => target === prefix || target.startsWith(`${prefix}/`) || target.startsWith(`${prefix}?`));
  return allowed ? target : fallback;
}
