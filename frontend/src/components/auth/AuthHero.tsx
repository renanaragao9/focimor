import { HeroBrand } from "@/components/auth/AuthShell";
import { H1, Lead } from "@/components/ui/typography";

interface AuthHeroProps {
  lead?: string;
}

export function AuthHero({ lead = "Dê match, adote um novo amigo e apoie ONGs de proteção animal." }: AuthHeroProps) {
  return (
    <div className="relative flex h-full flex-col justify-between gap-[clamp(1rem,3vh,2.5rem)] overflow-hidden px-12 py-[clamp(1.25rem,5vh,4rem)] xl:px-16">
      <HeroBrand />

      <div className="ml-10 flex max-w-xl flex-col gap-[clamp(0.75rem,2.5vh,1.5rem)] animate-in fade-in slide-in-from-bottom-4 duration-700">
        <H1 className="text-[clamp(2rem,7vh,3.75rem)] leading-[1.08]">
          O par ideal
          <br />
          para o seu pet
          <br />
          <span className="bg-linear-to-r from-brand to-brand-deep bg-clip-text text-transparent">está aqui.</span>
        </H1>
        <Lead className="max-w-md text-lg">{lead}</Lead>
      </div>

      <div aria-hidden="true" />
    </div>
  );
}
