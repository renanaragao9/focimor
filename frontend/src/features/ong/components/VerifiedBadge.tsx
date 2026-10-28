import { BadgeCheck } from "lucide-react";

import { Badge } from "@/components/ui/badge";

export function VerifiedBadge() {
  return (
    <Badge variant="secondary" className="gap-1 bg-mint-soft text-mint">
      <BadgeCheck className="size-3.5" aria-hidden="true" /> Verificada
    </Badge>
  );
}
