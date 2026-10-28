"use client";

import { CircleCheck } from "lucide-react";
import Link from "next/link";
import { useState } from "react";

import { Button } from "@/components/ui/button";
import { RegisterForm } from "@/features/ong-register/components/RegisterForm";

export function RegisterPanel() {
  const [registeredEmail, setRegisteredEmail] = useState<string | null>(null);

  if (registeredEmail === null) {
    return (
      <RegisterForm
        onRegistered={(email) => {
          setRegisteredEmail(email);
          window.scrollTo({ top: 0, behavior: "smooth" });
        }}
      />
    );
  }

  return (
    <div className="flex flex-col items-center gap-5 py-4 text-center" role="status">
      <span className="flex size-16 items-center justify-center rounded-full bg-brand-soft">
        <CircleCheck className="size-9 text-brand" aria-hidden="true" />
      </span>
      <div className="flex flex-col gap-2">
        <h2 className="text-2xl font-bold">Pré-cadastro enviado</h2>
        <p className="text-muted-foreground">Entraremos em contato em breve para liberar o painel da sua ONG.</p>
        <p className="text-muted-foreground">
          Quando for aprovada, entre com a conta do e-mail <strong className="text-foreground">{registeredEmail}</strong>. Se ainda não tem conta, crie uma com esse mesmo e-mail.
        </p>
      </div>
      <div className="flex w-full flex-col gap-3 sm:flex-row sm:justify-center">
        <Button size="lg" className="h-12 rounded-sm font-semibold" asChild>
          <Link href="/ongs/cadastro/status">Acompanhar pré-cadastro</Link>
        </Button>
        <Button size="lg" variant="outline" className="h-12 rounded-sm font-semibold" asChild>
          <Link href="/cadastro">Criar minha conta</Link>
        </Button>
      </div>
    </div>
  );
}
