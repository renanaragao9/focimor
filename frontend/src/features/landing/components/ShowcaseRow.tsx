"use client";

import { ChevronLeft, ChevronRight } from "lucide-react";
import Link from "next/link";
import { useId, useRef, type ReactNode } from "react";

import { Button } from "@/components/ui/button";

interface ShowcaseRowProps {
  title: string;
  href: string;
  children: ReactNode;
}

export function ShowcaseRow({ title, href, children }: ShowcaseRowProps) {
  const headingId = useId();
  const scroller = useRef<HTMLDivElement>(null);

  function scrollByPage(direction: -1 | 1): void {
    const element = scroller.current;
    if (element) element.scrollBy({ left: direction * element.clientWidth * 0.8, behavior: "smooth" });
  }

  return (
    <section className="mb-10" aria-labelledby={headingId}>
      <div className="mb-4 flex items-center justify-between gap-4">
        <h2 id={headingId} className="text-xl font-semibold tracking-tight text-foreground">
          {title}
        </h2>
        <div className="flex items-center gap-2">
          <Link href={href} className="mr-1 text-sm font-semibold text-brand hover:underline">
            Ver todos
          </Link>
          <Button type="button" variant="outline" size="icon-sm" aria-label="Voltar lista" onClick={() => scrollByPage(-1)}>
            <ChevronLeft className="text-muted-foreground" />
          </Button>
          <Button type="button" variant="outline" size="icon-sm" aria-label="Avançar lista" onClick={() => scrollByPage(1)}>
            <ChevronRight className="text-muted-foreground" />
          </Button>
        </div>
      </div>
      <div ref={scroller} className="flex gap-3 overflow-x-auto pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        {children}
      </div>
    </section>
  );
}
