import { BadgeCheck, Building2, ClipboardList, Dog, HeartHandshake, MessagesSquare, Newspaper, Search, Sparkles, type LucideIcon } from "lucide-react";
import Link from "next/link";

import { AppShell } from "@/components/app/AppShell";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { H1, Lead } from "@/components/ui/typography";
import { firstName, initials } from "@/features/auth/welcome/utils/welcomeDisplay";
import type { WelcomeCounts } from "@/features/auth/welcome/server/welcomeServer";
import type { AuthUser } from "@/lib/auth/authTypes";

interface WelcomeViewProps {
  user: AuthUser;
  counts: WelcomeCounts;
}

interface Step {
  icon: LucideIcon;
  title: string;
  description: string;
  href: string;
  count?: keyof WelcomeCounts;
  countLabel?: [string, string];
}

const NEXT_STEPS: readonly Step[] = [
  { icon: Dog, title: "Meus pets", description: "Cadastre seus pets e fotos para começar.", href: "/meus-pets", count: "pets", countLabel: ["pet", "pets"] },
  { icon: Sparkles, title: "Match", description: "Deslize, dê match e conheça outros pets.", href: "/match" },
  { icon: MessagesSquare, title: "Matches e conversas", description: "Continue os papos com quem deu match.", href: "/matches", count: "matches", countLabel: ["match", "matches"] },
  { icon: HeartHandshake, title: "Dê um match na sua adoção", description: "Deslize pets de ONGs e demonstre interesse.", href: "/adocao/descobrir" },
  {
    icon: ClipboardList,
    title: "Meus interesses",
    description: "Acompanhe seus pedidos de adoção e converse com a ONG.",
    href: "/adocoes",
    count: "adoptions",
    countLabel: ["interesse", "interesses"],
  },
  { icon: Building2, title: "Minha ONG", description: "Gerencie pets, novidades, caixinhas e equipe.", href: "/minhas-ongs", count: "ongs", countLabel: ["ONG", "ONGs"] },
  { icon: Search, title: "ONGs", description: "Conheça as ONGs verificadas e ajude nas caixinhas.", href: "/ongs" },
  { icon: Newspaper, title: "Novidades", description: "Acompanhe campanhas e avisos das ONGs.", href: "/posts" },
];

function countText(step: Step, counts: WelcomeCounts): string | null {
  const value = step.count ? counts[step.count] : null;
  if (value === null || value === undefined || !step.countLabel) return null;
  return `${value} ${value === 1 ? step.countLabel[0] : step.countLabel[1]}`;
}

export function WelcomeView({ user, counts }: WelcomeViewProps) {
  return (
    <AppShell user={user}>
      <div className="mx-auto flex w-full max-w-4xl flex-col gap-10">
        <section className="flex flex-col items-center gap-5 text-center">
          <Avatar className="size-24 border-4 border-brand-soft">
            {user.avatar_url && <AvatarImage src={user.avatar_url} alt="" />}
            <AvatarFallback className="bg-brand-soft text-2xl font-bold text-brand-deep">{initials(user.name)}</AvatarFallback>
          </Avatar>
          <div className="flex flex-col items-center gap-3">
            <H1 className="text-balance">
              Olá, <span className="text-brand">{firstName(user.name)}</span>!
            </H1>
            <Lead className="max-w-xl">Que bom ter você no Focimor. Escolha por onde quer começar.</Lead>
            {user.email_verified_at && (
              <Badge variant="secondary" className="gap-1">
                <BadgeCheck className="size-3.5 text-success" /> E-mail verificado
              </Badge>
            )}
          </div>
        </section>

        <section aria-labelledby="next-steps" className="flex flex-col gap-4">
          <h2 id="next-steps" className="text-center text-sm font-semibold uppercase tracking-wide text-muted-foreground">
            Atalhos
          </h2>
          <div className="grid gap-3 sm:grid-cols-2 sm:gap-4">
            {NEXT_STEPS.map((step) => {
              const { icon: Icon, title, description, href } = step;
              const text = countText(step, counts);

              return (
                <Link key={title} href={href} className="rounded-sm focus-visible:outline-2 focus-visible:outline-ring">
                  <Card className="h-full transition-colors hover:border-brand">
                    <CardHeader>
                      <div className="flex items-start gap-3">
                        <span className="flex size-11 shrink-0 items-center justify-center rounded-sm bg-brand-soft">
                          <Icon className="size-6 text-brand" aria-hidden="true" />
                        </span>
                        <div className="flex min-w-0 flex-col gap-1">
                          <CardTitle className="flex flex-wrap items-center gap-2">
                            {title}
                            {text && <Badge variant="secondary">{text}</Badge>}
                          </CardTitle>
                          <CardDescription>{description}</CardDescription>
                        </div>
                      </div>
                    </CardHeader>
                  </Card>
                </Link>
              );
            })}
          </div>
        </section>
      </div>
    </AppShell>
  );
}
