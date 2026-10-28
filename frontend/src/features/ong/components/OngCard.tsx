import { MapPin, PawPrint } from "lucide-react";
import Link from "next/link";

import { OngLogo } from "@/features/ong/components/OngLogo";
import { VerifiedBadge } from "@/features/ong/components/VerifiedBadge";
import type { Ong } from "@/features/ong/types/ongTypes";
import { excerpt, locationLabel } from "@/features/ong/utils/ongDisplay";
import { ongHref } from "@/features/ong/utils/ongParams";

export function OngCard({ ong }: { ong: Ong }) {
  const location = locationLabel(ong.city, ong.state);
  const count = ong.adoptable_pets_count ?? 0;

  return (
    <Link href={ongHref(ong.uuid)} className="flex flex-col gap-3 rounded-sm border bg-card p-4 transition-colors hover:border-brand focus-visible:outline-2 focus-visible:outline-ring">
      <div className="flex items-center gap-3">
        <OngLogo name={ong.name} url={ong.logo_url} className="size-16" />
        <div className="flex min-w-0 flex-col gap-1">
          <h3 className="truncate text-base font-semibold">{ong.name}</h3>
          {ong.is_verified && <VerifiedBadge />}
        </div>
      </div>
      {ong.description && <p className="line-clamp-3 text-sm text-muted-foreground">{excerpt(ong.description, 180)}</p>}
      <div className="mt-auto flex items-center justify-between gap-2 text-xs text-muted-foreground">
        <span className="flex min-w-0 items-center gap-1">
          {location && (
            <>
              <MapPin className="size-3.5 shrink-0" aria-hidden="true" />
              <span className="truncate">{location}</span>
            </>
          )}
        </span>
        <span className="flex shrink-0 items-center gap-1 font-semibold text-brand-deep dark:text-brand">
          <PawPrint className="size-3.5" aria-hidden="true" />
          {count} {count === 1 ? "pet" : "pets"} para adoção
        </span>
      </div>
    </Link>
  );
}
