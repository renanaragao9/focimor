"use client";

import { Heart, PawPrint } from "lucide-react";
import Image from "next/image";
import Link from "next/link";

import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import type { MatchInfo } from "@/features/match/types/matchTypes";
import type { PetPhoto } from "@/features/pet/types/petTypes";
import { chatHref } from "@/features/chat/utils/matchesParams";
import { primaryPhoto } from "@/features/pet/utils/petDisplay";

interface MatchDialogProps {
  match: MatchInfo | null;
  myPhotos: PetPhoto[];
  onClose: () => void;
}

function Portrait({ photo, name }: { photo: PetPhoto | null; name: string }) {
  return (
    <div className="relative size-28 overflow-hidden rounded-full border-4 border-background bg-brand-soft shadow-lg">
      {photo ? (
        <Image src={photo.url} alt={name} fill unoptimized sizes="112px" className="object-cover" />
      ) : (
        <span className="flex size-full items-center justify-center">
          <PawPrint className="size-12 text-brand/70" aria-hidden="true" />
        </span>
      )}
    </div>
  );
}

export function MatchDialog({ match, myPhotos, onClose }: MatchDialogProps) {
  return (
    <Dialog open={match !== null} onOpenChange={(open) => !open && onClose()}>
      <DialogContent className="sm:max-w-md">
        <DialogHeader className="items-center text-center">
          <DialogTitle className="bg-linear-to-r from-brand to-sky bg-clip-text text-4xl font-extrabold text-transparent">É um match!</DialogTitle>
          <DialogDescription>{match ? `${match.my_pet.name} e ${match.other_pet.name} curtiram um ao outro.` : ""}</DialogDescription>
        </DialogHeader>

        {match && (
          <div className="flex items-center justify-center py-4">
            <Portrait photo={primaryPhoto(myPhotos)} name={match.my_pet.name} />
            <span className="z-10 -mx-3 flex size-12 items-center justify-center rounded-full bg-brand text-brand-foreground shadow-lg">
              <Heart className="size-6 fill-current" aria-hidden="true" />
            </span>
            <Portrait photo={primaryPhoto(match.other_pet.photos)} name={match.other_pet.name} />
          </div>
        )}

        {match && (
          <Button className="w-full" asChild>
            <Link href={chatHref(match.uuid)}>Enviar mensagem</Link>
          </Button>
        )}
        <Button variant="outline" className="w-full cursor-pointer" onClick={onClose}>
          Continuar procurando
        </Button>
      </DialogContent>
    </Dialog>
  );
}
