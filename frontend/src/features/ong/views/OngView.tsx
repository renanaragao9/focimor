import { Newspaper, PawPrint, PiggyBank } from "lucide-react";
import { notFound, redirect } from "next/navigation";

import { JsonLd } from "@/components/JsonLd";
import { absoluteUrl } from "@/lib/seo";
import { SiteShell } from "@/components/app/SiteShell";
import { Pagination } from "@/components/Pagination";
import { AdoptablePetCard } from "@/features/ong/components/AdoptablePetCard";
import { EmptyState } from "@/features/ong/components/EmptyState";
import { OngHeader } from "@/features/ong/components/OngHeader";
import { OngTabs } from "@/features/ong/components/OngTabs";
import { PostCard } from "@/features/ong/components/PostCard";
import { CampaignsList } from "@/features/ong/components/CampaignsList";
import { fetchOng, fetchOngCampaigns, fetchOngPets, fetchOngPosts } from "@/features/ong/server/ongServer";
import type { Ong } from "@/features/ong/types/ongTypes";
import { ongHref, type OngDetailParams } from "@/features/ong/utils/ongParams";
import { ApiError } from "@/lib/api/errors";

interface OngViewProps {
  uuid: string;
  params: OngDetailParams;
}

async function loadOng(uuid: string): Promise<Ong> {
  try {
    return await fetchOng(uuid);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}

async function OngTabContent({ uuid, params, ong }: OngViewProps & { ong: Ong }) {
  if (params.tab === "caixinhas") {
    const page = await fetchOngCampaigns(uuid, params.page);
    if (page.data.length === 0 && params.page > 1) redirect(ongHref(uuid, { ...params, page: Math.max(page.meta.last_page, 1) }));
    if (page.data.length === 0) return <EmptyState icon={PiggyBank} title="Nenhuma caixinha por aqui" text="Esta ONG ainda não abriu nenhuma campanha de arrecadação." />;

    return (
      <>
        <CampaignsList campaigns={page.data} hasPix={Boolean(ong.pix_key)} />
        <Pagination meta={page.meta} buildHref={(target) => ongHref(uuid, { ...params, page: target })} />
      </>
    );
  }

  if (params.tab === "posts") {
    const page = await fetchOngPosts(uuid, params.page);
    if (page.data.length === 0 && params.page > 1) redirect(ongHref(uuid, { ...params, page: Math.max(page.meta.last_page, 1) }));
    if (page.data.length === 0) return <EmptyState icon={Newspaper} title="Sem novidades por aqui" text="Esta ONG ainda não publicou nada." />;

    return (
      <>
        <div className="grid gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
          {page.data.map((post) => (
            <PostCard key={post.uuid} post={post} showOng={false} />
          ))}
        </div>
        <Pagination meta={page.meta} buildHref={(target) => ongHref(uuid, { ...params, page: target })} />
      </>
    );
  }

  const page = await fetchOngPets(uuid, params.page);
  if (page.data.length === 0 && params.page > 1) redirect(ongHref(uuid, { ...params, page: Math.max(page.meta.last_page, 1) }));
  if (page.data.length === 0) return <EmptyState icon={PawPrint} title="Nenhum pet para adoção agora" text="Volte em breve, novos pets chegam sempre." />;

  return (
    <>
      <div className="grid gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
        {page.data.map((pet) => (
          <AdoptablePetCard key={pet.uuid} pet={pet} />
        ))}
      </div>
      <Pagination meta={page.meta} buildHref={(target) => ongHref(uuid, { ...params, page: target })} />
    </>
  );
}

export async function OngView(props: OngViewProps) {
  const ong = await loadOng(props.uuid);

  return (
    <SiteShell>
      <JsonLd
        data={{
          "@context": "https://schema.org",
          "@type": "NGO",
          name: ong.name,
          url: absoluteUrl(`/ongs/${ong.uuid}`),
          description: ong.description ?? undefined,
          logo: ong.logo_url ?? undefined,
          email: ong.email ?? undefined,
          telephone: ong.phone ?? undefined,
          address: ong.city ? { "@type": "PostalAddress", streetAddress: ong.address ?? undefined, addressLocality: ong.city, addressRegion: ong.state ?? undefined, addressCountry: "BR" } : undefined,
        }}
      />
      <OngHeader ong={ong} />
      <OngTabs uuid={ong.uuid} active={props.params.tab} />
      <OngTabContent {...props} ong={ong} />
    </SiteShell>
  );
}
