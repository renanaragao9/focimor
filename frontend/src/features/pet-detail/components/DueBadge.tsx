"use client";

import { Badge } from "@/components/ui/badge";
import { dueLabel, dueStatus } from "@/features/pet-detail/utils/petDetailDisplay";
import { cn } from "@/lib/utils";

interface DueBadgeProps {
  dueDate: string | null;
}

export function DueBadge({ dueDate }: DueBadgeProps) {
  const due = dueStatus(dueDate, new Date());

  if (!due) return <span className="text-muted-foreground">—</span>;

  return (
    <div className="flex flex-col items-start gap-1">
      <span>{dueDate}</span>
      <Badge variant={due.status === "overdue" ? "destructive" : "secondary"} className={cn(due.status === "soon" && "bg-warning/15 text-warning")}>
        {dueLabel(due)}
      </Badge>
    </div>
  );
}
