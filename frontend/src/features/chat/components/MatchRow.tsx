import Link from "next/link";

import { Badge } from "@/components/ui/badge";
import { LocalTime } from "@/features/chat/components/LocalTime";
import { PetAvatar } from "@/features/chat/components/PetAvatar";
import type { MatchItem } from "@/features/chat/types/chatTypes";
import { chatHref } from "@/features/chat/utils/matchesParams";
import { previewOf } from "@/features/chat/utils/messages";
import { cn } from "@/lib/utils";

interface MatchRowProps {
  match: MatchItem;
  showMyPet: boolean;
}

export function MatchRow({ match, showMyPet }: MatchRowProps) {
  const unread = match.unread_count ?? 0;
  const preview = previewOf(match.last_message);

  return (
    <Link href={chatHref(match.uuid)} className="flex items-center gap-4 rounded-sm border bg-card p-3 transition-colors hover:border-brand focus-visible:outline-2 focus-visible:outline-ring">
      <PetAvatar name={match.other_pet.name} photos={match.other_pet.photos} className="size-16" />

      <div className="flex min-w-0 flex-1 flex-col gap-0.5">
        <div className="flex items-baseline justify-between gap-2">
          <h3 className="truncate text-base font-semibold">{match.other_pet.name}</h3>
          <LocalTime value={match.last_message?.created_at ?? match.matched_at} kind="stamp" className={cn("shrink-0 text-xs", unread > 0 ? "font-semibold text-brand" : "text-muted-foreground")} />
        </div>
        <p className="truncate text-xs text-muted-foreground">
          {match.other_pet.species.name}
          {showMyPet && ` · com ${match.my_pet.name}`}
        </p>
        <div className="flex items-center justify-between gap-2">
          <p className={cn("truncate text-sm", unread > 0 ? "font-medium text-foreground" : "text-muted-foreground")}>{preview ?? "Novo match. Diga olá!"}</p>
          {unread > 0 && (
            <Badge className="shrink-0" aria-label={`${unread} ${unread === 1 ? "mensagem não lida" : "mensagens não lidas"}`}>
              {unread > 99 ? "99+" : unread}
            </Badge>
          )}
        </div>
      </div>
    </Link>
  );
}
