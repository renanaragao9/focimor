import type { LucideIcon } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";

interface EmptyStateProps {
  icon: LucideIcon;
  title: string;
  text: string;
  action?: { href: string; label: string };
}

export function EmptyState({ icon: Icon, title, text, action }: EmptyStateProps) {
  return (
    <div className="flex flex-col items-center gap-4 rounded-sm border border-dashed px-6 py-16 text-center">
      <span className="flex size-16 items-center justify-center rounded-full bg-brand-soft">
        <Icon className="size-8 text-brand" aria-hidden="true" />
      </span>
      <div className="flex flex-col gap-1">
        <h2 className="text-xl font-semibold">{title}</h2>
        <p className="max-w-md text-muted-foreground">{text}</p>
      </div>
      {action && (
        <Button asChild variant="outline">
          <Link href={action.href}>{action.label}</Link>
        </Button>
      )}
    </div>
  );
}
