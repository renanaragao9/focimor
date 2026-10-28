"use client";

import { Loader2, Save } from "lucide-react";
import type { FormEvent, ReactNode } from "react";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { FieldGroup } from "@/components/ui/field";

interface RecordFormDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  title: string;
  description: string;
  submitLabel: string;
  savingLabel?: string;
  isSaving: boolean;
  formError: string | null;
  onSubmit: (event: FormEvent<HTMLFormElement>) => void;
  children: ReactNode;
}

export function RecordFormDialog({ open, onOpenChange, title, description, submitLabel, savingLabel = "Salvando...", isSaving, formError, onSubmit, children }: RecordFormDialogProps) {
  return (
    <Dialog open={open} onOpenChange={(next) => !isSaving && onOpenChange(next)}>
      <DialogContent className="h-[var(--vvh,100dvh)] max-h-[var(--vvh,100dvh)] w-screen max-w-none rounded-none overflow-y-auto sm:h-auto sm:max-h-[92dvh] sm:w-full sm:max-w-xl sm:rounded-sm">
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
          <DialogDescription>{description}</DialogDescription>
        </DialogHeader>

        <form onSubmit={onSubmit} noValidate>
          <FieldGroup className="gap-5">
            {formError && <FormErrorAlert message={formError} />}
            {children}
            <Button type="submit" className="w-full cursor-pointer sm:ml-auto sm:w-auto" disabled={isSaving}>
              {isSaving ? <Loader2 className="animate-spin" /> : <Save />}
              {isSaving ? savingLabel : submitLabel}
            </Button>
          </FieldGroup>
        </form>
      </DialogContent>
    </Dialog>
  );
}
