import type { RegisterPayload } from "@/features/auth/register/types/registerTypes";
import { api } from "@/lib/api/client";

export const registerService = {
  register: (payload: RegisterPayload) => api<{ requires_verification: boolean }>("auth/register", { method: "POST", json: payload }),
  verifyEmail: (email: string, code: string) => api<unknown>("auth/verify-email", { method: "POST", json: { email, code } }),
  resendCode: (email: string) => api<unknown>("auth/resend-verification-code", { method: "POST", json: { email } }),
};
