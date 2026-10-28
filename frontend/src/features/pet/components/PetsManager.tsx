"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Pagination } from "@/components/Pagination";
import { PetCard } from "@/features/pet/components/PetCard";
import { PetFormDialog } from "@/features/pet/components/PetFormDialog";
import { PetsEmptyState } from "@/features/pet/components/PetsEmptyState";
import { PetsToolbar } from "@/features/pet/components/PetsToolbar";
import { petService } from "@/features/pet/services/petService";
import type { Pet, PetCatalogs, PetFilters } from "@/features/pet/types/petTypes";
import { hasActiveFilters, petsHref } from "@/features/pet/utils/petFilters";
import type { ApiPage } from "@/lib/api/types";

interface PetsManagerProps {
  page: ApiPage<Pet>;
  filters: PetFilters;
  catalogs: PetCatalogs;
}

export function PetsManager({ page, filters, catalogs }: PetsManagerProps) {
  const router = useRouter();
  const [formOpen, setFormOpen] = useState(false);
  const [formPet, setFormPet] = useState<Pet | null>(null);
  const [formKey, setFormKey] = useState("new-0");
  const [petToDelete, setPetToDelete] = useState<Pet | null>(null);

  function openCreate(): void {
    setFormPet(null);
    setFormKey(`new-${Date.now()}`);
    setFormOpen(true);
  }

  function openEdit(pet: Pet): void {
    setFormPet(pet);
    setFormKey(pet.uuid);
    setFormOpen(true);
  }

  function handleSaved(): void {
    router.refresh();
    setFormOpen(false);
  }

  const isFiltered = hasActiveFilters(filters);

  return (
    <>
      <PetsToolbar filters={filters} species={catalogs.species} onCreate={openCreate} />

      {page.data.length === 0 ? (
        <PetsEmptyState filtered={isFiltered} onCreate={openCreate} />
      ) : (
        <>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {page.data.map((pet) => (
              <PetCard key={pet.uuid} pet={pet} onEdit={openEdit} onDelete={setPetToDelete} />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => petsHref({ ...filters, page: target })} />
        </>
      )}

      <PetFormDialog key={formKey} pet={formPet} open={formOpen} catalogs={catalogs} onOpenChange={setFormOpen} onSaved={handleSaved} />

      <DeleteDialog
        name={petToDelete?.name ?? null}
        title="Excluir pet"
        warning="O pet, as fotos e o histórico dele deixam de aparecer. Esta ação não pode ser desfeita."
        successMessage="Pet excluído."
        errorMessage="Não foi possível excluir o pet."
        onConfirm={async () => {
          if (petToDelete) await petService.remove(petToDelete.uuid);
        }}
        onDeleted={() => router.refresh()}
        onOpenChange={(open) => !open && setPetToDelete(null)}
      />
    </>
  );
}
