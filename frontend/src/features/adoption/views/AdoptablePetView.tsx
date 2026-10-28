import { Cake, Check, MapPin, Palette, PawPrint, Ruler, X, type LucideIcon } from "lucide-react";
import Link from "next/link";
import { notFound } from "next/navigation";

import { SiteShell } from "@/components/app/SiteShell";
import { AdoptButton } from "@/features/adoption/components/AdoptButton";
import { PetGallery } from "@/features/adoption/components/PetGallery";
import { fetchAdoptablePet } from "@/features/adoption/server/adoptablePetServer";
import type { FeedPet } from "@/features/match/types/matchTypes";
import { OngLogo } from "@/features/ong/components/OngLogo";
import { locationLabel } from "@/features/ong/utils/ongDisplay";
import { ongHref } from "@/features/ong/utils/ongParams";
import { PetDetailsLine } from "@/features/pet/components/PetDetailsLine";
import { genderLabel, genderTheme, petSubtitle } from "@/features/pet/utils/petDisplay";
import { ApiError } from "@/lib/api/errors";
import { getCurrentUser } from "@/lib/auth/getCurrentUser";
import { cn } from "@/lib/utils";

async function loadPet(uuid: string): Promise<FeedPet> {
  try {
    return await fetchAdoptablePet(uuid);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}

function Fact({ icon: Icon, label, tone, children }: { icon: LucideIcon; label: string; tone: string; children: React.ReactNode }) {
  return (
    <div className="flex items-center gap-3 rounded-sm bg-muted/40 p-3">
      <span className={cn("flex size-10 shrink-0 items-center justify-center rounded-full", tone)}>
        <Icon className="size-5" aria-hidden="true" />
      </span>
      <div className="min-w-0">
        <dt className="text-xs text-muted-foreground">{label}</dt>
        <dd className="truncate text-sm font-semibold">{children}</dd>
      </div>
    </div>
  );
}

export async function AdoptablePetView({ uuid }: { uuid: string }) {
  const [pet, user] = await Promise.all([loadPet(uuid), getCurrentUser().catch(() => null)]);
  const ong = pet.ong;
  const location = ong ? locationLabel(ong.city, ong.state) : null;
  const theme = genderTheme(pet.gender);

  return (
    <SiteShell>
      <div className="grid gap-8 lg:grid-cols-2">
        <PetGallery name={pet.name} photos={pet.photos} accent={theme.border} />

        <div className="flex flex-col gap-5">
          <div className="flex flex-col gap-1">
            <h1 className={cn("bg-linear-to-r bg-clip-text text-4xl font-extrabold tracking-tight text-transparent", theme.gradient)}>{pet.name}</h1>
            <p className="text-muted-foreground">{petSubtitle(pet)}</p>
          </div>

          <dl className="grid gap-3 sm:grid-cols-2">
            <Fact icon={PawPrint} label="Sexo" tone={theme.soft}>
              {genderLabel(pet.gender)}
            </Fact>
            <Fact icon={Cake} label="Idade" tone={theme.soft}>
              <PetDetailsLine parts={[]} birthDate={pet.birth_date} />
            </Fact>
            {pet.size && (
              <Fact icon={Ruler} label="Porte" tone={theme.soft}>
                {pet.size.name}
              </Fact>
            )}
            {pet.color && (
              <Fact icon={Palette} label="Cor" tone={theme.soft}>
                {pet.color}
              </Fact>
            )}
            <Fact icon={pet.is_vaccinated ? Check : X} label="Vacinado" tone={theme.soft}>
              {pet.is_vaccinated ? "Sim" : "Não"}
            </Fact>
            <Fact icon={pet.is_neutered ? Check : X} label="Castrado" tone={theme.soft}>
              {pet.is_neutered ? "Sim" : "Não"}
            </Fact>
          </dl>

          {pet.bio && <p className="whitespace-pre-line leading-7">{pet.bio}</p>}

          <AdoptButton petUuid={pet.uuid} petName={pet.name} isAuthenticated={user !== null} className={theme.solid} />

          {ong && (
            <Link href={ongHref(ong.uuid)} className={cn("flex items-center gap-3 rounded-sm border bg-card p-4 transition-colors", theme.hoverBorder)}>
              <OngLogo name={ong.name} url={null} className="size-12" />
              <div className="flex min-w-0 flex-col">
                <span className="truncate font-semibold">{ong.name}</span>
                {location && (
                  <span className="flex items-center gap-1 text-xs text-muted-foreground">
                    <MapPin className="size-3.5" aria-hidden="true" /> {location}
                  </span>
                )}
                <span className={cn("text-xs", theme.text)}>Conheça a ONG e veja como ajudar</span>
              </div>
            </Link>
          )}
        </div>
      </div>
    </SiteShell>
  );
}
