import { AuthHero } from "@/components/auth/AuthHero";
import { AuthShell } from "@/components/auth/AuthShell";
import { RegisterForm } from "@/features/auth/register/components/RegisterForm";

export function RegisterView() {
  return (
    <AuthShell
      signedInDestination="/boas-vindas"
      hero={<AuthHero lead="Crie sua conta gratuita e comece a encontrar o par ideal para o seu pet." />}
      title="Criar conta"
      description="Leva menos de um minuto."
      cardClassName="max-w-xl"
    >
      <RegisterForm />
    </AuthShell>
  );
}
