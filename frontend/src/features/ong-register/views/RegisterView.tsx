import { AuthShell } from "@/components/auth/AuthShell";
import { RegisterHero } from "@/features/ong-register/components/RegisterHero";
import { RegisterPanel } from "@/features/ong-register/components/RegisterPanel";

export function RegisterView() {
  return (
    <AuthShell hero={<RegisterHero />} title="Cadastre sua ONG" description="Faça o pré-cadastro e nossa equipe entra em contato." cardClassName="max-w-2xl">
      <RegisterPanel />
    </AuthShell>
  );
}
