import type { Metadata } from "next";

import { RegisterView } from "@/features/auth/register/views/RegisterView";

export const metadata: Metadata = { title: "Criar conta", description: "Crie sua conta gratuita no Focimor e encontre o par ideal para o seu pet." };

export default function Page() {
  return <RegisterView />;
}
