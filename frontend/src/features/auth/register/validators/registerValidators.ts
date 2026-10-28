import type { RegisterPayload, RegisterValues } from "@/features/auth/register/types/registerTypes";

export type RegisterErrors = Partial<Record<keyof RegisterValues, string>>;

export interface PasswordRule {
  key: string;
  label: string;
  met: boolean;
}

const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function passwordRules(password: string): PasswordRule[] {
  return [
    { key: "length", label: "8 caracteres ou mais", met: password.length >= 8 },
    { key: "upper", label: "Uma letra maiúscula", met: /\p{Lu}/u.test(password) },
    { key: "lower", label: "Uma letra minúscula", met: /\p{Ll}/u.test(password) },
    { key: "number", label: "Um número", met: /\d/.test(password) },
  ];
}

export function isStrongPassword(password: string): boolean {
  return passwordRules(password).every((rule) => rule.met);
}

export function isValidName(name: string): boolean {
  return name.trim().length >= 2;
}

export function isValidEmail(email: string): boolean {
  return EMAIL.test(email.trim());
}

export function validateRegister(values: RegisterValues): RegisterErrors {
  const errors: RegisterErrors = {};
  if (!isValidName(values.name)) errors.name = "Informe seu nome.";
  if (!isValidEmail(values.email)) errors.email = "Informe um e-mail válido.";
  if (!isStrongPassword(values.password)) errors.password = "A senha não atende a todos os requisitos.";
  if (values.password !== values.confirmPassword) errors.confirmPassword = "As senhas não coincidem.";
  return errors;
}

export function toRegisterPayload(values: RegisterValues): RegisterPayload {
  return {
    name: values.name.trim(),
    email: values.email.trim().toLowerCase(),
    password: values.password,
    password_confirmation: values.confirmPassword,
  };
}

export function maskEmail(email: string): string {
  const [local, domain] = email.split("@");
  if (!domain || local.length <= 2) return email;
  return `${local[0]}${"*".repeat(local.length - 2)}${local[local.length - 1]}@${domain}`;
}

export function isValidVerificationCode(code: string): boolean {
  return /^\d{6}$/.test(code);
}
