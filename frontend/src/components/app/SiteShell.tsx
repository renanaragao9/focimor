import type { ReactNode } from "react";

import { BackButton } from "@/components/app/BackButton";
import { SiteHeader } from "@/components/app/SiteHeader";
import type { AuthUser } from "@/lib/auth/authTypes";

interface SiteShellProps {
  user?: AuthUser | null;
  children: ReactNode;
}

export function SiteShell({ user, children }: SiteShellProps) {
  return (
    <div className="flex min-h-dvh min-w-0 flex-col overflow-x-hidden">
      <SiteHeader user={user} />
      <main className="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 px-4 py-8 sm:px-6">
        <BackButton />
        {children}
      </main>
    </div>
  );
}
