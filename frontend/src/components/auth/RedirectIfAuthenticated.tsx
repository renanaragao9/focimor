import { redirect } from "next/navigation";

import { getCurrentUser } from "@/lib/auth/getCurrentUser";

interface RedirectIfAuthenticatedProps {
  destination: string;
}

export async function RedirectIfAuthenticated({ destination }: RedirectIfAuthenticatedProps) {
  let isSignedIn = false;
  try {
    isSignedIn = (await getCurrentUser()) !== null;
  } catch {
    isSignedIn = false;
  }

  if (isSignedIn) redirect(destination);
  return null;
}
