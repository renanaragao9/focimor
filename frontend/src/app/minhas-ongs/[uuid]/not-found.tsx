import { NotFoundState } from "@/features/ong/components/NotFoundState";

export default function PanelNotFound() {
  return <NotFoundState title="ONG não encontrada" text="Essa ONG não existe ou você não faz parte dela." href="/minhas-ongs" label="Voltar para Minhas ONGs" />;
}
