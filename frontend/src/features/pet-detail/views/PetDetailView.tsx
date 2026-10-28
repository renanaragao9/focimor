import { notFound, redirect } from "next/navigation";
import { Suspense } from "react";

import { AppShell } from "@/components/app/AppShell";
import { HealthRecordsTab } from "@/features/pet-detail/components/HealthRecordsTab";
import { PetAbout } from "@/features/pet-detail/components/PetAbout";
import { PetDetailForm } from "@/features/pet-detail/components/PetDetailForm";
import { PetDetailHeader } from "@/features/pet-detail/components/PetDetailHeader";
import { PetTabs } from "@/features/pet-detail/components/PetTabs";
import { TabSkeleton } from "@/features/pet-detail/components/TabSkeleton";
import { VaccinesTab } from "@/features/pet-detail/components/VaccinesTab";
import { WeightsTab } from "@/features/pet-detail/components/WeightsTab";
import { fetchHealthRecords, fetchHealthRecordTypes, fetchPet, fetchVaccines, fetchWeights } from "@/features/pet-detail/server/petDetailServer";
import type { PetWithDetail } from "@/features/pet-detail/types/petDetailTypes";
import { petDetailHref, type PetDetailParams } from "@/features/pet-detail/utils/petDetailParams";
import { ApiError } from "@/lib/api/errors";
import type { ApiPage } from "@/lib/api/types";
import type { AuthUser } from "@/lib/auth/authTypes";

interface PetDetailViewProps {
  user: AuthUser;
  uuid: string;
  params: PetDetailParams;
}

async function loadPet(uuid: string): Promise<PetWithDetail> {
  try {
    return await fetchPet(uuid);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}

function backToLastPage<T>(page: ApiPage<T>, uuid: string, params: PetDetailParams): void {
  if (page.data.length === 0 && params.page > 1) redirect(petDetailHref(uuid, { ...params, page: Math.max(page.meta.last_page, 1) }));
}

async function PetTabContent({ pet, params }: { pet: PetWithDetail; params: PetDetailParams }) {
  switch (params.tab) {
    case "vacinas": {
      const page = await fetchVaccines(pet.uuid, params.page);
      backToLastPage(page, pet.uuid, params);
      return <VaccinesTab petUuid={pet.uuid} page={page} />;
    }
    case "saude": {
      const [page, types] = await Promise.all([fetchHealthRecords(pet.uuid, params.page, params.typeId), fetchHealthRecordTypes()]);
      backToLastPage(page, pet.uuid, params);
      return <HealthRecordsTab petUuid={pet.uuid} page={page} types={types} typeId={params.typeId} />;
    }
    case "pesos": {
      const page = await fetchWeights(pet.uuid, params.page);
      backToLastPage(page, pet.uuid, params);
      return <WeightsTab petUuid={pet.uuid} page={page} />;
    }
    default:
      return (
        <>
          <PetAbout pet={pet} />
          <PetDetailForm key={pet.detail?.updated_at ?? "empty"} petUuid={pet.uuid} detail={pet.detail} />
        </>
      );
  }
}

export async function PetDetailView({ user, uuid, params }: PetDetailViewProps) {
  const pet = await loadPet(uuid);

  return (
    <AppShell user={user}>
      <PetDetailHeader pet={pet} />
      <PetTabs uuid={pet.uuid} active={params.tab} />
      <Suspense key={`${params.tab}-${params.page}-${params.typeId}`} fallback={<TabSkeleton />}>
        <PetTabContent pet={pet} params={params} />
      </Suspense>
    </AppShell>
  );
}
