"use client";

import { Plus, Search, X } from "lucide-react";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";

import { Button } from "@/components/ui/button";
import { InputGroup, InputGroupAddon, InputGroupInput } from "@/components/ui/input-group";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import type { CatalogItem, PetFilters, PetStatusFilter } from "@/features/pet/types/petTypes";
import { hasActiveFilters, petsHref } from "@/features/pet/utils/petFilters";

interface PetsToolbarProps {
  filters: PetFilters;
  species: CatalogItem[];
  onCreate: () => void;
}

const ALL = "all";
const SEARCH_DEBOUNCE_MS = 400;

export function PetsToolbar({ filters, species, onCreate }: PetsToolbarProps) {
  const router = useRouter();
  const [search, setSearch] = useState(filters.search);

  useEffect(() => {
    const term = search.trim();
    if (term === filters.search) return;

    const timer = window.setTimeout(() => router.replace(petsHref({ ...filters, search: term, page: 1 }), { scroll: false }), SEARCH_DEBOUNCE_MS);
    return () => window.clearTimeout(timer);
  }, [search, filters, router]);

  function applyFilters(next: Partial<PetFilters>): void {
    router.replace(petsHref({ ...filters, ...next, page: 1 }), { scroll: false });
  }

  function clearFilters(): void {
    setSearch("");
    router.replace(petsHref({ search: "", speciesId: "", status: "", page: 1 }), { scroll: false });
  }

  return (
    <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
      <InputGroup className="h-10 sm:max-w-xs">
        <InputGroupAddon>
          <Search />
        </InputGroupAddon>
        <InputGroupInput type="search" placeholder="Buscar pelo nome" aria-label="Buscar pelo nome" value={search} onChange={(event) => setSearch(event.target.value)} />
      </InputGroup>

      <Select value={filters.speciesId || ALL} onValueChange={(value) => applyFilters({ speciesId: value === ALL ? "" : value })}>
        <SelectTrigger aria-label="Filtrar por espécie" className="w-full cursor-pointer sm:w-44">
          <SelectValue placeholder="Espécie" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value={ALL}>Todas as espécies</SelectItem>
          {species.map((item) => (
            <SelectItem key={item.id} value={String(item.id)}>
              {item.name}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>

      <Select value={filters.status || ALL} onValueChange={(value) => applyFilters({ status: (value === ALL ? "" : value) as PetStatusFilter })}>
        <SelectTrigger aria-label="Filtrar por situação" className="w-full cursor-pointer sm:w-40">
          <SelectValue placeholder="Situação" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value={ALL}>Todos</SelectItem>
          <SelectItem value="active">Ativos</SelectItem>
          <SelectItem value="inactive">Inativos</SelectItem>
        </SelectContent>
      </Select>

      {(hasActiveFilters(filters) || search) && (
        <Button variant="ghost" size="sm" className="cursor-pointer" onClick={clearFilters}>
          <X /> Limpar
        </Button>
      )}

      <Button className="cursor-pointer sm:ml-auto" onClick={onCreate}>
        <Plus /> Novo pet
      </Button>
    </div>
  );
}
