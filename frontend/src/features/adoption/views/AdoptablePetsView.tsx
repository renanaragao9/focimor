import { PawPrint } from "lucide-react";
import Link from "next/link";
import { redirect } from "next/navigation";

import { SiteShell } from "@/components/app/SiteShell";
import { Pagination } from "@/components/Pagination";
import { Button } from "@/components/ui/button";
import { H1, Muted } from "@/components/ui/typography";
import { AdoptableFilters } from "@/features/adoption/components/AdoptableFilters";
import { fetchAdoptableCatalogs, fetchAdoptablePets } from "@/features/adoption/server/adoptablePetServer";
import { adoptablePetsHref, hasAdoptableFilters, type AdoptablePetParams } from "@/features/adoption/utils/adoptablePetParams";
import { AdoptablePetCard } from "@/features/ong/components/AdoptablePetCard";
import { EmptyState } from "@/features/ong/components/EmptyState";

export async function AdoptablePetsView({ params }: { params: AdoptablePetParams }) {
  const [page, catalogs] = await Promise.all([fetchAdoptablePets(params), fetchAdoptableCatalogs()]);

  if (page.data.length === 0 && params.page > 1) redirect(adoptablePetsHref({ ...params, page: Math.max(page.meta.last_page, 1) }));

  const filtered = hasAdoptableFilters(params);

  return (
    <SiteShell>
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div className="flex flex-col gap-1">
          <H1 className="text-3xl">Adoção</H1>
          <Muted>Pets de ONGs verificadas esperando um lar. Abra um pet para conhecer a história dele.</Muted>
        </div>
        <Button variant="outline" asChild>
          <Link href="/adocao/descobrir">Dê um match na sua adoção</Link>
        </Button>
      </div>

      <AdoptableFilters params={params} species={catalogs.species} sizes={catalogs.sizes} />

      {page.data.length === 0 ? (
        <EmptyState
          icon={PawPrint}
          title="Nenhum pet encontrado"
          text={filtered ? "Tente outros filtros ou limpe a busca." : "Ainda não há pets para adoção. Volte em breve."}
          action={filtered ? { href: "/adocao", label: "Limpar filtros" } : undefined}
        />
      ) : (
        <>
          <div className="grid gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
            {page.data.map((pet) => (
              <AdoptablePetCard key={pet.uuid} pet={pet} />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => adoptablePetsHref({ ...params, page: target })} />
        </>
      )}
    </SiteShell>
  );
}
