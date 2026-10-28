import { api } from "@/lib/api/client";
import type { AuthUser, LoginCredentials } from "@/lib/auth/authTypes";

export const authService = {
  enter: (credentials: LoginCredentials) => api<AuthUser>("auth/enter", { method: "POST", json: credentials }),
  logout: () => api<null>("auth/logout", { method: "POST" }),
  me: () => api<AuthUser>("me"),
};
