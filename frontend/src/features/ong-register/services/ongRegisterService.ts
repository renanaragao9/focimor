import type { PreRegisterPayload, PreRegistrationStatusInfo } from "@/features/ong-register/types/ongRegisterTypes";
import { api } from "@/lib/api/client";

export const ongRegisterService = {
  preRegister: (payload: PreRegisterPayload) => api<PreRegistrationStatusInfo>("ong-registrations", { method: "POST", json: payload }),
  status: (contact: string) => api<PreRegistrationStatusInfo>("ong-registrations/status", { query: { contact } }),
};
