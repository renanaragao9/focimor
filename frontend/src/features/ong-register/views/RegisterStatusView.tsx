import { AuthShell } from "@/components/auth/AuthShell";
import { RegisterHero } from "@/features/ong-register/components/RegisterHero";
import { StatusLookup } from "@/features/ong-register/components/StatusLookup";

export function RegisterStatusView() {
  return (
    <AuthShell hero={<RegisterHero />} title="Acompanhe seu pré-cadastro" description="Informe o e-mail ou o telefone usado no envio." cardClassName="max-w-xl">
      <StatusLookup />
    </AuthShell>
  );
}
