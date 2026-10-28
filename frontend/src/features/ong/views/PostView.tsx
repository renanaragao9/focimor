import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";

import { JsonLd } from "@/components/JsonLd";
import { clip, absoluteUrl } from "@/lib/seo";
import { SiteShell } from "@/components/app/SiteShell";
import { OngLogo } from "@/features/ong/components/OngLogo";
import { fetchPost } from "@/features/ong/server/ongServer";
import type { OngPost } from "@/features/ong/types/ongTypes";
import { postDate } from "@/features/ong/utils/ongDisplay";
import { ongHref } from "@/features/ong/utils/ongParams";
import { ApiError } from "@/lib/api/errors";

async function loadPost(uuid: string): Promise<OngPost> {
  try {
    return await fetchPost(uuid);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}

export async function PostView({ uuid }: { uuid: string }) {
  const post = await loadPost(uuid);
  const date = postDate(post.published_at);

  return (
    <SiteShell>
      <JsonLd
        data={{
          "@context": "https://schema.org",
          "@type": "Article",
          headline: post.title,
          description: clip(post.body),
          image: post.cover_url ?? undefined,
          datePublished: post.published_at ? post.published_at.slice(0, 10).split("/").reverse().join("-") : undefined,
          author: { "@type": "Organization", name: post.ong?.name ?? "Focimor" },
          mainEntityOfPage: absoluteUrl(`/posts/${post.uuid}`),
          inLanguage: "pt-BR",
        }}
      />
      <article className="mx-auto flex w-full max-w-3xl flex-col gap-5">
        {post.cover_url && (
          <div className="relative aspect-video overflow-hidden rounded-sm border">
            <Image src={post.cover_url} alt="" fill unoptimized priority sizes="768px" className="object-cover" />
          </div>
        )}

        <h1 className="text-3xl font-extrabold tracking-tight text-balance">{post.title}</h1>

        <div className="flex items-center gap-3 text-sm text-muted-foreground">
          {post.ong ? (
            <Link href={ongHref(post.ong.uuid)} className="flex items-center gap-2 font-medium text-foreground hover:underline">
              <OngLogo name={post.ong.name} url={post.ong.logo_url} className="size-8 border" />
              {post.ong.name}
            </Link>
          ) : (
            <span className="font-medium text-foreground">Focimor</span>
          )}
          {date && <time>· {date}</time>}
        </div>

        <p className="whitespace-pre-line leading-7">{post.body}</p>
      </article>
    </SiteShell>
  );
}
