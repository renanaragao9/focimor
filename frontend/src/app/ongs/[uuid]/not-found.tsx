import { NotFoundState } from "@/features/ong/components/NotFoundState";

export default function OngNotFound() {
  return <NotFoundState title="ONG não encontrada" text="Essa ONG não existe ou não está mais disponível." href="/ongs" label="Voltar para ONGs" />;
}
