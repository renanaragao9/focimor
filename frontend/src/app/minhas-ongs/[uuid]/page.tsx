import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Suspense } from "react";

import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import { parsePanelParams } from "@/features/ong-panel/utils/panelParams";
import { MyOngPanelView } from "@/features/ong-panel/views/MyOngPanelView";
import { requireUser } from "@/lib/auth/requireUser";
import type { RawSearchParams } from "@/lib/searchParams";
import { isUuid } from "@/lib/uuid";

export const metadata: Metadata = { title: "Painel da ONG", robots: { index: false, follow: false } };

async function PanelContent({ params, searchParams }: { params: Promise<{ uuid: string }>; searchParams: Promise<RawSearchParams> }) {
  const { uuid } = await params;
  if (!isUuid(uuid)) notFound();

  const user = await requireUser("/entrar", `/minhas-ongs/${uuid}`);

  return <MyOngPanelView user={user} uuid={uuid} params={parsePanelParams(await searchParams)} />;
}

export default function PanelPage(props: { params: Promise<{ uuid: string }>; searchParams: Promise<RawSearchParams> }) {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <PanelContent {...props} />
    </Suspense>
  );
}
