import { HeartHandshake } from "lucide-react";
import Image from "next/image";

import { cn } from "@/lib/utils";

interface OngLogoProps {
  name: string;
  url: string | null;
  className?: string;
}

export function OngLogo({ name, url, className }: OngLogoProps) {
  return (
    <div className={cn("relative size-14 shrink-0 overflow-hidden rounded-full border-2 border-brand-soft bg-brand-soft", className)}>
      {url ? (
        <Image src={url} alt={`Logo de ${name}`} fill unoptimized sizes="96px" className="object-cover" />
      ) : (
        <span className="flex size-full items-center justify-center">
          <HeartHandshake className="size-1/2 text-brand" aria-hidden="true" />
        </span>
      )}
    </div>
  );
}
