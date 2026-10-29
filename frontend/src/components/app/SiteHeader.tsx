import { PawPrint } from "lucide-react";
import Link from "next/link";
import { Suspense } from "react";

import { MainNav } from "@/components/app/MainNav";
import { UserAccountMenu } from "@/components/app/UserAccountMenu";
import { ThemeToggle } from "@/components/ThemeToggle";
import { Button } from "@/components/ui/button";
import type { AuthUser } from "@/lib/auth/authTypes";
import { getCurrentUser } from "@/lib/auth/getCurrentUser";

function HeaderActions({ user }: { user: AuthUser | null }) {
  if (user) {
    return (
      <div className="flex items-center gap-2">
        <ThemeToggle />
        <UserAccountMenu user={user} />
      </div>
    );
  }

  return (
    <div className="flex items-center gap-2">
      <span className="hidden sm:block">
        <ThemeToggle />
      </span>
      <Button variant="ghost" asChild>
        <Link href="/entrar">Entrar</Link>
      </Button>
      <Button asChild>
        <Link href="/cadastro">Criar conta</Link>
      </Button>
      <Link href="/ongs/cadastro" className="hidden rounded-sm bg-brand-deep px-4 py-3 text-sm font-semibold text-white hover:bg-brand-deep/90 xl:block">
        Cadastre sua ONG
      </Link>
    </div>
  );
}

function HeaderLayout({ user }: { user: AuthUser | null }) {
  const kind = user ? "app" : "public";

  return (
    <header className="sticky top-0 z-40 border-b border-border bg-background">
      <div className="mx-auto flex h-18.5 w-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6">
        <Link href={user ? "/boas-vindas" : "/"} className="flex shrink-0 items-center gap-2" aria-label="Focimor, início">
          <PawPrint className="size-8 text-brand" aria-hidden="true" />
          <span className="hidden text-xl font-bold tracking-tight text-foreground sm:inline">Focimor</span>
        </Link>
        <MainNav kind={kind} variant="desktop" />
        <HeaderActions user={user} />
      </div>
      <MainNav kind={kind} variant="mobile" />
    </header>
  );
}

async function ResolvedHeader() {
  return <HeaderLayout user={await getCurrentUser().catch(() => null)} />;
}

export function SiteHeader({ user }: { user?: AuthUser | null }) {
  if (user !== undefined) return <HeaderLayout user={user} />;

  return (
    <Suspense fallback={<HeaderLayout user={null} />}>
      <ResolvedHeader />
    </Suspense>
  );
}
