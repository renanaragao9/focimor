"use client";

import { ArrowLeft, HeartCrack, MoreVertical } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Button } from "@/components/ui/button";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { PetAvatar } from "@/features/chat/components/PetAvatar";
import { chatService } from "@/features/chat/services/chatService";
import type { MatchItem } from "@/features/chat/types/chatTypes";
import { petSubtitle } from "@/features/pet/utils/petDisplay";

interface ChatHeaderProps {
  match: MatchItem;
}

export function ChatHeader({ match }: ChatHeaderProps) {
  const router = useRouter();
  const [confirmOpen, setConfirmOpen] = useState(false);
  const other = match.other_pet;

  return (
    <header className="flex items-center gap-3 border-b bg-background px-3 py-2.5">
      <Button variant="ghost" size="icon-sm" className="shrink-0" aria-label="Voltar para Matches" asChild>
        <Link href="/matches">
          <ArrowLeft />
        </Link>
      </Button>
      <PetAvatar name={other.name} photos={other.photos} className="size-11" />
      <div className="min-w-0 flex-1">
        <h1 className="truncate text-base font-semibold leading-tight">{other.name}</h1>
        <p className="truncate text-xs text-muted-foreground">
          {petSubtitle({ species: other.species, breed: other.breed ?? null })} · com {match.my_pet.name}
        </p>
      </div>

      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button variant="ghost" size="icon-sm" className="cursor-pointer" aria-label="Mais opções">
            <MoreVertical />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuItem variant="destructive" onClick={() => setConfirmOpen(true)}>
            <HeartCrack /> Desfazer match
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>

      <DeleteDialog
        name={confirmOpen ? other.name : null}
        title="Desfazer match"
        description="A conversa deixa de aparecer para os dois."
        warning="Vocês não poderão mais trocar mensagens. Para voltar a conversar, um dos pets precisa aparecer e ser curtido de novo."
        successMessage="Match desfeito."
        errorMessage="Não foi possível desfazer o match."
        confirmLabel="Desfazer match"
        icon={HeartCrack}
        onConfirm={async () => {
          await chatService.unmatch(match.uuid);
        }}
        onDeleted={() => {
          router.replace("/matches");
          router.refresh();
        }}
        onOpenChange={setConfirmOpen}
      />
    </header>
  );
}
