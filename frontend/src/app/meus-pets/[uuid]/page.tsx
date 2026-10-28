import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { PetsSkeleton } from "@/features/pet/components/PetsSkeleton";
import { PetDetailView } from "@/features/pet-detail/views/PetDetailView";
import { isPetUuid, parsePetDetailParams } from "@/features/pet-detail/utils/petDetailParams";
import { requireUser } from "@/lib/auth/requireUser";
import type { RawSearchParams } from "@/lib/searchParams";

export const metadata: Metadata = { title: "Detalhes do pet", robots: { index: false, follow: false } };

interface PageProps {
  params: Promise<{ uuid: string }>;
  searchParams: Promise<RawSearchParams>;
}

async function PetDetailContent({ params, searchParams }: PageProps) {
  const { uuid } = await params;
  if (!isPetUuid(uuid)) notFound();

  const user = await requireUser("/entrar", `/meus-pets/${uuid}`);

  return <PetDetailView user={user} uuid={uuid} params={parsePetDetailParams(await searchParams)} />;
}

export default function PetDetailPage(props: PageProps) {
  return (
    <Suspense fallback={<PetsSkeleton />}>
      <PetDetailContent {...props} />
    </Suspense>
  );
}
