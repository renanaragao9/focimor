import "server-only";

import { redirect } from "next/navigation";

import type { AuthUser } from "@/lib/auth/authTypes";
import { getCurrentUser } from "@/lib/auth/getCurrentUser";

export async function requireUser(signInPath: string, returnTo: string): Promise<AuthUser> {
  const user = await getCurrentUser();

  if (!user) redirect(`${signInPath}?redirect=${encodeURIComponent(returnTo)}`);
  return user;
}
