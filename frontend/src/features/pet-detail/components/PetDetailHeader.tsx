import { Bone, Heart, Mars, PawPrint, Venus } from "lucide-react";

import { Badge } from "@/components/ui/badge";
import { H1 } from "@/components/ui/typography";
import { PetImageDialog } from "@/features/pet-detail/components/PetImageDialog";
import type { Pet } from "@/features/pet/types/petTypes";
import { ageLabel, genderLabel, petSubtitle, primaryPhoto, genderTheme } from "@/features/pet/utils/petDisplay";
import { cn } from "@/lib/utils";

interface PetDetailHeaderProps {
  pet: Pet;
}

export function PetDetailHeader({ pet }: PetDetailHeaderProps) {
  const photo = primaryPhoto(pet.photos);
  const age = ageLabel(pet.birth_date, new Date());
  const GenderIcon = pet.gender === "male" ? Mars : Venus;

  return (
    <div className="flex flex-col gap-4">
      <div className="relative overflow-hidden rounded-sm bg-linear-to-br from-brand-soft via-background to-sky-soft p-5 ring-1 ring-foreground/10 sm:p-6">
        <PawPrint className="pointer-events-none absolute -right-4 -top-4 size-32 rotate-12 text-brand/10" aria-hidden="true" />
        <Bone className="pointer-events-none absolute -bottom-3 right-24 size-20 -rotate-12 text-sun/20" aria-hidden="true" />
        <Heart className="pointer-events-none absolute bottom-4 right-6 size-10 text-brand/15" aria-hidden="true" />

        <div className="relative flex items-center gap-4 sm:gap-5">
          {photo ? (
            <PetImageDialog name={pet.name} src={photo.url} />
          ) : (
            <div className="relative size-24 shrink-0 overflow-hidden rounded-full border-2 border-brand/40 bg-brand-soft shadow-lg ring-2 ring-brand/20 sm:size-28">
              <span className="flex size-full items-center justify-center bg-linear-to-br from-brand-soft via-white/70 to-sky-soft dark:via-card">
                <PawPrint className="size-12 text-brand" strokeWidth={1.75} aria-hidden="true" />
              </span>
            </div>
          )}
          <div className="flex min-w-0 flex-col gap-2">
            <H1 className="truncate bg-linear-to-r from-brand to-sky bg-clip-text text-3xl text-transparent sm:text-4xl">{pet.name}</H1>
            <p className="truncate font-medium text-foreground/70">{petSubtitle(pet)}</p>
            <div className="flex flex-wrap gap-1.5">
              <Badge className={cn("gap-1 border-0", genderTheme(pet.gender).solid)}>
                <GenderIcon className="size-3" aria-hidden="true" /> {genderLabel(pet.gender)}
              </Badge>
              {age && <Badge className="border-0 bg-sun text-white dark:text-background">{age}</Badge>}
              <Badge className="border-0 bg-grape text-white dark:text-background">{pet.goal.name}</Badge>
              {!pet.is_active && <Badge variant="secondary">Inativo</Badge>}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
