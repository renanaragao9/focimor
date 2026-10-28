"use client";

import type { ReactElement } from "react";

import { Tooltip, TooltipContent, TooltipTrigger } from "@/components/ui/tooltip";

interface HintProps {
  label: string;
  shortcut?: readonly string[];
  side?: "top" | "right" | "bottom" | "left";
  children: ReactElement;
}

export function Hint({ label, shortcut, side = "bottom", children }: HintProps) {
  return (
    <Tooltip delayDuration={150}>
      <TooltipTrigger asChild>{children}</TooltipTrigger>
      <TooltipContent
        side={side}
        sideOffset={8}
        className="gap-2.5 rounded-md border border-sidebar-border bg-sidebar px-3 py-2 text-sm font-medium text-sidebar-foreground shadow-lg [&>svg]:bg-sidebar [&>svg]:fill-sidebar"
      >
        {label}
        {shortcut && (
          <span className="flex items-center gap-1">
            {shortcut.map((key) => (
              <kbd
                key={key}
                className="rounded border border-sidebar-primary/40 bg-sidebar-accent px-1.5 py-0.5 font-sans text-[11px] font-semibold text-sidebar-primary"
              >
                {key}
              </kbd>
            ))}
          </span>
        )}
      </TooltipContent>
    </Tooltip>
  );
}
