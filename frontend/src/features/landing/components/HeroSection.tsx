import { Heart, MapPin, MessageCircleHeart, X } from "lucide-react";
import Link from "next/link";
import { Suspense } from "react";

import { Button } from "@/components/ui/button";
import { PetPhoto } from "@/features/landing/components/PetPhoto";
import { IMAGES, type LandingImage } from "@/features/landing/utils/landingImages";
import { Skeleton } from "@/components/ui/skeleton";
import { getCurrentUser } from "@/lib/auth/getCurrentUser";

async function HeroActions() {
  const user = await getCurrentUser().catch(() => null);

  if (user) {
    return (
      <Button size="lg" asChild>
        <Link href="/boas-vindas">Ir para minha área</Link>
      </Button>
    );
  }

  return (
    <div className="flex flex-col gap-3 sm:flex-row">
      <Button size="lg" asChild>
        <Link href="/cadastro">
          <Heart /> Começar agora
        </Link>
      </Button>
      <Button size="lg" variant="outline" asChild>
        <Link href="/entrar">Já tenho conta</Link>
      </Button>
    </div>
  );
}

function PhotoCard({ className, image, name, info }: { className: string; image: LandingImage; name: string; info: string }) {
  return (
    <div className={`absolute h-72 w-52 overflow-hidden rounded-2xl shadow-xl ring-1 ring-black/5 sm:h-80 sm:w-60 ${className}`} aria-hidden="true">
      <PetPhoto image={image} sizes="240px" priority className="size-full" />
      <div className="absolute inset-x-0 bottom-0 flex flex-col gap-1 bg-linear-to-t from-black/80 to-transparent p-4 pt-12 text-white">
        <p className="text-xl font-bold">{name}</p>
        <p className="flex items-center gap-1 text-xs font-medium">
          <MapPin className="size-3.5" /> {info}
        </p>
      </div>
    </div>
  );
}

export function HeroSection() {
  return (
    <section className="relative overflow-hidden">
      <div className="absolute inset-0 -z-10 bg-gradient-to-br from-brand-soft via-background to-sky-soft" aria-hidden="true" />
      <div className="mx-auto grid w-full max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-24">
        <div className="flex flex-col items-start gap-6">
          <h1 className="text-4xl font-extrabold tracking-tight text-balance sm:text-5xl lg:text-6xl">
            O par ideal para o <span className="text-brand">seu pet</span>
          </h1>
          <p className="max-w-xl text-lg text-muted-foreground">
            No Focimor, tutores encontram amigos e parceiros para os seus pets, famílias encontram um novo companheiro para adotar e ONGs de proteção animal ganham visibilidade para os animais que
            cuidam.
          </p>
          <Suspense fallback={<Skeleton className="h-10 w-72" />}>
            <HeroActions />
          </Suspense>
        </div>

        <div className="relative mx-auto h-[26rem] w-full max-w-sm sm:h-[28rem]" aria-hidden="true">
          <PhotoCard className="left-0 top-10 -rotate-6" image={IMAGES.boxer} name="Thor, 3 anos" info="a 2 km" />
          <PhotoCard className="right-0 top-0 rotate-6" image={IMAGES.terrier} name="Luna, 2 anos" info="a 4 km" />
          <span className="absolute bottom-6 left-1/2 flex -translate-x-1/2 items-center gap-3 rounded-full bg-card px-4 py-3 shadow-xl ring-1 ring-foreground/10">
            <span className="flex size-10 items-center justify-center rounded-full bg-muted text-destructive">
              <X className="size-5" />
            </span>
            <span className="flex size-12 items-center justify-center rounded-full bg-brand text-brand-foreground">
              <Heart className="size-6 fill-current" />
            </span>
            <span className="flex size-10 items-center justify-center rounded-full bg-sky-soft text-sky">
              <MessageCircleHeart className="size-5" />
            </span>
          </span>
        </div>
      </div>
    </section>
  );
}
