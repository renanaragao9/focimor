import { Newspaper } from "lucide-react";
import Image from "next/image";
import Link from "next/link";

import { OngLogo } from "@/features/ong/components/OngLogo";
import type { OngPost } from "@/features/ong/types/ongTypes";
import { excerpt, postDate } from "@/features/ong/utils/ongDisplay";
import { postHref } from "@/features/ong/utils/ongParams";

interface PostCardProps {
  post: OngPost;
  showOng: boolean;
}

export function PostCard({ post, showOng }: PostCardProps) {
  const date = postDate(post.published_at);

  return (
    <Link href={postHref(post.uuid)} className="flex overflow-hidden rounded-sm border bg-card transition-colors sm:flex-col hover:border-brand focus-visible:outline-2 focus-visible:outline-ring">
      <div className="relative w-28 shrink-0 bg-gradient-to-br from-brand/30 to-sky/30 sm:aspect-video sm:w-auto">
        {post.cover_url ? (
          <Image src={post.cover_url} alt="" fill unoptimized sizes="(min-width: 1024px) 384px, (min-width: 640px) 50vw, 112px" className="object-cover" />
        ) : (
          <span className="flex size-full items-center justify-center">
            <Newspaper className="size-8 sm:size-10 text-foreground/40" aria-hidden="true" />
          </span>
        )}
      </div>
      <div className="flex min-w-0 flex-1 flex-col gap-1.5 p-3 sm:gap-2 sm:p-4">
        {showOng && (
          <span className="flex items-center gap-2 text-xs text-muted-foreground">
            <OngLogo name={post.ong?.name ?? "Focimor"} url={post.ong?.logo_url ?? null} className="size-6 border" />
            <span className="truncate">{post.ong?.name ?? "Focimor"}</span>
          </span>
        )}
        <h3 className="line-clamp-2 text-base font-semibold">{post.title}</h3>
        <p className="line-clamp-2 text-sm sm:line-clamp-3 text-muted-foreground">{excerpt(post.body)}</p>
        {date && <time className="mt-auto text-xs text-muted-foreground">{date}</time>}
      </div>
    </Link>
  );
}
