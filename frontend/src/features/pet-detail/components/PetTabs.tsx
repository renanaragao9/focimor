import { Activity, Scale, Stethoscope, Syringe, type LucideIcon } from "lucide-react";
import Link from "next/link";

import { PET_TABS, type PetTab } from "@/features/pet-detail/types/petDetailTypes";
import { petDetailHref } from "@/features/pet-detail/utils/petDetailParams";
import { cn } from "@/lib/utils";

interface PetTabsProps {
  uuid: string;
  active: PetTab;
}

const TAB_STYLE: Record<PetTab, { icon: LucideIcon; active: string; idle: string }> = {
  detalhes: { icon: Activity, active: "bg-brand text-brand-foreground", idle: "text-brand-deep hover:bg-brand-soft dark:text-brand" },
  vacinas: { icon: Syringe, active: "bg-mint text-white dark:text-background", idle: "text-mint hover:bg-mint-soft" },
  saude: { icon: Stethoscope, active: "bg-sky text-white dark:text-background", idle: "text-sky hover:bg-sky-soft" },
  pesos: { icon: Scale, active: "bg-sun text-white dark:text-background", idle: "text-sun hover:bg-sun-soft" },
};

export function PetTabs({ uuid, active }: PetTabsProps) {
  return (
    <nav aria-label="Seções do pet" className="flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
      {PET_TABS.map(({ key, label }) => {
        const { icon: Icon, active: activeClass, idle } = TAB_STYLE[key];

        return (
          <Link
            key={key}
            href={petDetailHref(uuid, { tab: key })}
            scroll={false}
            aria-current={key === active ? "page" : undefined}
            className={cn(
              "flex items-center gap-2 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition-colors",
              key === active ? cn(activeClass, "shadow-md") : cn("bg-card ring-1 ring-foreground/10", idle),
            )}
          >
            <Icon className="size-4" aria-hidden="true" />
            {label}
          </Link>
        );
      })}
    </nav>
  );
}
