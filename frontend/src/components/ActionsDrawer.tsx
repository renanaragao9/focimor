"use client";

import type { LucideIcon } from "lucide-react";

import { Button } from "@/components/ui/button";
import { Drawer, DrawerContent, DrawerDescription, DrawerHeader, DrawerTitle } from "@/components/ui/drawer";
import { cn } from "@/lib/utils";

interface ActionOption {
  label: string;
  icon: LucideIcon;
  destructive?: boolean;
  onSelect: () => void;
}

interface ActionsDrawerProps {
  title: string | null;
  options: ActionOption[];
  onOpenChange: (open: boolean) => void;
}

const DRAWER_CLOSE_MS = 250;

export function ActionsDrawer({ title, options, onOpenChange }: ActionsDrawerProps) {
  function handleSelect(option: ActionOption): void {
    onOpenChange(false);
    window.setTimeout(option.onSelect, DRAWER_CLOSE_MS);
  }

  return (
    <Drawer open={title !== null} onOpenChange={onOpenChange}>
      <DrawerContent>
        <DrawerHeader>
          <DrawerTitle>{title}</DrawerTitle>
          <DrawerDescription>Escolha uma ação.</DrawerDescription>
        </DrawerHeader>
        <div className="flex flex-col gap-1 px-4 pb-6">
          {options.map((option) => {
            const Icon = option.icon;

            return (
              <Button key={option.label} variant="ghost" className={cn("h-12 justify-start gap-3", option.destructive && "text-destructive hover:text-destructive")} onClick={() => handleSelect(option)}>
                <Icon />
                {option.label}
              </Button>
            );
          })}
        </div>
      </DrawerContent>
    </Drawer>
  );
}
