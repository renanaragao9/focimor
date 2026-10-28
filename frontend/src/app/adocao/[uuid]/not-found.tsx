import { NotFoundState } from "@/features/ong/components/NotFoundState";

export default function AdoptablePetNotFound() {
  return <NotFoundState title="Pet não encontrado" text="Esse pet já foi adotado ou não está mais disponível." href="/adocao" label="Ver pets para adoção" />;
}
