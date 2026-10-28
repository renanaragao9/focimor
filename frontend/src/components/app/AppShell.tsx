import type { ReactNode } from "react";

import { SiteShell } from "@/components/app/SiteShell";
import type { AuthUser } from "@/lib/auth/authTypes";

export function AppShell({ user, children }: { user: AuthUser; children: ReactNode }) {
  return <SiteShell user={user}>{children}</SiteShell>;
}
