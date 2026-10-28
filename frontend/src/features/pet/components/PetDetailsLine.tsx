"use client";

import { useIsClient } from "@/hooks/use-is-client";
import { ageLabel } from "@/features/pet/utils/petDisplay";

interface PetDetailsLineProps {
  parts: Array<string | null | undefined>;
  birthDate: string | null;
}

export function PetDetailsLine({ parts, birthDate }: PetDetailsLineProps) {
  const isClient = useIsClient();
  const age = isClient ? ageLabel(birthDate, new Date()) : null;

  return <>{[...parts.slice(0, 1), age, ...parts.slice(1)].filter(Boolean).join(" · ")}</>;
}
