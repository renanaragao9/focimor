import { FilePenLine, LayoutDashboard, SearchCheck, type LucideIcon } from "lucide-react";

import { HeroBrand } from "@/components/auth/AuthShell";
import { H1, Lead } from "@/components/ui/typography";

interface Step {
  icon: LucideIcon;
  title: string;
  description: string;
}

const STEPS: Step[] = [
  { icon: FilePenLine, title: "Envie o pré-cadastro", description: "Nome, contato e cidade. Leva menos de um minuto." },
  { icon: SearchCheck, title: "Análise da equipe", description: "Em até 2 dias úteis, entramos em contato por e-mail ou telefone." },
  { icon: LayoutDashboard, title: "Painel liberado", description: "Cadastre pets para adoção, publique novidades e abra caixinhas de arrecadação." },
];

export function RegisterHero() {
  return (
    <div className="relative flex h-full flex-col justify-between gap-[clamp(1rem,3vh,2.5rem)] overflow-hidden px-12 py-[clamp(1.25rem,5vh,4rem)] xl:px-16">
      <HeroBrand />

      <div className="ml-10 flex max-w-xl flex-col gap-[clamp(0.75rem,2.5vh,1.5rem)] animate-in fade-in slide-in-from-bottom-4 duration-700">
        <H1 className="text-[clamp(2rem,7vh,3.75rem)] leading-[1.08]">
          Dê voz
          <br />
          aos animais
          <br />
          <span className="bg-linear-to-r from-brand to-sky bg-clip-text text-transparent">que você cuida.</span>
        </H1>
        <Lead className="max-w-md text-lg">
          Tem uma ONG ou abrigo? Cadastre-se para divulgar os pets para adoção, receber doações e mostrar o seu trabalho. Informe só o básico e nossa equipe entra em contato para liberar o seu painel.
        </Lead>

        <ol className="mt-4 flex flex-col [@media(max-height:640px)]:hidden">
          {STEPS.map(({ icon: Icon, title, description }, index) => (
            <li key={title} className="flex items-stretch gap-4">
              <div className="flex flex-col items-center">
                <span className="flex size-10 shrink-0 items-center justify-center rounded-sm border bg-card shadow-sm">
                  <Icon className="size-5 text-brand" aria-hidden="true" />
                </span>
                {index < STEPS.length - 1 && <span className="my-1 w-px flex-1 bg-border" aria-hidden="true" />}
              </div>
              <div className={index < STEPS.length - 1 ? "pb-4" : ""}>
                <p className="text-sm font-bold">
                  {index + 1}. {title}
                </p>
                <p className="mt-0.5 max-w-sm text-xs/relaxed text-muted-foreground">{description}</p>
              </div>
            </li>
          ))}
        </ol>
      </div>

      <div aria-hidden="true" />
    </div>
  );
}
