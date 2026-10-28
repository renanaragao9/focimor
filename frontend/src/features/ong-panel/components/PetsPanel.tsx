"use client";

import { Eye, ImageIcon, PawPrint, Plus } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { Pagination } from "@/components/Pagination";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { PetAvatar } from "@/features/chat/components/PetAvatar";
import { panelHref } from "@/features/ong-panel/utils/panelParams";
import { EmptyState } from "@/features/ong/components/EmptyState";
import { PetFormDialog } from "@/features/pet/components/PetFormDialog";
import type { Pet, PetCatalogs } from "@/features/pet/types/petTypes";
import { petSubtitle } from "@/features/pet/utils/petDisplay";
import type { ApiPage } from "@/lib/api/types";

interface PetsPanelProps {
  ongUuid: string;
  page: ApiPage<Pet>;
  catalogs: PetCatalogs;
  canPublish: boolean;
}

export function PetsPanel({ ongUuid, page, catalogs, canPublish }: PetsPanelProps) {
  const router = useRouter();
  const [formOpen, setFormOpen] = useState(false);
  const [formKey, setFormKey] = useState("new-0");

  function openCreate(): void {
    setFormKey(`new-${Date.now()}`);
    setFormOpen(true);
  }

  const createButton = (
    <Button className="cursor-pointer" disabled={!canPublish} onClick={openCreate}>
      <Plus /> Novo pet
    </Button>
  );

  return (
    <>
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="text-xl font-semibold">Pets para adoção</h2>
          <p className="text-sm text-muted-foreground">{canPublish ? "Todo pet cadastrado aqui entra na lista de adoção." : "Disponível depois que a ONG for verificada."}</p>
        </div>
        {createButton}
      </div>

      {page.data.length === 0 ? (
        <EmptyState icon={PawPrint} title="Nenhum pet cadastrado" text="Cadastre os pets da ONG para que as pessoas possam demonstrar interesse." />
      ) : (
        <>
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {page.data.map((pet) => (
              <article key={pet.uuid} className="flex items-center gap-3 rounded-sm border bg-card p-3">
                <PetAvatar name={pet.name} photos={pet.photos} className="size-16" />
                <div className="flex min-w-0 flex-1 flex-col gap-1">
                  <div className="flex items-center justify-between gap-2">
                    <h3 className="truncate text-base font-semibold">{pet.name}</h3>
                    <Badge variant={pet.is_active ? "secondary" : "outline"}>{pet.is_active ? "Ativo" : "Inativo"}</Badge>
                  </div>
                  <p className="truncate text-xs text-muted-foreground">{petSubtitle(pet)}</p>
                  <div className="flex flex-wrap gap-x-3">
                    <Button variant="link" size="sm" className="h-auto w-fit p-0 text-xs" asChild>
                      <Link href={`/meus-pets/${pet.uuid}`}>
                        <Eye /> Ver detalhes
                      </Link>
                    </Button>
                    <Button variant="link" size="sm" className="h-auto w-fit p-0 text-xs" asChild>
                      <Link href={`/meus-pets/${pet.uuid}/fotos`}>
                        <ImageIcon /> Fotos ({pet.photos.length})
                      </Link>
                    </Button>
                  </div>
                </div>
              </article>
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => panelHref(ongUuid, { tab: "pets", page: target })} />
        </>
      )}

      <PetFormDialog
        key={formKey}
        pet={null}
        open={formOpen}
        catalogs={catalogs}
        ongUuid={ongUuid}
        onOpenChange={setFormOpen}
        onSaved={() => {
          setFormOpen(false);
          router.refresh();
        }}
      />
    </>
  );
}
