import { Badge } from "@/components/ui/badge";
import type { AdoptionStatus } from "@/features/adoption/types/adoptionTypes";
import { STATUS_CLASSES, STATUS_LABELS } from "@/features/adoption/utils/adoptionDisplay";
import { cn } from "@/lib/utils";

export function StatusBadge({ status }: { status: AdoptionStatus }) {
  return (
    <Badge variant="secondary" className={cn("shrink-0", STATUS_CLASSES[status])}>
      {STATUS_LABELS[status]}
    </Badge>
  );
}
