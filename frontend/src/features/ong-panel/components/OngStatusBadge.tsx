import { Badge } from "@/components/ui/badge";
import { STATUS_LABELS } from "@/features/ong-panel/types/ongPanelTypes";
import { cn } from "@/lib/utils";

const CLASSES: Record<string, string> = {
  verified: "bg-mint-soft text-mint",
  pending: "bg-sun-soft text-sun",
  rejected: "bg-muted text-muted-foreground",
  suspended: "bg-muted text-muted-foreground",
};

export function OngStatusBadge({ status }: { status: string }) {
  return (
    <Badge variant="secondary" className={cn("shrink-0", CLASSES[status] ?? "bg-muted text-muted-foreground")}>
      {STATUS_LABELS[status] ?? status}
    </Badge>
  );
}
