import { Mars, MapPin, PawPrint, Venus } from "lucide-react";
import Image from "next/image";

import { Badge } from "@/components/ui/badge";
import type { FeedPet } from "@/features/match/types/matchTypes";
import { cardTags, distanceLabel } from "@/features/match/utils/matchDisplay";
import type { OverlayOpacities } from "@/features/match/utils/swipeGesture";
import { ageLabel, genderLabel, petSubtitle } from "@/features/pet/utils/petDisplay";
import { cn } from "@/lib/utils";

interface PetCardFaceProps {
  pet: FeedPet;
  photoIndex?: number;
  overlays?: OverlayOpacities;
  priority?: boolean;
}

const STAMP = "absolute rounded-md border-4 px-3 py-1 text-2xl font-extrabold uppercase tracking-wide";

export function PetCardFace({ pet, photoIndex = 0, overlays, priority = false }: PetCardFaceProps) {
  const photo = pet.photos[photoIndex] ?? pet.photos[0];
  const age = ageLabel(pet.birth_date, new Date());
  const distance = distanceLabel(pet.distance_km);
  const GenderIcon = pet.gender === "male" ? Mars : Venus;
  const gender = genderLabel(pet.gender);

  return (
    <div className="relative size-full select-none overflow-hidden rounded-2xl bg-brand-soft shadow-xl ring-1 ring-black/5">
      {photo ? (
        <Image
          src={photo.url}
          alt={`Foto de ${pet.name}`}
          fill
          unoptimized
          priority={priority}
          draggable={false}
          sizes="(min-width: 640px) 28rem, 100vw"
          className="pointer-events-none object-cover"
        />
      ) : (
        <div className="flex size-full items-center justify-center">
          <PawPrint className="size-24 text-brand/70" aria-hidden="true" />
        </div>
      )}

      {pet.photos.length > 1 && (
        <div className="absolute inset-x-3 top-3 flex gap-1" aria-hidden="true">
          {pet.photos.map((item, index) => (
            <span key={item.id} className={cn("h-1 flex-1 rounded-full", index === photoIndex ? "bg-white" : "bg-white/40")} />
          ))}
        </div>
      )}

      <div className="pointer-events-none absolute inset-x-0 bottom-0 flex flex-col gap-2 bg-linear-to-t from-black/95 via-black/75 to-transparent p-5 pt-28 text-white">
        <div className="flex items-end justify-between gap-3">
          <div className="min-w-0">
            <h2 className="truncate text-3xl font-bold leading-tight drop-shadow-[0_2px_3px_rgb(0_0_0/0.9)]">
              {pet.name}
              {age && <span className="ml-2 text-xl font-normal text-white">{age}</span>}
            </h2>
            <p className="text-sm font-medium text-white drop-shadow-[0_1px_2px_rgb(0_0_0/0.9)]">{petSubtitle(pet)}</p>
          </div>
          <span className="flex shrink-0 items-center gap-1 text-sm font-medium text-white drop-shadow-[0_1px_2px_rgb(0_0_0/0.9)]">
            <GenderIcon className="size-5" aria-hidden="true" />
            {gender}
          </span>
        </div>

        {pet.ong && <p className="truncate text-sm font-semibold text-white drop-shadow-[0_1px_2px_rgb(0_0_0/0.9)]">{pet.ong.name}</p>}

        {distance && (
          <p className="flex items-center gap-1 text-sm font-medium text-white drop-shadow-[0_1px_2px_rgb(0_0_0/0.9)]">
            <MapPin className="size-4" aria-hidden="true" /> {distance}
          </p>
        )}

        <div className="flex flex-wrap gap-1.5">
          {cardTags(pet).map((tag) => (
            <Badge key={tag} className="border-white/45 bg-black/55 font-medium text-white shadow-sm backdrop-blur">
              {tag}
            </Badge>
          ))}
        </div>

        {pet.bio && <p className="line-clamp-2 text-sm font-medium text-white drop-shadow-[0_1px_2px_rgb(0_0_0/0.9)]">{pet.bio}</p>}
      </div>

      {overlays && (
        <>
          <span className={cn(STAMP, "left-5 top-10 -rotate-12 border-success text-success")} style={{ opacity: overlays.like }} aria-hidden="true">
            Curtir
          </span>
          <span className={cn(STAMP, "right-5 top-10 rotate-12 border-destructive text-destructive")} style={{ opacity: overlays.dislike }} aria-hidden="true">
            Passar
          </span>
          <span className={cn(STAMP, "left-1/2 top-1/3 -translate-x-1/2 border-info text-info")} style={{ opacity: overlays.superlike }} aria-hidden="true">
            Super
          </span>
        </>
      )}
    </div>
  );
}
