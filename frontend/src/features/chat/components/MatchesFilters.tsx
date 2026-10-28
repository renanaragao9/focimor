"use client";

import { useRouter } from "next/navigation";

import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import type { PetOption } from "@/features/chat/types/chatTypes";
import { matchesHref } from "@/features/chat/utils/matchesParams";

interface MatchesFiltersProps {
  petUuid: string;
  pets: PetOption[];
}

const ALL = "all";

export function MatchesFilters({ petUuid, pets }: MatchesFiltersProps) {
  const router = useRouter();

  return (
    <Select value={petUuid || ALL} onValueChange={(value) => router.replace(matchesHref({ petUuid: value === ALL ? "" : value }), { scroll: false })}>
      <SelectTrigger aria-label="Filtrar por pet" className="w-full cursor-pointer sm:w-60">
        <SelectValue placeholder="Todos os pets" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value={ALL}>Todos os meus pets</SelectItem>
        {pets.map((pet) => (
          <SelectItem key={pet.uuid} value={pet.uuid}>
            Matches de {pet.name}
          </SelectItem>
        ))}
      </SelectContent>
    </Select>
  );
}
