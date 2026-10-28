import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { PetsSkeleton } from "@/features/pet/components/PetsSkeleton";
import { PetPhotosView } from "@/features/pet/views/PetPhotosView";
import { isPetUuid } from "@/features/pet-detail/utils/petDetailParams";
import { requireUser } from "@/lib/auth/requireUser";

export const metadata: Metadata = { title: "Fotos do pet", robots: { index: false, follow: false } };

async function PetPhotosContent({ params }: { params: Promise<{ uuid: string }> }) {
  const { uuid } = await params;
  if (!isPetUuid(uuid)) notFound();

  const user = await requireUser("/entrar", `/meus-pets/${uuid}/fotos`);
  return <PetPhotosView user={user} uuid={uuid} />;
}

export default function PetPhotosPage({ params }: { params: Promise<{ uuid: string }> }) {
  return (
    <Suspense fallback={<PetsSkeleton />}>
      <PetPhotosContent params={params} />
    </Suspense>
  );
}
