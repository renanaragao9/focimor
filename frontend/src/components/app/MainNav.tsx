"use client";

import { Building2, HandHeart, HeartHandshake, House, Newspaper, PawPrint, type LucideIcon } from "lucide-react";
import Link from "next/link";
import { usePathname } from "next/navigation";

import { cn } from "@/lib/utils";

interface NavLink {
  href: string;
  label: string;
  icon: LucideIcon;
}

const PUBLIC_LINKS: NavLink[] = [
  { href: "/", label: "Início", icon: House },
  { href: "/adocao", label: "Adoção", icon: HandHeart },
  { href: "/ongs", label: "ONGs", icon: HeartHandshake },
  { href: "/posts", label: "Novidades", icon: Newspaper },
];

const APP_LINKS: NavLink[] = [
  { href: "/boas-vindas", label: "Início", icon: House },
  { href: "/meus-pets", label: "Meus pets", icon: PawPrint },
  { href: "/adocao", label: "Adoção", icon: HandHeart },
  { href: "/ongs", label: "ONGs", icon: HeartHandshake },
  { href: "/minhas-ongs", label: "Minha ONG", icon: Building2 },
  { href: "/posts", label: "Novidades", icon: Newspaper },
];

const STYLES = {
  desktop: {
    nav: "hidden h-full items-center gap-5 xl:gap-8 md:flex",
    item: "flex h-full flex-col items-center justify-center gap-1 border-b-2 border-transparent px-1 text-sm whitespace-nowrap text-muted-foreground transition-colors hover:text-foreground",
  },
  mobile: {
    nav: "mx-auto flex max-w-2xl overflow-x-auto px-2 md:hidden [scrollbar-width:none] [&::-webkit-scrollbar]:hidden",
    item: "flex min-w-18 flex-1 flex-col items-center justify-center gap-1 border-b-2 border-transparent px-2 py-2 text-xs whitespace-nowrap text-muted-foreground",
  },
} as const;

const ACTIVE = "border-foreground font-semibold text-foreground";

interface MainNavProps {
  kind: "public" | "app";
  variant: "desktop" | "mobile";
}

function isActive(pathname: string, href: string): boolean {
  return href === "/" ? pathname === "/" : pathname === href || pathname.startsWith(`${href}/`);
}

export function MainNav({ kind, variant }: MainNavProps) {
  const pathname = usePathname();
  const styles = STYLES[variant];
  const links = kind === "app" ? APP_LINKS : PUBLIC_LINKS;

  return (
    <nav aria-label="Principal" className={styles.nav}>
      {links.map(({ href, label, icon: Icon }) => {
        const active = isActive(pathname, href);

        return (
          <Link key={href} href={href} aria-current={active ? "page" : undefined} className={cn(styles.item, active && ACTIVE)}>
            <Icon className="size-6" aria-hidden="true" />
            {label}
          </Link>
        );
      })}
    </nav>
  );
}
