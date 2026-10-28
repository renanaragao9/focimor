import { NotFoundState } from "@/features/ong/components/NotFoundState";

export default function AdoptionNotFound() {
  return <NotFoundState title="Adoção não encontrada" text="Esse interesse não existe ou você não tem acesso a ele." href="/adocoes" label="Voltar para Adoções" />;
}
