import { MapPin } from "lucide-react";

import { StatusBadge } from "@/features/adoption/components/StatusBadge";
import type { Adoption } from "@/features/adoption/types/adoptionTypes";
import { counterpartLabel } from "@/features/adoption/utils/adoptionDisplay";
import { PetAvatar } from "@/features/chat/components/PetAvatar";
import { petSubtitle } from "@/features/pet/utils/petDisplay";
import { locationLabel } from "@/features/ong/utils/ongDisplay";

export function AdoptionSummary({ adoption }: { adoption: Adoption }) {
  const { pet } = adoption;
  const location = pet.ong ? locationLabel(pet.ong.city, pet.ong.state) : null;

  return (
    <section className="flex items-center gap-4 rounded-sm border bg-card p-4">
      <PetAvatar name={pet.name} photos={pet.photos} className="size-20" />
      <div className="flex min-w-0 flex-1 flex-col gap-1">
        <div className="flex flex-wrap items-center gap-2">
          <h1 className="truncate text-xl font-bold">{pet.name}</h1>
          <StatusBadge status={adoption.status} />
        </div>
        <p className="truncate text-sm text-muted-foreground">{petSubtitle(pet)}</p>
        <p className="truncate text-sm">{counterpartLabel(adoption)}</p>
        {location && (
          <p className="flex items-center gap-1 text-xs text-muted-foreground">
            <MapPin className="size-3.5" aria-hidden="true" /> {location}
          </p>
        )}
      </div>
    </section>
  );
}
