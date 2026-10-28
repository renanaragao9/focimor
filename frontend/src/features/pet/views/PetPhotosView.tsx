import { notFound } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { H1, Muted } from "@/components/ui/typography";
import { PetPhotosPageManager } from "@/features/pet/components/PetPhotosPageManager";
import { fetchPet } from "@/features/pet-detail/server/petDetailServer";
import type { PetWithDetail } from "@/features/pet-detail/types/petDetailTypes";
import { ApiError } from "@/lib/api/errors";
import type { AuthUser } from "@/lib/auth/authTypes";

interface PetPhotosViewProps {
  user: AuthUser;
  uuid: string;
}

async function loadPet(uuid: string): Promise<PetWithDetail> {
  try {
    return await fetchPet(uuid);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}

export async function PetPhotosView({ user, uuid }: PetPhotosViewProps) {
  const pet = await loadPet(uuid);

  return (
    <AppShell user={user}>
      <div className="flex flex-col gap-2">
        <H1 className="text-3xl">Fotos de {pet.name}</H1>
        <Muted>Adicione, escolha a foto principal ou exclua fotos do seu pet.</Muted>
      </div>
      <PetPhotosPageManager petUuid={pet.uuid} initialPhotos={pet.photos} />
    </AppShell>
  );
}
