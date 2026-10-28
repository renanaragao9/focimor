import { PawPrint } from "lucide-react";
import Image from "next/image";

import type { PetPhoto } from "@/features/pet/types/petTypes";
import { primaryPhoto } from "@/features/pet/utils/petDisplay";
import { cn } from "@/lib/utils";

interface PetAvatarProps {
  name: string;
  photos: PetPhoto[];
  className?: string;
}

export function PetAvatar({ name, photos, className }: PetAvatarProps) {
  const photo = primaryPhoto(photos);

  return (
    <div className={cn("relative size-14 shrink-0 overflow-hidden rounded-full border-2 border-brand-soft bg-brand-soft", className)}>
      {photo ? (
        <Image src={photo.url} alt={`Foto de ${name}`} fill unoptimized sizes="64px" className="object-cover" />
      ) : (
        <span className="flex size-full items-center justify-center">
          <PawPrint className="size-1/2 text-brand/70" aria-hidden="true" />
        </span>
      )}
    </div>
  );
}
