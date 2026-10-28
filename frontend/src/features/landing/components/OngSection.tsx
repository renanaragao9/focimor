import { BadgeCheck, FileText, PawPrint, Users } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";

const POINTS = [
  { icon: PawPrint, text: "Cadastre os pets para adoção, com fotos e informações de saúde." },
  { icon: FileText, text: "Publique novidades, campanhas e pedidos de ajuda, com rascunho e agendamento." },
  { icon: Users, text: "Convide a equipe e defina quem administra e quem apenas colabora." },
  { icon: BadgeCheck, text: "Receba o selo de ONG verificada depois da análise da equipe Focimor." },
];

export function OngSection() {
  return (
    <section id="ongs" className="scroll-mt-20 border-t px-4 py-20 sm:px-6">
      <div className="mx-auto grid w-full max-w-6xl items-center gap-10 lg:grid-cols-2">
        <div className="flex flex-col gap-4">
          <h2 className="text-3xl font-extrabold tracking-tight sm:text-4xl">
            Sua ONG com mais <span className="text-brand">visibilidade</span>
          </h2>
          <p className="text-muted-foreground">Os pets que esperam um lar chegam a quem está procurando um. A gestão fica num painel simples, feito para o dia a dia de quem resgata.</p>
          <p className="text-sm text-muted-foreground">
            Faça o pré-cadastro em um minuto. A equipe Focimor analisa para garantir que só organizações sérias apareçam e, depois de aprovada, os administradores acessam o painel com a própria conta.
          </p>
          <div className="flex flex-col gap-3 sm:flex-row">
            <Button asChild>
              <Link href="/ongs/cadastro">Cadastre sua ONG</Link>
            </Button>
            <Button variant="outline" asChild>
              <Link href="/ongs/cadastro/status">Acompanhar pré-cadastro</Link>
            </Button>
          </div>
        </div>
        <ul className="grid gap-3 sm:grid-cols-2">
          {POINTS.map(({ icon: Icon, text }) => (
            <li key={text} className="flex gap-3 rounded-sm border bg-card p-4">
              <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-mint-soft text-mint">
                <Icon className="size-5" aria-hidden="true" />
              </span>
              <p className="text-sm">{text}</p>
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
}
