import type { PreRegisterPayload, PreRegisterValues } from "@/features/ong-register/types/ongRegisterTypes";
import { isValidCnpj, isValidEmail, onlyDigits } from "@/lib/masks";

export type PreRegisterErrors = Partial<Record<keyof PreRegisterValues, string>>;

export const PRE_REGISTER_API_FIELDS = [
  ["name", "name"],
  ["email", "email"],
  ["phone", "phone"],
  ["cnpj", "cnpj"],
  ["city", "city"],
  ["state", "state"],
  ["description", "description"],
] as const;

export function validatePreRegister(values: PreRegisterValues): PreRegisterErrors {
  const errors: PreRegisterErrors = {};
  const phone = onlyDigits(values.phone);

  if (!values.name.trim()) errors.name = "Informe o nome da ONG.";
  else if (values.name.trim().length > 255) errors.name = "O nome não pode ter mais de 255 caracteres.";
  if (!values.email.trim()) errors.email = "Informe o e-mail.";
  else if (!isValidEmail(values.email)) errors.email = "Informe um e-mail válido.";
  if (!phone) errors.phone = "Informe o telefone.";
  else if (phone.length < 10 || phone.length > 11) errors.phone = "Informe o telefone com DDD (10 ou 11 números).";
  if (values.cnpj.trim() && !isValidCnpj(values.cnpj)) errors.cnpj = "O CNPJ informado é inválido.";
  if (!values.city.trim()) errors.city = "Informe a cidade.";
  if (values.state.trim().length !== 2) errors.state = "Informe a UF com 2 letras.";
  if (values.description.length > 1000) errors.description = "O texto não pode ter mais de 1000 caracteres.";
  if (!values.lgpd) errors.lgpd = "Aceite a Política de Privacidade para continuar.";

  return errors;
}

export function toPreRegisterPayload(values: PreRegisterValues): PreRegisterPayload {
  const description = values.description.trim();

  return {
    name: values.name.trim(),
    email: values.email.trim().toLowerCase(),
    phone: onlyDigits(values.phone),
    cnpj: onlyDigits(values.cnpj) || null,
    city: values.city.trim(),
    state: values.state.trim().toUpperCase(),
    description: description === "" ? null : description,
  };
}

export type ContactType = "email" | "phone";

export function validateContact(type: ContactType, contact: string): string | undefined {
  const value = contact.trim();
  if (type === "email") {
    if (!value) return "Informe o e-mail usado no pré-cadastro.";
    return isValidEmail(value) ? undefined : "Informe um e-mail válido.";
  }
  if (!value) return "Informe o telefone usado no pré-cadastro.";
  const digits = onlyDigits(value);
  return digits.length === 10 || digits.length === 11 ? undefined : "Informe o telefone com DDD (10 ou 11 números).";
}

export function toContactValue(type: ContactType, contact: string): string {
  return type === "email" ? contact.trim().toLowerCase() : onlyDigits(contact);
}
