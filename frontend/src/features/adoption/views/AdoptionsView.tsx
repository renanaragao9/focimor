import { HeartHandshake, Inbox } from "lucide-react";
import { redirect } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { Pagination } from "@/components/Pagination";
import { H1, Muted } from "@/components/ui/typography";
import { AdoptionRow } from "@/features/adoption/components/AdoptionRow";
import { AdoptionTabs } from "@/features/adoption/components/AdoptionTabs";
import { fetchAdoptions } from "@/features/adoption/server/adoptionServer";
import type { AdoptionsParams } from "@/features/adoption/types/adoptionTypes";
import { adoptionsHref } from "@/features/adoption/utils/adoptionParams";
import { EmptyState } from "@/features/ong/components/EmptyState";
import type { AuthUser } from "@/lib/auth/authTypes";

export async function AdoptionsView({ user, params }: { user: AuthUser; params: AdoptionsParams }) {
  const page = await fetchAdoptions(params);

  if (page.data.length === 0 && params.page > 1) redirect(adoptionsHref({ ...params, page: Math.max(page.meta.last_page, 1) }));

  const received = params.tab === "recebidos";

  return (
    <AppShell user={user}>
      <div className="flex flex-col gap-1">
        <H1 className="text-3xl">Adoções</H1>
        <Muted>{received ? "Pessoas interessadas nos pets sob a sua responsabilidade." : "Os pets pelos quais você demonstrou interesse."}</Muted>
      </div>

      <AdoptionTabs params={params} />

      {page.data.length === 0 ? (
        <EmptyState
          icon={received ? Inbox : HeartHandshake}
          title={params.status ? "Nada com essa situação" : received ? "Nenhum interesse recebido" : "Você ainda não demonstrou interesse"}
          text={
            params.status
              ? "Escolha outra situação ou veja todas."
              : received
                ? "Quando alguém curtir um pet seu para adoção, aparece aqui."
                : "Curta pets na tela de Adoção para enviar seu interesse."
          }
          action={params.status ? { href: adoptionsHref({ tab: params.tab }), label: "Ver todas" } : received ? undefined : { href: "/adocao", label: "Ir para Adoção" }}
        />
      ) : (
        <>
          <div className="flex flex-col gap-2">
            {page.data.map((adoption) => (
              <AdoptionRow key={adoption.uuid} adoption={adoption} />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => adoptionsHref({ ...params, page: target })} />
        </>
      )}
    </AppShell>
  );
}
