import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { AdoptionsSkeleton } from "@/features/adoption/components/AdoptionsSkeleton";
import { AdoptionView } from "@/features/adoption/views/AdoptionView";
import { requireUser } from "@/lib/auth/requireUser";
import { isUuid } from "@/lib/uuid";

export const metadata: Metadata = { title: "Adoção", robots: { index: false, follow: false } };

async function AdoptionContent({ params }: { params: Promise<{ uuid: string }> }) {
  const { uuid } = await params;
  if (!isUuid(uuid)) notFound();

  const user = await requireUser("/entrar", `/adocoes/${uuid}`);

  return <AdoptionView user={user} uuid={uuid} />;
}

export default function AdoptionPage({ params }: { params: Promise<{ uuid: string }> }) {
  return (
    <Suspense fallback={<AdoptionsSkeleton />}>
      <AdoptionContent params={params} />
    </Suspense>
  );
}
