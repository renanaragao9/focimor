import { MapPin } from "lucide-react";
import Link from "next/link";

import { OngStatusBadge } from "@/features/ong-panel/components/OngStatusBadge";
import { ROLE_LABELS, type MyOng } from "@/features/ong-panel/types/ongPanelTypes";
import { panelHref } from "@/features/ong-panel/utils/panelParams";
import { OngLogo } from "@/features/ong/components/OngLogo";
import { locationLabel } from "@/features/ong/utils/ongDisplay";

export function MyOngCard({ ong }: { ong: MyOng }) {
  const location = locationLabel(ong.city, ong.state);

  return (
    <Link href={panelHref(ong.uuid)} className="flex items-center gap-4 rounded-sm border bg-card p-4 transition-colors hover:border-brand focus-visible:outline-2 focus-visible:outline-ring">
      <OngLogo name={ong.name} url={ong.logo_url} className="size-16" />
      <div className="flex min-w-0 flex-1 flex-col gap-1">
        <div className="flex items-center justify-between gap-2">
          <h3 className="truncate text-base font-semibold">{ong.name}</h3>
          <OngStatusBadge status={ong.status} />
        </div>
        <p className="text-xs text-muted-foreground">{ong.my_role ? ROLE_LABELS[ong.my_role] : "Membro"}</p>
        {location && (
          <p className="flex items-center gap-1 text-xs text-muted-foreground">
            <MapPin className="size-3.5" aria-hidden="true" /> {location}
          </p>
        )}
      </div>
    </Link>
  );
}
