import { HeartHandshake } from "lucide-react";
import Link from "next/link";
import { redirect } from "next/navigation";

import { SiteShell } from "@/components/app/SiteShell";
import { Pagination } from "@/components/Pagination";
import { H1, Muted } from "@/components/ui/typography";
import { EmptyState } from "@/features/ong/components/EmptyState";
import { ListSearch } from "@/features/ong/components/ListSearch";
import { OngCard } from "@/features/ong/components/OngCard";
import { fetchOngs } from "@/features/ong/server/ongServer";
import { listHref, type ListParams } from "@/features/ong/utils/ongParams";

export async function OngsView({ params }: { params: ListParams }) {
  const page = await fetchOngs(params);

  if (page.data.length === 0 && params.page > 1) redirect(listHref("/ongs", { ...params, page: Math.max(page.meta.last_page, 1) }));

  return (
    <SiteShell>
      <div className="flex flex-col gap-1">
        <H1 className="text-3xl">ONGs</H1>
        <Muted>Conheça quem cuida de pets que procuram um lar.</Muted>
      </div>

      <p className="text-sm text-muted-foreground">
        Tem uma ONG ou abrigo?{" "}
        <Link href="/ongs/cadastro" className="font-semibold text-primary hover:underline">
          Faça o pré-cadastro
        </Link>
      </p>

      <ListSearch basePath="/ongs" value={params.search} placeholder="Buscar ONG pelo nome" />

      {page.data.length === 0 ? (
        <EmptyState
          icon={HeartHandshake}
          title="Nenhuma ONG encontrada"
          text={params.search ? "Tente outro nome ou limpe a busca." : "Ainda não há ONGs verificadas."}
          action={params.search ? { href: "/ongs", label: "Limpar busca" } : undefined}
        />
      ) : (
        <>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {page.data.map((ong) => (
              <OngCard key={ong.uuid} ong={ong} />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => listHref("/ongs", { ...params, page: target })} />
        </>
      )}
    </SiteShell>
  );
}
