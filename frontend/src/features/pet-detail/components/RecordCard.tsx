import type { LucideIcon } from "lucide-react";
import type { ReactNode } from "react";

import { Card, CardContent } from "@/components/ui/card";
import { cn } from "@/lib/utils";

export type RecordTone = "mint" | "sky" | "sun" | "grape";

const TONES: Record<RecordTone, { stripe: string; icon: string }> = {
  mint: { stripe: "border-l-mint", icon: "bg-mint-soft text-mint" },
  sky: { stripe: "border-l-sky", icon: "bg-sky-soft text-sky" },
  sun: { stripe: "border-l-sun", icon: "bg-sun-soft text-sun" },
  grape: { stripe: "border-l-grape", icon: "bg-grape-soft text-grape" },
};

export interface RecordStat {
  label: string;
  value: ReactNode;
}

interface RecordCardProps {
  icon: LucideIcon;
  tone: RecordTone;
  title: string;
  subtitle?: string | null;
  badge?: ReactNode;
  stats: RecordStat[];
  note?: string | null;
  actions: ReactNode;
}

export function RecordCard({ icon: Icon, tone, title, subtitle, badge, stats, note, actions }: RecordCardProps) {
  const style = TONES[tone];

  return (
    <Card className={cn("rounded-sm border-l-4 transition-shadow hover:shadow-md", style.stripe)}>
      <CardContent className="flex flex-col gap-4">
        <div className="flex items-start gap-3">
          <span className={cn("flex size-11 shrink-0 items-center justify-center rounded-full", style.icon)}>
            <Icon className="size-5" aria-hidden="true" />
          </span>
          <div className="min-w-0 flex-1">
            <p className="truncate text-base font-semibold">{title}</p>
            {subtitle && <p className="truncate text-xs text-muted-foreground">{subtitle}</p>}
            {badge && <div className="mt-1.5 flex flex-wrap gap-1.5">{badge}</div>}
          </div>
          <div className="-mr-2 -mt-1 shrink-0">{actions}</div>
        </div>

        <dl className="grid grid-cols-2 gap-3 rounded-sm bg-muted/40 p-3 sm:grid-cols-3">
          {stats.map((stat) => (
            <div key={stat.label} className="flex min-w-0 flex-col gap-0.5">
              <dt className="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">{stat.label}</dt>
              <dd className="truncate text-sm font-medium">{stat.value}</dd>
            </div>
          ))}
        </dl>

        {note && <p className="line-clamp-2 text-sm text-muted-foreground">{note}</p>}
      </CardContent>
    </Card>
  );
}
