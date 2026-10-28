import { NotFoundState } from "@/features/ong/components/NotFoundState";

export default function PostNotFound() {
  return <NotFoundState title="Publicação não encontrada" text="Essa publicação não existe ou foi removida." href="/posts" label="Voltar para Novidades" />;
}
