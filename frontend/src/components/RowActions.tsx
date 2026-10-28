"use client";

import { Pencil, Trash2 } from "lucide-react";

import { Button } from "@/components/ui/button";

interface RowActionsProps {
  label: string;
  onEdit: () => void;
  onDelete: () => void;
}

export function RowActions({ label, onEdit, onDelete }: RowActionsProps) {
  return (
    <div className="flex justify-end gap-1">
      <Button type="button" variant="ghost" size="icon-sm" className="cursor-pointer" aria-label={`Editar ${label}`} onClick={onEdit}>
        <Pencil />
      </Button>
      <Button type="button" variant="ghost" size="icon-sm" className="cursor-pointer text-destructive hover:text-destructive" aria-label={`Excluir ${label}`} onClick={onDelete}>
        <Trash2 />
      </Button>
    </div>
  );
}
