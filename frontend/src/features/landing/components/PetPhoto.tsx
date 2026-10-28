import Image from "next/image";

import type { LandingImage } from "@/features/landing/utils/landingImages";
import { cn } from "@/lib/utils";

interface PetPhotoProps {
  image: LandingImage;
  sizes: string;
  className?: string;
  priority?: boolean;
}

export function PetPhoto({ image, sizes, className, priority = false }: PetPhotoProps) {
  return (
    <div className={cn("relative overflow-hidden bg-brand-soft", className)}>
      <Image src={image.src} alt={image.alt} fill unoptimized priority={priority} sizes={sizes} className="object-cover" />
    </div>
  );
}
