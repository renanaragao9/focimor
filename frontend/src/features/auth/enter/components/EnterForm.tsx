"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";

import { CredentialsForm } from "@/components/auth/CredentialsForm";
import type { ApiError } from "@/lib/api/errors";
import { authService } from "@/lib/auth/authService";

const UNVERIFIED_EMAIL_MESSAGE = "verificar o e-mail antes de fazer login";

export function EnterForm() {
  const router = useRouter();

  function redirectUnverified(error: ApiError, email: string): boolean {
    const unverified = error.message.includes(UNVERIFIED_EMAIL_MESSAGE) || (error.firstError("email") ?? "").includes(UNVERIFIED_EMAIL_MESSAGE);
    if (!unverified) return false;
    router.push(`/verificar-email?email=${encodeURIComponent(email.toLowerCase())}`);
    return true;
  }

  return (
    <CredentialsForm
      onAuthError={redirectUnverified}
      authenticate={authService.enter}
      defaultDestination="/boas-vindas"
      areaPrefix={["/boas-vindas", "/meus-pets", "/match", "/matches", "/ongs", "/posts", "/adocao", "/adocoes", "/minhas-ongs"]}
      footer={
        <p className="text-center text-sm text-muted-foreground">
          Não tem conta?{" "}
          <Link href="/cadastro" className="font-semibold text-primary hover:underline">
            Cadastre-se
          </Link>
        </p>
      }
    />
  );
}
