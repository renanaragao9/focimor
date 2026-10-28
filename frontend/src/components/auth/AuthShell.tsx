import { PawPrint, type LucideIcon } from "lucide-react";
import Link from "next/link";
import { Suspense, type ReactNode } from "react";

import { AuthBackdrop } from "@/components/auth/AuthBackdrop";
import { RedirectIfAuthenticated } from "@/components/auth/RedirectIfAuthenticated";
import { ThemeToggle } from "@/components/ThemeToggle";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { cn } from "@/lib/utils";

interface AuthShellProps {
  signedInDestination?: string;
  hero: ReactNode;
  icon?: LucideIcon;
  title: string;
  description: string;
  cardClassName?: string;
  children: ReactNode;
}

export function HeroBrand() {
  return (
    <div className="ml-10 flex items-center gap-3 animate-in fade-in slide-in-from-left-4 duration-500">
      <span className="flex size-14 shrink-0 items-center justify-center rounded-full bg-brand text-brand-foreground">
        <PawPrint className="size-8" aria-hidden="true" />
      </span>
      <span className="h-8 w-px shrink-0 bg-foreground/30" aria-hidden="true" />
      <span className="text-3xl font-extrabold tracking-tight">Focimor</span>
    </div>
  );
}

export function AuthShell({ signedInDestination, hero, icon: Icon, title, description, cardClassName, children }: AuthShellProps) {
  return (
    <div className="relative flex min-h-dvh flex-col overflow-hidden lg:h-dvh">
      <AuthBackdrop />

      <main className="relative z-10 grid min-h-0 flex-1 lg:grid-cols-[55fr_45fr]">
        {signedInDestination && (
          <Suspense>
            <RedirectIfAuthenticated destination={signedInDestination} />
          </Suspense>
        )}

        <section className="hidden min-h-0 lg:block">{hero}</section>

        <section className="relative flex min-h-0 flex-col items-center gap-6 overflow-y-auto p-0 sm:px-6 sm:py-10">
          <div className="absolute right-4 top-4 rounded-sm bg-card/80 backdrop-blur">
            <ThemeToggle />
          </div>

          <Card
            className={cn(
              "w-full max-w-lg flex-1 rounded-none border-x-0 border-t-4 border-t-brand shadow-2xl shadow-black/20 animate-in fade-in slide-in-from-bottom-6 duration-500 sm:my-auto sm:flex-none sm:shrink-0 sm:rounded-sm sm:border-x [--card-spacing:--spacing(8)]",
              cardClassName,
            )}
          >
            <CardHeader>
              <div className="mt-10 mb-4 flex items-center justify-center gap-3 sm:mt-0 lg:hidden">
                <span className="flex size-12 items-center justify-center rounded-full bg-brand text-brand-foreground">
                  <PawPrint className="size-7" aria-hidden="true" />
                </span>
                <span className="text-2xl font-extrabold tracking-tight">Focimor</span>
              </div>
              <div className={cn("flex items-center gap-3.5", !Icon && "flex-col text-center")}>
                {Icon && (
                  <span className="flex size-14 shrink-0 items-center justify-center rounded-sm bg-brand-soft">
                    <Icon className="size-7 text-brand" aria-hidden="true" />
                  </span>
                )}
                <div className={cn("flex flex-col gap-0.5", !Icon && "items-center")}>
                  <CardTitle className={cn("text-2xl", !Icon && "text-3xl")}>{title}</CardTitle>
                  <CardDescription className={cn("text-base", !Icon && "text-lg")}>{description}</CardDescription>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              {children}
              <div className="mt-4 text-center text-sm">
                <Link href="/" className="font-semibold text-primary hover:underline">
                  Início
                </Link>
              </div>
            </CardContent>
          </Card>
        </section>
      </main>
    </div>
  );
}
