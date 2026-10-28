"use client";

import { ArrowLeft } from "lucide-react";
import { usePathname, useRouter } from "next/navigation";

import { Button } from "@/components/ui/button";

const HIDDEN_ON = ["/", "/boas-vindas"];

export function BackButton() {
  const router = useRouter();
  const pathname = usePathname();

  if (HIDDEN_ON.includes(pathname) || /^\/matches\/[^/]+$/.test(pathname)) return null;

  return (
    <Button type="button" variant="ghost" size="sm" className="-mb-2 w-fit cursor-pointer" onClick={() => (window.history.length > 1 ? router.back() : router.push("/boas-vindas"))}>
      <ArrowLeft /> Voltar
    </Button>
  );
}
