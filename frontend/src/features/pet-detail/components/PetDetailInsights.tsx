"use client";

import type { LucideIcon } from "lucide-react";
import { useEffect, useRef, useState } from "react";

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { cn } from "@/lib/utils";

interface InsightCard {
  label: string;
  value: string;
  description: string;
  icon: LucideIcon;
  tone: "mint" | "sky" | "sun" | "grape";
}

interface InsightBar {
  label: string;
  value: number;
  caption?: string;
}

interface PetDetailInsightsProps {
  cards: InsightCard[];
  chartTitle: string;
  bars: InsightBar[];
}

const TONES = {
  mint: "bg-mint-soft text-mint",
  sky: "bg-sky-soft text-sky",
  sun: "bg-sun-soft text-sun",
  grape: "bg-grape-soft text-grape",
} as const;

export function PetDetailInsights({ cards, chartTitle, bars }: PetDetailInsightsProps) {
  const max = Math.max(...bars.map((bar) => bar.value), 1);
  const chartRef = useRef<HTMLDivElement>(null);
  const [canScroll, setCanScroll] = useState(false);

  useEffect(() => {
    const chart = chartRef.current;
    if (!chart) return;

    const updateScrollState = () => {
      setCanScroll(chart.scrollWidth > chart.clientWidth + 1 && chart.scrollLeft < chart.scrollWidth - chart.clientWidth - 1);
    };

    updateScrollState();
    chart.addEventListener("scroll", updateScrollState, { passive: true });
    const observer = new ResizeObserver(updateScrollState);
    observer.observe(chart);

    return () => {
      chart.removeEventListener("scroll", updateScrollState);
      observer.disconnect();
    };
  }, [bars.length]);

  return (
    <section aria-label="Resumo do pet" className="flex flex-col gap-3">
      <div className={cn("grid gap-3", cards.length === 4 ? "sm:grid-cols-2 lg:grid-cols-4" : "sm:grid-cols-3")}>
        {cards.map(({ label, value, description, icon: Icon, tone }) => (
          <Card key={label} size="sm">
            <CardContent className="flex items-center gap-3">
              <span className={cn("flex size-10 shrink-0 items-center justify-center rounded-full", TONES[tone])}>
                <Icon className="size-5" aria-hidden="true" />
              </span>
              <div className="min-w-0">
                <p className="text-xs font-medium uppercase tracking-wide text-muted-foreground">{label}</p>
                <p className="truncate text-xl font-bold">{value}</p>
                <p className="truncate text-xs text-muted-foreground">{description}</p>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      <Card size="sm">
        <CardHeader>
          <CardTitle>{chartTitle}</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="relative">
            <div ref={chartRef} className="flex h-40 items-end gap-2 overflow-x-auto" role="img" aria-label={chartTitle}>
              <div className="flex h-full min-w-max items-end gap-2">
                {bars.map((bar) => (
                  <div key={bar.label} className="flex h-full w-16 shrink-0 flex-col items-center justify-end gap-1 sm:w-auto sm:flex-1">
                    <span className="text-xs font-semibold">{bar.caption ?? bar.value}</span>
                    <div className="flex min-h-0 w-full flex-1 items-end rounded-t-sm bg-muted/50">
                      <span className="w-full rounded-t-sm bg-brand transition-all" style={{ height: `${Math.max((bar.value / max) * 100, 8)}%` }} />
                    </div>
                    <span className="max-w-20 truncate text-[10px] text-muted-foreground" title={bar.label}>
                      {bar.label}
                    </span>
                  </div>
                ))}
              </div>
            </div>
            {canScroll && (
              <span className="pointer-events-none absolute right-0 top-1/2 rounded-l-full bg-card/95 py-1 pl-3 pr-1 text-[10px] font-medium text-muted-foreground shadow-sm sm:hidden">Arraste →</span>
            )}
          </div>
        </CardContent>
      </Card>
    </section>
  );
}
