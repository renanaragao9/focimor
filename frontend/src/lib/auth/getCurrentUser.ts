import "server-only";

import { cache } from "react";

import { ApiError } from "@/lib/api/errors";
import { apiServer } from "@/lib/api/server";
import type { AuthUser } from "@/lib/auth/authTypes";
import { getToken } from "@/lib/auth/session";

export const getCurrentUser = cache(async (): Promise<AuthUser | null> => {
  if (!(await getToken())) return null;

  try {
    return await apiServer<AuthUser>("me");
  } catch (error) {
    if (error instanceof ApiError && error.isUnauthenticated) return null;
    throw error;
  }
});
