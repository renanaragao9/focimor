import { Newspaper, PawPrint, PiggyBank } from "lucide-react";
import Link from "next/link";

import type { OngTab } from "@/features/ong/types/ongTypes";
import { ongHref } from "@/features/ong/utils/ongParams";
import { cn } from "@/lib/utils";

const TABS = [
  { key: "pets", label: "Para adoção", icon: PawPrint, active: "bg-brand text-brand-foreground", idle: "text-brand-deep hover:bg-brand-soft dark:text-brand" },
  { key: "caixinhas", label: "Caixinhas", icon: PiggyBank, active: "bg-grape text-white dark:text-background", idle: "text-grape hover:bg-grape-soft" },
  { key: "posts", label: "Novidades", icon: Newspaper, active: "bg-sky text-white dark:text-background", idle: "text-sky hover:bg-sky-soft" },
] as const;

export function OngTabs({ uuid, active }: { uuid: string; active: OngTab }) {
  return (
    <nav aria-label="Seções da ONG" className="flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
      {TABS.map(({ key, label, icon: Icon, active: activeClass, idle }) => (
        <Link
          key={key}
          href={ongHref(uuid, { tab: key })}
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
