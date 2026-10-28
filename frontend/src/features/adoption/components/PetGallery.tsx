"use client";

import { PawPrint } from "lucide-react";
import Image from "next/image";
import { useState } from "react";

import type { PetPhoto } from "@/features/pet/types/petTypes";
import { cn } from "@/lib/utils";

export function PetGallery({ name, photos, accent }: { name: string; photos: PetPhoto[]; accent: string }) {
  const [index, setIndex] = useState(0);
  const current = photos[index] ?? photos[0];

  return (
    <div className="flex flex-col gap-3">
      <div className={cn("relative aspect-square overflow-hidden rounded-sm border-2 bg-muted sm:aspect-4/3", accent)}>
        {current ? (
          <Image src={current.url} alt={`Foto de ${name}`} fill unoptimized priority sizes="(min-width: 1024px) 560px, 100vw" className="object-cover" />
        ) : (
          <span className="flex size-full items-center justify-center">
            <PawPrint className="size-20 text-brand/70" aria-hidden="true" />
          </span>
        )}
      </div>
      {photos.length > 1 && (
        <div className="flex gap-2 overflow-x-auto pb-1">
          {photos.map((photo, position) => (
            <button
              key={photo.id}
              type="button"
              aria-label={`Ver foto ${position + 1} de ${name}`}
              aria-current={position === index}
              onClick={() => setIndex(position)}
              className={cn(
                "relative size-16 shrink-0 cursor-pointer overflow-hidden rounded-sm border-2 bg-brand-soft",
                position === index ? accent : "border-transparent opacity-70 hover:opacity-100",
              )}
            >
              <Image src={photo.url} alt="" fill unoptimized sizes="64px" className="object-cover" />
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
