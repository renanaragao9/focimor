"use client";

import { ChevronDown, ClipboardList, Dog, HeartHandshake, House, Loader2, LogOut, MessagesSquare, Newspaper, Search, Sparkles, Building2, type LucideIcon } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { firstName, initials } from "@/features/auth/welcome/utils/welcomeDisplay";
import { authService } from "@/lib/auth/authService";
import type { AuthUser } from "@/lib/auth/authTypes";

interface MenuLink {
  label: string;
  href: string;
  icon: LucideIcon;
}

const ACCOUNT_LINKS: readonly MenuLink[] = [
  { label: "Início", href: "/boas-vindas", icon: House },
  { label: "Meus pets", href: "/meus-pets", icon: Dog },
  { label: "Match", href: "/match", icon: Sparkles },
  { label: "Matches e conversas", href: "/matches", icon: MessagesSquare },
  { label: "Meus interesses", href: "/adocoes", icon: ClipboardList },
  { label: "Minha ONG", href: "/minhas-ongs", icon: Building2 },
];

const EXPLORE_LINKS: readonly MenuLink[] = [
  { label: "Adoção", href: "/adocao", icon: HeartHandshake },
  { label: "ONGs", href: "/ongs", icon: Search },
  { label: "Novidades", href: "/posts", icon: Newspaper },
];

const ITEM_CLASS = "cursor-pointer gap-3 px-3 py-2.5 text-[15px]";

function MenuLinks({ links }: { links: readonly MenuLink[] }) {
  return links.map(({ label, href, icon: Icon }) => (
    <DropdownMenuItem key={href} asChild className={ITEM_CLASS}>
      <Link href={href}>
        <Icon className="size-5 text-muted-foreground" /> {label}
      </Link>
    </DropdownMenuItem>
  ));
}

export function UserAccountMenu({ user }: { user: AuthUser }) {
  const router = useRouter();
  const [isLoading, setIsLoading] = useState(false);

  async function handleLogout(): Promise<void> {
    setIsLoading(true);
    try {
      await authService.logout();
    } finally {
      router.replace("/entrar");
      router.refresh();
    }
  }

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button
          variant="ghost"
          aria-label="Menu do usuário"
          className="group h-11 cursor-pointer gap-2.5 rounded-full border border-border py-0 pr-3 pl-1 transition-colors hover:bg-muted data-[state=open]:bg-muted"
        >
          <Avatar className="size-9">
            {user.avatar_url && <AvatarImage src={user.avatar_url} alt="" />}
            <AvatarFallback className="bg-brand-soft text-xs font-bold text-brand-deep">{initials(user.name)}</AvatarFallback>
          </Avatar>
          <span className="flex flex-col items-start leading-tight">
            <span className="max-w-24 truncate text-sm font-semibold sm:max-w-32">{firstName(user.name)}</span>
            <span className="text-xs font-normal text-muted-foreground">Minha conta</span>
          </span>
          <ChevronDown className="size-4 text-muted-foreground transition-transform group-data-[state=open]:rotate-180" aria-hidden="true" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-72 p-2">
        <DropdownMenuLabel className="flex flex-col gap-0.5 px-3 py-2">
          <span className="truncate text-[15px] font-semibold">{user.name}</span>
          <span className="truncate text-[13px] font-normal text-muted-foreground">{user.email}</span>
        </DropdownMenuLabel>
        <DropdownMenuSeparator className="my-2" />
        <MenuLinks links={ACCOUNT_LINKS} />
        <DropdownMenuSeparator className="my-2" />
        <MenuLinks links={EXPLORE_LINKS} />
        <DropdownMenuSeparator className="my-2" />
        <DropdownMenuItem
          variant="destructive"
          className={ITEM_CLASS}
          disabled={isLoading}
          onSelect={(event) => {
            event.preventDefault();
            void handleLogout();
          }}
        >
          {isLoading ? <Loader2 className="size-5 animate-spin" /> : <LogOut className="size-5" />}
          Sair
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
