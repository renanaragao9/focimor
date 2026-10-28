"use client";

import { Inbox, Send } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";

import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { ADOPTION_STATUSES, type AdoptionsParams } from "@/features/adoption/types/adoptionTypes";
import { STATUS_LABELS } from "@/features/adoption/utils/adoptionDisplay";
import { adoptionsHref } from "@/features/adoption/utils/adoptionParams";
import { cn } from "@/lib/utils";

const TABS = [
  { key: "meus", label: "Meus interesses", icon: Send, active: "bg-brand text-brand-foreground", idle: "text-brand-deep hover:bg-brand-soft dark:text-brand" },
  { key: "recebidos", label: "Recebidos", icon: Inbox, active: "bg-sky text-white dark:text-background", idle: "text-sky hover:bg-sky-soft" },
] as const;

const ALL = "all";

export function AdoptionTabs({ params }: { params: AdoptionsParams }) {
  const router = useRouter();

  return (
    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <nav aria-label="Adoções" className="flex gap-2">
        {TABS.map(({ key, label, icon: Icon, active, idle }) => (
          <Link
            key={key}
            href={adoptionsHref({ tab: key })}
            scroll={false}
            aria-current={key === params.tab ? "page" : undefined}
            className={cn(
              "flex items-center gap-2 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-colors",
              key === params.tab ? cn(active, "shadow-md") : cn("bg-card ring-1 ring-foreground/10", idle),
            )}
          >
            <Icon className="size-4" aria-hidden="true" />
            {label}
          </Link>
        ))}
      </nav>

      <Select
        value={params.status || ALL}
        onValueChange={(value) => router.replace(adoptionsHref({ tab: params.tab, status: value === ALL ? "" : (value as AdoptionsParams["status"]) }), { scroll: false })}
      >
        <SelectTrigger aria-label="Filtrar por situação" className="w-full cursor-pointer sm:w-44">
          <SelectValue placeholder="Situação" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value={ALL}>Todas as situações</SelectItem>
          {ADOPTION_STATUSES.map((status) => (
            <SelectItem key={status} value={status}>
              {STATUS_LABELS[status]}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
    </div>
  );
}
