import type { Metadata } from "next";

import { NotFoundState } from "@/features/ong/components/NotFoundState";

export const metadata: Metadata = { title: "Página não encontrada", robots: { index: false, follow: false } };

export default function NotFound() {
  return (
    <NotFoundState
      title="404 · Página não encontrada"
      text="O endereço que você abriu não existe ou foi movido. Volte para o início ou veja os pets que esperam um lar."
      href="/"
      label="Voltar para o início"
      secondary={{ href: "/adocao", label: "Ver pets para adoção" }}
    />
  );
}
