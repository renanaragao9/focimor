import { MailCheck } from "lucide-react";
import { Suspense } from "react";

import { AuthHero } from "@/components/auth/AuthHero";
import { AuthShell } from "@/components/auth/AuthShell";
import { VerifyEmailForm } from "@/features/auth/register/components/VerifyEmailForm";

export function VerifyEmailView() {
  return (
    <AuthShell signedInDestination="/boas-vindas" hero={<AuthHero lead="Falta só confirmar o seu e-mail para liberar o acesso." />} icon={MailCheck} title="Confirme seu e-mail" description="Digite o código que enviamos para você.">
      <Suspense>
        <VerifyEmailForm />
      </Suspense>
    </AuthShell>
  );
}
