"use client";

import { ArrowLeft } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";
import { useClosePanel } from "@/features/adoption/components/AdoptionChatPanel";
import { adoptionsHref } from "@/features/adoption/utils/adoptionParams";

export function AdoptionChatHeader({ title }: { title: string }) {
  const close = useClosePanel();

  return (
    <header className="flex items-center gap-3 border-b bg-background px-3 py-2.5">
      {close && (
        <Button type="button" variant="ghost" size="icon-sm" className="shrink-0 cursor-pointer sm:hidden" aria-label="Fechar conversa" onClick={close}>
          <ArrowLeft />
        </Button>
      )}
      <Button variant="ghost" size="icon-sm" className="hidden shrink-0 sm:inline-flex" aria-label="Voltar para Adoções" asChild>
        <Link href={adoptionsHref()}>
          <ArrowLeft />
        </Link>
      </Button>
      <h2 className="truncate text-base font-semibold">{title}</h2>
    </header>
  );
}
