import { Suspense } from "react";

import { AuthHero } from "@/components/auth/AuthHero";
import { AuthShell } from "@/components/auth/AuthShell";
import { EnterForm } from "@/features/auth/enter/components/EnterForm";

export function EnterView() {
  return (
    <AuthShell
      signedInDestination="/boas-vindas"
      hero={<AuthHero lead="Entre para continuar dando match e encontrando um novo amigo." />}
      title="Bem-vindo de volta"
      description="Entre na sua conta para continuar."
    >
      <Suspense>
        <EnterForm />
      </Suspense>
    </AuthShell>
  );
}
