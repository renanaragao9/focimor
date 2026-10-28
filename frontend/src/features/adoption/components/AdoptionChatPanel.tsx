"use client";

import { MessageCircle } from "lucide-react";
import { createContext, useContext, useEffect, useState, type ReactNode } from "react";

import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";

const ClosePanelContext = createContext<(() => void) | null>(null);

export function useClosePanel() {
  return useContext(ClosePanelContext);
}

export function AdoptionChatPanel({ title, children }: { title: string; children: ReactNode }) {
  const [open, setOpen] = useState(false);

  useEffect(() => {
    if (!open) return;
    const previous = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    return () => {
      document.body.style.overflow = previous;
    };
  }, [open]);

  return (
    <ClosePanelContext.Provider value={() => setOpen(false)}>
      <Button type="button" size="lg" className="w-full cursor-pointer sm:hidden" onClick={() => setOpen(true)}>
        <MessageCircle /> Abrir conversa com {title}
      </Button>
      <div className={cn(open ? "fixed inset-0 z-[60] flex flex-col bg-background pb-[env(safe-area-inset-bottom)]" : "hidden sm:block")}>{children}</div>
    </ClosePanelContext.Provider>
  );
}
