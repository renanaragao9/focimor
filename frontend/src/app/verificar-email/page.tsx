import type { Metadata } from "next";

import { VerifyEmailView } from "@/features/auth/register/views/VerifyEmailView";

export const metadata: Metadata = { title: "Confirmar e-mail", robots: { index: false, follow: false } };

export default function Page() {
  return <VerifyEmailView />;
}
