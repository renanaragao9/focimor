"use client";

import { ClipboardList, EllipsisVertical, Images, Mars, MessageCircle, PawPrint, Pencil, Sparkles, Syringe, Trash2, Venus } from "lucide-react";
import Image from "next/image";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { ActionsDrawer } from "@/components/ActionsDrawer";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { DEFAULT_MATCH_FILTERS } from "@/features/match/types/matchTypes";
import { matchHref } from "@/features/match/utils/matchFilters";
import { matchesHref } from "@/features/chat/utils/matchesParams";
import type { Pet } from "@/features/pet/types/petTypes";
import { ageLabel, genderLabel, petSubtitle, primaryPhoto } from "@/features/pet/utils/petDisplay";
import { cn } from "@/lib/utils";

interface PetCardProps {
  pet: Pet;
  onEdit: (pet: Pet) => void;
  onDelete: (pet: Pet) => void;
}

export function PetCard({ pet, onEdit, onDelete }: PetCardProps) {
  const router = useRouter();
  const [actionsOpen, setActionsOpen] = useState(false);
  const photo = primaryPhoto(pet.photos);
  const age = ageLabel(pet.birth_date, new Date());
  const GenderIcon = pet.gender === "male" ? Mars : Venus;

  return (
    <Card className={cn("overflow-hidden py-0", !pet.is_active && "opacity-70")}>
      <div className="relative aspect-4/3 bg-brand-soft">
        {photo ? (
          <Image src={photo.url} alt={`Foto de ${pet.name}`} fill unoptimized sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" className="object-cover" />
        ) : (
          <div className="flex size-full items-center justify-center">
            <PawPrint className="size-16 text-brand/70" aria-hidden="true" />
          </div>
        )}
        <div className="absolute left-3 top-3 flex gap-1.5">
          <Badge className="bg-background/90 text-foreground">{pet.goal.name}</Badge>
          {!pet.is_active && <Badge variant="secondary">Inativo</Badge>}
        </div>
      </div>

      <CardContent className="flex flex-col gap-3 pb-4">
        <div className="flex items-start justify-between gap-2">
          <div className="min-w-0">
            <h3 className="truncate text-lg font-semibold">
              <Link href={`/meus-pets/${pet.uuid}`} className="hover:underline">
                {pet.name}
              </Link>
            </h3>
            <p className="truncate text-sm text-muted-foreground">{petSubtitle(pet)}</p>
          </div>
          <span className="flex shrink-0 items-center gap-1 text-sm text-muted-foreground">
            <GenderIcon className={pet.gender === "male" ? "size-4 text-sky" : "size-4 text-brand"} aria-hidden="true" />
            {genderLabel(pet.gender)}
          </span>
        </div>

        <div className="flex flex-wrap gap-1.5">
          {age && <Badge variant="outline">{age}</Badge>}
          {pet.size && <Badge variant="outline">{pet.size.name}</Badge>}
          {pet.is_vaccinated && (
            <Badge variant="secondary" className="gap-1">
              <Syringe className="size-3" aria-hidden="true" /> Vacinado
            </Badge>
          )}
          {pet.is_neutered && <Badge variant="secondary">Castrado</Badge>}
        </div>

        <div className="hidden flex-wrap gap-2 sm:flex">
          <Button size="sm" className="flex-1" asChild>
            <Link href={matchHref({ ...DEFAULT_MATCH_FILTERS, petUuid: pet.uuid })}>
              <Sparkles /> Match
            </Link>
          </Button>
          <Button variant="outline" size="sm" className="flex-1" asChild>
            <Link href={matchesHref({ petUuid: pet.uuid })}>
              <MessageCircle /> Mensagens
            </Link>
          </Button>
          <Button size="sm" className="flex-1" asChild>
            <Link href={`/meus-pets/${pet.uuid}`}>
              <ClipboardList /> Detalhes
            </Link>
          </Button>
          <Button variant="outline" size="sm" className="cursor-pointer" onClick={() => onEdit(pet)}>
            <Pencil /> Editar
          </Button>
          <Button variant="outline" size="sm" className="cursor-pointer" asChild>
            <Link href={`/meus-pets/${pet.uuid}/fotos`}>
              <Images /> Fotos
            </Link>
          </Button>
          <Button variant="ghost" size="icon-sm" className="cursor-pointer text-destructive hover:text-destructive" aria-label={`Excluir ${pet.name}`} onClick={() => onDelete(pet)}>
            <Trash2 />
          </Button>
        </div>

        <Button variant="outline" className="cursor-pointer sm:hidden" onClick={() => setActionsOpen(true)}>
          <EllipsisVertical /> Ações
        </Button>
      </CardContent>

      <ActionsDrawer
        title={actionsOpen ? pet.name : null}
        onOpenChange={setActionsOpen}
        options={[
          { label: "Encontrar matches", icon: Sparkles, onSelect: () => router.push(matchHref({ ...DEFAULT_MATCH_FILTERS, petUuid: pet.uuid })) },
          { label: "Ver mensagens", icon: MessageCircle, onSelect: () => router.push(matchesHref({ petUuid: pet.uuid })) },
          { label: "Ver detalhes", icon: ClipboardList, onSelect: () => router.push(`/meus-pets/${pet.uuid}`) },
          { label: "Editar pet", icon: Pencil, onSelect: () => onEdit(pet) },
          { label: "Gerenciar fotos", icon: Images, onSelect: () => router.push(`/meus-pets/${pet.uuid}/fotos`) },
          { label: "Excluir pet", icon: Trash2, destructive: true, onSelect: () => onDelete(pet) },
        ]}
      />
    </Card>
  );
}
