"use client";

import Image from "next/image";
import { useState } from "react";

import { Dialog, DialogContent, DialogTitle } from "@/components/ui/dialog";

interface PetImageDialogProps {
  name: string;
  src: string;
}

export function PetImageDialog({ name, src }: PetImageDialogProps) {
  const [open, setOpen] = useState(false);

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <button
        type="button"
        className="relative size-24 shrink-0 overflow-hidden rounded-full border-4 border-white bg-white shadow-lg ring-4 ring-brand/30 transition-opacity hover:opacity-85 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 sm:size-28 dark:border-card"
        aria-label={`Ampliar foto de ${name}`}
        onClick={() => setOpen(true)}
      >
        <Image
          src={src}
          alt={`Foto de ${name}`}
          fill
          unoptimized
          sizes="112px"
          className="object-cover"
        />
      </button>

      <DialogContent className="gap-0 bg-black/90 p-0 text-white sm:h-[85dvh] sm:max-w-4xl [&_[data-slot=dialog-close]]:text-white">
        <DialogTitle className="sr-only">Foto de {name}</DialogTitle>
        <div className="relative min-h-0 flex-1">
          <Image
            src={src}
            alt={`Foto ampliada de ${name}`}
            fill
            unoptimized
            sizes="(min-width: 640px) 56rem, 100vw"
            className="object-contain"
          />
        </div>
      </DialogContent>
    </Dialog>
  );
}
