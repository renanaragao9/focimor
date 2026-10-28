import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { fetchAdoptablePet } from "@/features/adoption/server/adoptablePetServer";
import { locationLabel } from "@/features/ong/utils/ongDisplay";
import { clip, pageMetadata } from "@/lib/seo";
import { AdoptablePetView } from "@/features/adoption/views/AdoptablePetView";
import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import { isUuid } from "@/lib/uuid";

export async function generateMetadata({ params }: { params: Promise<{ uuid: string }> }): Promise<Metadata> {
  const { uuid } = await params;
  if (!isUuid(uuid)) return { title: "Pet para adoção", robots: { index: false } };

  try {
    const pet = await fetchAdoptablePet(uuid);
    const kind = [pet.species.name, pet.breed?.name].filter(Boolean).join(" ");
    const place = locationLabel(pet.ong?.city ?? null, pet.ong?.state ?? null);

    return pageMetadata({
      title: `Adote ${pet.name}`,
      description: clip(pet.bio) ?? `${pet.name}, ${kind} para adoção${pet.ong ? ` na ${pet.ong.name}` : ""}${place ? ` em ${place}` : ""}.`,
      path: `/adocao/${uuid}`,
      image: pet.photos[0]?.url,
    });
  } catch {
    return { title: "Pet para adoção", robots: { index: false } };
  }
}

async function AdoptablePetContent({ params }: { params: Promise<{ uuid: string }> }) {
  const { uuid } = await params;
  if (!isUuid(uuid)) notFound();

  return <AdoptablePetView uuid={uuid} />;
}

export default function AdoptablePetPage({ params }: { params: Promise<{ uuid: string }> }) {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <AdoptablePetContent params={params} />
    </Suspense>
  );
}
