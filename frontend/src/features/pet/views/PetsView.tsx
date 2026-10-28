import { redirect } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { H1, Muted } from "@/components/ui/typography";
import { PetsManager } from "@/features/pet/components/PetsManager";
import { fetchPetCatalogs, fetchPets } from "@/features/pet/server/petServer";
import type { PetFilters } from "@/features/pet/types/petTypes";
import { petsHref } from "@/features/pet/utils/petFilters";
import type { AuthUser } from "@/lib/auth/authTypes";

interface PetsViewProps {
  user: AuthUser;
  filters: PetFilters;
}

export async function PetsView({ user, filters }: PetsViewProps) {
  const [page, catalogs] = await Promise.all([fetchPets(filters), fetchPetCatalogs()]);

  if (page.data.length === 0 && filters.page > 1) redirect(petsHref({ ...filters, page: Math.max(page.meta.last_page, 1) }));

  return (
    <AppShell user={user}>
      <div className="flex flex-col gap-1">
        <H1 className="text-3xl">Meus pets</H1>
        <Muted>Cadastre e organize os seus pets. Fotos e dados completos ajudam a encontrar o par ideal.</Muted>
      </div>
      <PetsManager page={page} filters={filters} catalogs={catalogs} />
    </AppShell>
  );
}
