"use client";

import { useIsClient } from "@/hooks/use-is-client";
import { formatClock, listStamp, parseApiDateTime } from "@/lib/dateTime";

interface LocalTimeProps {
  value: string | null;
  kind: "clock" | "stamp";
  className?: string;
}

export function LocalTime({ value, kind, className }: LocalTimeProps) {
  const isClient = useIsClient();
  const date = parseApiDateTime(value);
  if (!isClient || !date) return <span className={className} aria-hidden="true" />;

  return (
    <time dateTime={date.toISOString()} className={className}>
      {kind === "clock" ? formatClock(date) : listStamp(date, new Date())}
    </time>
  );
}
