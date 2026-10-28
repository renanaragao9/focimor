import type { Metadata } from "next";

import { EnterView } from "@/features/auth/enter/views/EnterView";

export const metadata: Metadata = { title: "Entrar", description: "Entre na sua conta Focimor para dar match, adotar e apoiar ONGs." };

export default function Page() {
  return <EnterView />;
}
