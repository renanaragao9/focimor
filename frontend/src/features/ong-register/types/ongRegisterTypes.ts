export type PreRegistrationStatus = "pending" | "contacted" | "approved" | "rejected";

export interface PreRegisterValues {
  name: string;
  email: string;
  phone: string;
  cnpj: string;
  city: string;
  state: string;
  description: string;
  lgpd: boolean;
}

export const EMPTY_PRE_REGISTER: PreRegisterValues = { name: "", email: "", phone: "", cnpj: "", city: "", state: "", description: "", lgpd: false };

export interface PreRegisterPayload {
  name: string;
  email: string;
  phone: string;
  cnpj: string | null;
  city: string;
  state: string;
  description: string | null;
}

export interface PreRegistrationStatusInfo {
  status: PreRegistrationStatus;
  status_label: string;
  created_at: string | null;
  updated_at: string | null;
}
