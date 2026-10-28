import { CalendarClock, ExternalLink } from "lucide-react";
import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { PostDetailActions } from "@/features/ong-panel/components/PostDetailActions";
import { fetchOngPanelPost } from "@/features/ong-panel/server/ongPanelServer";
import { publishState } from "@/features/ong-panel/utils/panelDisplay";
import type { OngPost } from "@/features/ong/types/ongTypes";
import { postDate } from "@/features/ong/utils/ongDisplay";
import { postHref } from "@/features/ong/utils/ongParams";
import { ApiError } from "@/lib/api/errors";
import type { AuthUser } from "@/lib/auth/authTypes";

const STATE_LABELS = { published: "Publicado", scheduled: "Agendado", draft: "Rascunho" } as const;

async function loadPost(ongUuid: string, postUuid: string): Promise<OngPost> {
  try {
    return await fetchOngPanelPost(ongUuid, postUuid);
  } catch (error) {
    if (error instanceof ApiError && (error.status === 404 || error.status === 403)) notFound();
    throw error;
  }
}

export async function PostDetailView({ user, ongUuid, postUuid }: { user: AuthUser; ongUuid: string; postUuid: string }) {
  const post = await loadPost(ongUuid, postUuid);
  const state = publishState(post);
  const date = postDate(post.published_at);

  return (
    <AppShell user={user}>
      <article className="mx-auto flex w-full max-w-3xl flex-col gap-5">
        {post.cover_url && (
          <div className="relative aspect-video overflow-hidden rounded-sm border">
            <Image src={post.cover_url} alt="" fill unoptimized priority sizes="768px" className="object-cover" />
          </div>
        )}

        <div className="flex flex-wrap items-center gap-2">
          <Badge variant={state === "published" ? "secondary" : "outline"}>{STATE_LABELS[state]}</Badge>
          {date && (
            <span className="flex items-center gap-1 text-sm text-muted-foreground">
              <CalendarClock className="size-4" aria-hidden="true" /> {state === "scheduled" ? "Será publicado em" : "Publicado em"} {date}
            </span>
          )}
        </div>

        <h1 className="text-3xl font-extrabold tracking-tight text-balance">{post.title}</h1>

        <div className="flex flex-wrap gap-2">
          <PostDetailActions ongUuid={ongUuid} post={post} />
          {state === "published" && (
            <Button variant="outline" asChild>
              <Link href={postHref(post.uuid)}>
                <ExternalLink /> Ver página pública
              </Link>
            </Button>
          )}
        </div>

        <p className="whitespace-pre-line leading-7">{post.body}</p>
      </article>
    </AppShell>
  );
}
