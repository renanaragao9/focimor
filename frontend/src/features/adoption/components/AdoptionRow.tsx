import Link from "next/link";

import { Badge } from "@/components/ui/badge";
import { StatusBadge } from "@/features/adoption/components/StatusBadge";
import type { Adoption } from "@/features/adoption/types/adoptionTypes";
import { counterpartLabel } from "@/features/adoption/utils/adoptionDisplay";
import { adoptionHref } from "@/features/adoption/utils/adoptionParams";
import { LocalTime } from "@/features/chat/components/LocalTime";
import { PetAvatar } from "@/features/chat/components/PetAvatar";
import { previewOf } from "@/features/chat/utils/messages";
import { cn } from "@/lib/utils";

export function AdoptionRow({ adoption }: { adoption: Adoption }) {
  const unread = adoption.unread_count ?? 0;
  const preview = previewOf(adoption.last_message ?? null);

  return (
    <Link href={adoptionHref(adoption.uuid)} className="flex items-center gap-4 rounded-sm border bg-card p-3 transition-colors hover:border-brand focus-visible:outline-2 focus-visible:outline-ring">
      <PetAvatar name={adoption.pet.name} photos={adoption.pet.photos} className="size-16" />

      <div className="flex min-w-0 flex-1 flex-col gap-0.5">
        <div className="flex items-center justify-between gap-2">
          <h3 className="truncate text-base font-semibold">{adoption.pet.name}</h3>
          <StatusBadge status={adoption.status} />
        </div>
        <p className="truncate text-xs text-muted-foreground">{counterpartLabel(adoption)}</p>
        <div className="flex items-center justify-between gap-2">
          <p className={cn("truncate text-sm", unread > 0 ? "font-medium text-foreground" : "text-muted-foreground")}>{preview ?? "Sem mensagens ainda."}</p>
          <span className="flex shrink-0 items-center gap-2">
            <LocalTime value={adoption.last_message?.created_at ?? adoption.created_at} kind="stamp" className="text-xs text-muted-foreground" />
            {unread > 0 && <Badge aria-label={`${unread} não lidas`}>{unread > 99 ? "99+" : unread}</Badge>}
          </span>
        </div>
      </div>
    </Link>
  );
}
