import { Mars, PawPrint, Venus } from "lucide-react";
import Image from "next/image";
import Link from "next/link";

import { adoptablePetHref } from "@/features/adoption/utils/adoptablePetParams";
import { Badge } from "@/components/ui/badge";
import type { AdoptablePet } from "@/features/ong/types/ongTypes";
import { PetDetailsLine } from "@/features/pet/components/PetDetailsLine";
import { cn } from "@/lib/utils";
import { genderLabel, genderTheme, petSubtitle, primaryPhoto } from "@/features/pet/utils/petDisplay";

export function AdoptablePetCard({ pet }: { pet: AdoptablePet }) {
  const photo = primaryPhoto(pet.photos);
  const theme = genderTheme(pet.gender);
  const GenderIcon = pet.gender === "male" ? Mars : Venus;

  return (
    <Link
      href={adoptablePetHref(pet.uuid)}
      className={cn("flex overflow-hidden rounded-sm sm:flex-col border bg-card transition-colors focus-visible:outline-2 focus-visible:outline-ring", theme.hoverBorder)}
    >
      <div className="relative aspect-square w-28 shrink-0 bg-brand-soft sm:w-auto">
        {photo ? (
          <Image src={photo.url} alt={`Foto de ${pet.name}`} fill unoptimized sizes="(min-width: 1024px) 288px, (min-width: 640px) 50vw, 112px" className="object-cover" />
        ) : (
          <span className="flex size-full items-center justify-center">
            <PawPrint className="size-12 text-brand/70" aria-hidden="true" />
          </span>
        )}
      </div>
      <div className="flex min-w-0 flex-1 flex-col gap-2 p-3 sm:p-4">
        <div className="flex items-center justify-between gap-2">
          <h3 className="truncate text-base font-semibold">{pet.name}</h3>
          <GenderIcon className={cn("size-4 shrink-0", theme.text)} aria-label={genderLabel(pet.gender)} />
        </div>
        <p className="truncate text-xs text-muted-foreground">
          <PetDetailsLine parts={[petSubtitle(pet)]} birthDate={pet.birth_date} />
        </p>
        <div className="flex flex-wrap gap-1">
          {pet.size && <Badge variant="outline">{pet.size.name}</Badge>}
          {pet.is_vaccinated && <Badge variant="outline">Vacinado</Badge>}
          {pet.is_neutered && <Badge variant="outline">Castrado</Badge>}
        </div>
        {pet.bio && <p className="line-clamp-2 text-sm text-muted-foreground">{pet.bio}</p>}
      </div>
    </Link>
  );
}
