import { Newspaper } from "lucide-react";
import { redirect } from "next/navigation";

import { SiteShell } from "@/components/app/SiteShell";
import { Pagination } from "@/components/Pagination";
import { H1, Muted } from "@/components/ui/typography";
import { EmptyState } from "@/features/ong/components/EmptyState";
import { ListSearch } from "@/features/ong/components/ListSearch";
import { PostCard } from "@/features/ong/components/PostCard";
import { fetchPosts } from "@/features/ong/server/ongServer";
import { listHref, type ListParams } from "@/features/ong/utils/ongParams";

export async function PostsView({ params }: { params: ListParams }) {
  const page = await fetchPosts(params);

  if (page.data.length === 0 && params.page > 1) redirect(listHref("/posts", { ...params, page: Math.max(page.meta.last_page, 1) }));

  return (
    <SiteShell>
      <div className="flex flex-col gap-1">
        <H1 className="text-3xl">Novidades</H1>
        <Muted>O que as ONGs e o Focimor estão publicando.</Muted>
      </div>

      <ListSearch basePath="/posts" value={params.search} placeholder="Buscar pelo título" />

      {page.data.length === 0 ? (
        <EmptyState
          icon={Newspaper}
          title="Nenhuma publicação encontrada"
          text={params.search ? "Tente outro título ou limpe a busca." : "Ainda não há publicações."}
          action={params.search ? { href: "/posts", label: "Limpar busca" } : undefined}
        />
      ) : (
        <>
          <div className="grid gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
            {page.data.map((post) => (
              <PostCard key={post.uuid} post={post} showOng />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => listHref("/posts", { ...params, page: target })} />
        </>
      )}
    </SiteShell>
  );
}
