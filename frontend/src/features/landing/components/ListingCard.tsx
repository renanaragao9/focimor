import Image from "next/image";
import Link from "next/link";
import type { ReactNode } from "react";

import { cn } from "@/lib/utils";

interface ListingCardProps {
  title: string;
  href: string;
  image: string | null;
  imageAlt: string;
  placeholder: ReactNode;
  fit?: "cover" | "contain";
  tag?: string;
  lines: ReactNode[];
  footer?: ReactNode;
}

export function ListingCard({ title, href, image, imageAlt, placeholder, fit = "cover", tag, lines, footer }: ListingCardProps) {
  return (
    <article className="flex w-64 shrink-0 flex-col sm:w-72">
      <div className="relative aspect-video overflow-hidden rounded-sm border border-border bg-muted/40">
        <Link href={href} className="absolute inset-0 block" aria-label={title}>
          {image ? (
            <Image src={image} alt={imageAlt} fill unoptimized sizes="288px" className={cn(fit === "contain" ? "object-contain p-4" : "object-cover")} />
          ) : (
            <span className="flex size-full items-center justify-center">{placeholder}</span>
          )}
        </Link>
        {tag && <span className="absolute left-3 top-3 rounded-sm bg-background px-3 py-1 text-xs font-semibold text-foreground shadow-sm">{tag}</span>}
      </div>
      <div className="flex flex-1 flex-col pt-3 text-sm/5">
        <h3 className="truncate font-semibold text-foreground">
          <Link href={href} className="hover:underline">
            {title}
          </Link>
        </h3>
        <div className="mt-1.5 flex flex-col gap-1.5 text-muted-foreground">
          {lines.map((line, index) => (
            <p key={index} className="min-w-0 wrap-break-word">
              {line}
            </p>
          ))}
        </div>
        {footer && <div className="mt-auto pt-2 text-foreground">{footer}</div>}
      </div>
    </article>
  );
}
