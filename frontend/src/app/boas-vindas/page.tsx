import type { Metadata } from "next";
import { Suspense } from "react";

import { WelcomeSkeleton } from "@/features/auth/welcome/components/WelcomeSkeleton";
import { fetchWelcomeCounts } from "@/features/auth/welcome/server/welcomeServer";
import { WelcomeView } from "@/features/auth/welcome/views/WelcomeView";
import { requireUser } from "@/lib/auth/requireUser";

export const metadata: Metadata = { title: "Boas-vindas", robots: { index: false, follow: false } };

async function WelcomeContent() {
  const user = await requireUser("/entrar", "/boas-vindas");

  const counts = await fetchWelcomeCounts();

  return <WelcomeView user={user} counts={counts} />;
}

export default function WelcomePage() {
  return (
    <Suspense fallback={<WelcomeSkeleton />}>
      <WelcomeContent />
    </Suspense>
  );
}
