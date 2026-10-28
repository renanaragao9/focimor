"use client";

import { FileText, Plus } from "lucide-react";
import Image from "next/image";
import Link from "next/link";
import { useRouter } from "next/navigation";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Pagination } from "@/components/Pagination";
import { RowActions } from "@/components/RowActions";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { PostFormDialog } from "@/features/ong-panel/components/PostFormDialog";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { publishState } from "@/features/ong-panel/utils/panelDisplay";
import { panelHref } from "@/features/ong-panel/utils/panelParams";
import { EmptyState } from "@/features/ong/components/EmptyState";
import type { OngPost } from "@/features/ong/types/ongTypes";
import { postDate } from "@/features/ong/utils/ongDisplay";
import { useRecordDialogs } from "@/hooks/use-record-dialogs";
import type { ApiPage } from "@/lib/api/types";

const STATE_LABELS = { published: "Publicado", scheduled: "Agendado", draft: "Rascunho" } as const;

interface PostsPanelProps {
  ongUuid: string;
  page: ApiPage<OngPost>;
  canPublish: boolean;
}

export function PostsPanel({ ongUuid, page, canPublish }: PostsPanelProps) {
  const router = useRouter();
  const dialogs = useRecordDialogs<OngPost>();

  return (
    <>
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="text-xl font-semibold">Posts</h2>
          <p className="text-sm text-muted-foreground">{canPublish ? "Novidades que aparecem na página da ONG e no feed." : "Disponível depois que a ONG for verificada."}</p>
        </div>
        <Button className="cursor-pointer" disabled={!canPublish} onClick={dialogs.openCreate}>
          <Plus /> Novo post
        </Button>
      </div>

      {page.data.length === 0 ? (
        <EmptyState icon={FileText} title="Nenhum post ainda" text="Conte as novidades da ONG: eventos, resgates, adoções e pedidos de ajuda." />
      ) : (
        <>
          <div className="flex flex-col gap-2">
            {page.data.map((post) => {
              const state = publishState(post);

              return (
                <article key={post.uuid} className="flex items-center gap-3 rounded-sm border bg-card p-3">
                  <div className="relative size-16 shrink-0 overflow-hidden rounded-sm bg-sky-soft">
                    {post.cover_url && <Image src={post.cover_url} alt="" fill unoptimized sizes="64px" className="object-cover" />}
                  </div>
                  <div className="flex min-w-0 flex-1 flex-col gap-1">
                    <h3 className="truncate text-base font-semibold">
                      <Link href={`/minhas-ongs/${ongUuid}/posts/${post.uuid}`} className="hover:underline">
                        {post.title}
                      </Link>
                    </h3>
                    <div className="flex items-center gap-2 text-xs text-muted-foreground">
                      <Badge variant={state === "published" ? "secondary" : "outline"}>{STATE_LABELS[state]}</Badge>
                      {postDate(post.published_at) && <time>{postDate(post.published_at)}</time>}
                    </div>
                  </div>
                  <RowActions label={post.title} onEdit={() => dialogs.openEdit(post, post.uuid)} onDelete={() => dialogs.setItemToDelete(post)} />
                </article>
              );
            })}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => panelHref(ongUuid, { tab: "posts", page: target })} />
        </>
      )}

      <PostFormDialog key={dialogs.formKey} ongUuid={ongUuid} post={dialogs.formItem} open={dialogs.formOpen} onOpenChange={dialogs.setFormOpen} onSaved={() => router.refresh()} />

      <DeleteDialog
        name={dialogs.itemToDelete?.title ?? null}
        title="Excluir post"
        warning="O post deixa de aparecer na página da ONG e no feed."
        successMessage="Post excluído."
        errorMessage="Não foi possível excluir o post."
        onConfirm={async () => {
          if (dialogs.itemToDelete) await ongPanelService.removePost(ongUuid, dialogs.itemToDelete.uuid);
        }}
        onDeleted={() => router.refresh()}
        onOpenChange={(open) => !open && dialogs.setItemToDelete(null)}
      />
    </>
  );
}
