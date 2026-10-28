import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import { parseOngDetailParams } from "@/features/ong/utils/ongParams";
import { OngView } from "@/features/ong/views/OngView";
import { fetchOng } from "@/features/ong/server/ongServer";
import { locationLabel } from "@/features/ong/utils/ongDisplay";
import { clip, pageMetadata } from "@/lib/seo";
import type { RawSearchParams } from "@/lib/searchParams";
import { isUuid } from "@/lib/uuid";

export async function generateMetadata({ params }: { params: Promise<{ uuid: string }> }): Promise<Metadata> {
  const { uuid } = await params;
  if (!isUuid(uuid)) return { title: "ONG", robots: { index: false } };

  try {
    const ong = await fetchOng(uuid);
    const place = locationLabel(ong.city, ong.state);

    return pageMetadata({
      title: place ? `${ong.name} em ${place}` : ong.name,
      description: clip(ong.description) ?? `Conheça a ${ong.name}, veja os pets para adoção e ajude com doações.`,
      path: `/ongs/${uuid}`,
      image: ong.logo_url,
    });
  } catch {
    return { title: "ONG", robots: { index: false } };
  }
}

async function OngContent({ params, searchParams }: { params: Promise<{ uuid: string }>; searchParams: Promise<RawSearchParams> }) {
  const { uuid } = await params;
  if (!isUuid(uuid)) notFound();

  return <OngView uuid={uuid} params={parseOngDetailParams(await searchParams)} />;
}

export default function OngPage(props: { params: Promise<{ uuid: string }>; searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <OngContent {...props} />
    </Suspense>
  );
}
