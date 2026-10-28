import { Building2 } from "lucide-react";
import { redirect } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { H1, Muted } from "@/components/ui/typography";
import { MyOngCard } from "@/features/ong-panel/components/MyOngCard";
import { fetchMyOngs } from "@/features/ong-panel/server/ongPanelServer";
import { panelHref } from "@/features/ong-panel/utils/panelParams";
import { EmptyState } from "@/features/ong/components/EmptyState";
import type { AuthUser } from "@/lib/auth/authTypes";

export async function MyOngsView({ user }: { user: AuthUser }) {
  const ongs = await fetchMyOngs();

  if (ongs.length === 1) redirect(panelHref(ongs[0].uuid));

  return (
    <AppShell user={user}>
      <div className="flex flex-col gap-1">
        <H1 className="text-3xl">Minhas ONGs</H1>
        <Muted>As ONGs em que você é administrador ou membro.</Muted>
      </div>

      {ongs.length === 0 ? (
        <EmptyState
          icon={Building2}
          title="Você não faz parte de nenhuma ONG"
          text="Peça a um administrador para adicionar o seu e-mail ou faça o pré-cadastro da sua ONG. Depois da aprovação, ela aparece aqui."
          action={{ href: "/ongs/cadastro", label: "Cadastrar minha ONG" }}
        />
      ) : (
        <div className="grid gap-3 sm:grid-cols-2">
          {ongs.map((ong) => (
            <MyOngCard key={ong.uuid} ong={ong} />
          ))}
        </div>
      )}
    </AppShell>
  );
}
