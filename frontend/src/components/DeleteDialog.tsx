"use client";

import { Loader2, Trash2, type LucideIcon } from "lucide-react";
import { useState } from "react";
import { toast } from "sonner";

import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { ApiError } from "@/lib/api/errors";
import { cn } from "@/lib/utils";

interface DeleteDialogProps {
  name: string | null;
  title: string;
  description?: string;
  warning: string;
  successMessage: string;
  errorMessage: string;
  onConfirm: () => Promise<void>;
  onDeleted: () => void;
  confirmLabel?: string;
  dismissLabel?: string;
  icon?: LucideIcon;
  tone?: "destructive" | "neutral";
  onOpenChange: (open: boolean) => void;
}

export function DeleteDialog({
  name,
  title,
  description = "Esta ação não pode ser desfeita.",
  warning,
  successMessage,
  errorMessage,
  onConfirm,
  onDeleted,
  confirmLabel = "Excluir",
  dismissLabel = "Cancelar",
  icon: Icon = Trash2,
  tone = "destructive",
  onOpenChange,
}: DeleteDialogProps) {
  const [isDeleting, setIsDeleting] = useState(false);

  async function handleDelete(): Promise<void> {
    setIsDeleting(true);
    try {
      await onConfirm();
      toast.success(successMessage);
      onDeleted();
      onOpenChange(false);
    } catch (caught) {
      toast.error(caught instanceof ApiError ? caught.message : errorMessage);
    } finally {
      setIsDeleting(false);
    }
  }

  return (
    <Dialog open={name !== null} onOpenChange={onOpenChange}>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
          <DialogDescription>{description}</DialogDescription>
        </DialogHeader>

        <div className="flex flex-col items-center gap-3 py-2 text-center">
          <span className={cn("flex size-16 items-center justify-center rounded-full", tone === "destructive" ? "bg-destructive/10" : "bg-brand/10")}>
            <Icon className={cn("size-8", tone === "destructive" ? "text-destructive" : "text-brand")} aria-hidden="true" />
          </span>
          <p className="max-w-full truncate text-xl font-bold">{name}</p>
          <p className="max-w-sm text-sm text-muted-foreground">{warning}</p>
        </div>

        <DialogFooter>
          <Button variant="outline" onClick={() => onOpenChange(false)} disabled={isDeleting}>
            {dismissLabel}
          </Button>
          <Button variant={tone === "destructive" ? "destructive" : "default"} onClick={handleDelete} disabled={isDeleting}>
            {isDeleting && <Loader2 className="animate-spin" />}
            {confirmLabel}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
