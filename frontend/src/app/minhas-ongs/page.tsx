import type { Metadata } from "next";
import { Suspense } from "react";

import { ListSkeleton } from "@/features/ong/components/ListSkeleton";
import { MyOngsView } from "@/features/ong-panel/views/MyOngsView";
import { requireUser } from "@/lib/auth/requireUser";

export const metadata: Metadata = { title: "Minhas ONGs", robots: { index: false, follow: false } };

async function MyOngsContent() {
  const user = await requireUser("/entrar", "/minhas-ongs");

  return <MyOngsView user={user} />;
}

export default function MyOngsPage() {
  return (
    <Suspense fallback={<ListSkeleton />}>
      <MyOngsContent />
    </Suspense>
  );
}
