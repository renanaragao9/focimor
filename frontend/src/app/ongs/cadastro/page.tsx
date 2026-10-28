import type { Metadata } from "next";

import { RegisterView } from "@/features/ong-register/views/RegisterView";

export const metadata: Metadata = {
  title: "Cadastre sua ONG",
  description: "Faça o pré-cadastro da sua ONG no Focimor, divulgue pets para adoção e receba doações.",
  alternates: { canonical: "/ongs/cadastro" },
};

export default function OngRegisterPage() {
  return <RegisterView />;
}
