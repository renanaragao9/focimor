import { FileText, PawPrint, PiggyBank, Settings, Users, type LucideIcon } from "lucide-react";
import Link from "next/link";

import type { PanelTab } from "@/features/ong-panel/types/ongPanelTypes";
import { panelHref } from "@/features/ong-panel/utils/panelParams";
import { cn } from "@/lib/utils";

const TABS: Array<{ key: PanelTab; label: string; icon: LucideIcon; active: string; idle: string }> = [
  { key: "dados", label: "Dados", icon: Settings, active: "bg-brand text-brand-foreground", idle: "text-brand-deep hover:bg-brand-soft dark:text-brand" },
  { key: "pets", label: "Pets", icon: PawPrint, active: "bg-mint text-white dark:text-background", idle: "text-mint hover:bg-mint-soft" },
  { key: "caixinhas", label: "Caixinhas", icon: PiggyBank, active: "bg-grape text-white dark:text-background", idle: "text-grape hover:bg-grape-soft" },
  { key: "posts", label: "Posts", icon: FileText, active: "bg-sky text-white dark:text-background", idle: "text-sky hover:bg-sky-soft" },
  { key: "membros", label: "Membros", icon: Users, active: "bg-sun text-white dark:text-background", idle: "text-sun hover:bg-sun-soft" },
];

export function PanelTabs({ uuid, active }: { uuid: string; active: PanelTab }) {
  return (
    <nav aria-label="Seções da ONG" className="flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
      {TABS.map(({ key, label, icon: Icon, active: activeClass, idle }) => (
        <Link
          key={key}
          href={panelHref(uuid, { tab: key })}
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
      ))}
    </nav>
  );
}
