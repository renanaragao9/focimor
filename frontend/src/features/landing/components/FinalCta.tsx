import { Heart } from "lucide-react";
import Link from "next/link";
import { Suspense } from "react";

import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { getCurrentUser } from "@/lib/auth/getCurrentUser";

async function CtaButton() {
  const user = await getCurrentUser().catch(() => null);

  return (
    <Button size="lg" variant="secondary" asChild>
      <Link href={user ? "/boas-vindas" : "/cadastro"}>
        <Heart /> {user ? "Ir para minha área" : "Criar minha conta"}
      </Link>
    </Button>
  );
}

export function FinalCta() {
  return (
    <>
      <section className="px-4 pb-20 sm:px-6">
        <div className="mx-auto flex w-full max-w-6xl flex-col items-center gap-5 rounded-2xl bg-gradient-to-br from-brand to-sky p-10 text-center text-white sm:p-14">
          <h2 className="text-3xl font-extrabold tracking-tight text-balance sm:text-4xl">Pronto para encontrar o par ideal?</h2>
          <p className="max-w-xl text-white/90">Crie sua conta, cadastre seu pet e comece a deslizar. Se quer adotar, também é por aqui.</p>
          <Suspense fallback={<Skeleton className="h-10 w-48" />}>
            <CtaButton />
          </Suspense>
        </div>
      </section>
    </>
  );
}
