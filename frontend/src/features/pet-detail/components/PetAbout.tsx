import { Cake, Check, Mars, PawPrint, Palette, Ruler, Venus, X, type LucideIcon } from "lucide-react";

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import type { Pet } from "@/features/pet/types/petTypes";
import { ageLabel, genderLabel } from "@/features/pet/utils/petDisplay";
import { cn } from "@/lib/utils";

interface PetAboutProps {
  pet: Pet;
}

const TONES = {
  brand: "bg-brand-soft text-brand-deep dark:text-brand",
  sun: "bg-sun-soft text-sun",
  sky: "bg-sky-soft text-sky",
  grape: "bg-grape-soft text-grape",
} as const;

function Item({ label, icon: Icon, tone, children }: { label: string; icon: LucideIcon; tone: keyof typeof TONES; children: React.ReactNode }) {
  return (
    <div className="flex items-center gap-3 rounded-sm bg-muted/40 p-3">
      <span className={cn("flex size-10 shrink-0 items-center justify-center rounded-full", TONES[tone])}>
        <Icon className="size-5" aria-hidden="true" />
      </span>
      <div className="flex min-w-0 flex-col">
        <dt className="text-xs font-medium uppercase tracking-wide text-muted-foreground">{label}</dt>
        <dd className="truncate text-sm font-semibold">{children}</dd>
      </div>
    </div>
  );
}

function Flag({ label, on }: { label: string; on: boolean }) {
  const Icon = on ? Check : X;

  return (
    <span className={cn("flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium", on ? "bg-mint-soft text-mint" : "bg-muted text-muted-foreground")}>
      <Icon className="size-4" aria-hidden="true" />
      {label}
    </span>
  );
}

export function PetAbout({ pet }: PetAboutProps) {
  const age = ageLabel(pet.birth_date, new Date());

  return (
    <Card className="border-t-4 border-t-brand">
      <CardHeader>
        <CardTitle className="text-lg">Sobre {pet.name}</CardTitle>
      </CardHeader>
      <CardContent className="flex flex-col gap-5">
        <dl className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <Item label="Sexo" icon={pet.gender === "male" ? Mars : Venus} tone={pet.gender === "male" ? "sky" : "brand"}>
            {genderLabel(pet.gender)}
          </Item>
          <Item label="Nascimento" icon={Cake} tone="sun">
            {pet.birth_date ? `${pet.birth_date}${age ? ` · ${age}` : ""}` : "Não informado"}
          </Item>
          <Item label="Raça" icon={PawPrint} tone="grape">
            {pet.breed?.name ?? "Não informada"}
          </Item>
          <Item label="Cor" icon={Palette} tone="brand">
            {pet.color ?? "Não informada"}
          </Item>
          <Item label="Porte" icon={Ruler} tone="sky">
            {pet.size?.name ?? "Não informado"}
          </Item>
        </dl>
        <div className="flex flex-wrap gap-2">
          <Flag label="Vacinado" on={pet.is_vaccinated} />
          <Flag label="Castrado" on={pet.is_neutered} />
          <Flag label="Ativo" on={pet.is_active} />
        </div>
        {pet.bio && <p className="whitespace-pre-line text-sm text-muted-foreground">{pet.bio}</p>}
      </CardContent>
    </Card>
  );
}
