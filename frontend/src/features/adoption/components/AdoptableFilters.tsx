"use client";

import { Search, X } from "lucide-react";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";

import { Button } from "@/components/ui/button";
import { InputGroup, InputGroupAddon, InputGroupInput } from "@/components/ui/input-group";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { adoptablePetsHref, hasAdoptableFilters, type AdoptablePetParams } from "@/features/adoption/utils/adoptablePetParams";
import type { CatalogItem } from "@/features/pet/types/petTypes";

interface AdoptableFiltersProps {
  params: AdoptablePetParams;
  species: CatalogItem[];
  sizes: CatalogItem[];
}

const ANY = "any";
const SEARCH_DEBOUNCE_MS = 400;

export function AdoptableFilters({ params, species, sizes }: AdoptableFiltersProps) {
  const router = useRouter();
  const [search, setSearch] = useState(params.search);

  useEffect(() => {
    const term = search.trim();
    if (term === params.search) return;

    const timer = window.setTimeout(() => router.replace(adoptablePetsHref({ ...params, search: term, page: 1 }), { scroll: false }), SEARCH_DEBOUNCE_MS);
    return () => window.clearTimeout(timer);
  }, [search, params, router]);

  function apply(next: Partial<AdoptablePetParams>): void {
    router.replace(adoptablePetsHref({ ...params, ...next, page: 1 }), { scroll: false });
  }

  return (
    <div className="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
      <InputGroup className="h-10 sm:max-w-xs">
        <InputGroupAddon>
          <Search />
        </InputGroupAddon>
        <InputGroupInput type="search" placeholder="Buscar pelo nome" aria-label="Buscar pelo nome" value={search} onChange={(event) => setSearch(event.target.value)} />
      </InputGroup>

      <Select value={params.speciesId || ANY} onValueChange={(value) => apply({ speciesId: value === ANY ? "" : value })}>
        <SelectTrigger aria-label="Espécie" className="w-full cursor-pointer sm:w-40">
          <SelectValue placeholder="Espécie" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value={ANY}>Qualquer espécie</SelectItem>
          {species.map((item) => (
            <SelectItem key={item.id} value={String(item.id)}>
              {item.name}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>

      <Select value={params.sizeId || ANY} onValueChange={(value) => apply({ sizeId: value === ANY ? "" : value })}>
        <SelectTrigger aria-label="Porte" className="w-full cursor-pointer sm:w-40">
          <SelectValue placeholder="Porte" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value={ANY}>Qualquer porte</SelectItem>
          {sizes.map((item) => (
            <SelectItem key={item.id} value={String(item.id)}>
              {item.name}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>

      <Select value={params.gender || ANY} onValueChange={(value) => apply({ gender: value === ANY ? "" : (value as AdoptablePetParams["gender"]) })}>
        <SelectTrigger aria-label="Sexo" className="w-full cursor-pointer sm:w-40">
          <SelectValue placeholder="Sexo" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value={ANY}>Qualquer sexo</SelectItem>
          <SelectItem value="male">Machos</SelectItem>
          <SelectItem value="female">Fêmeas</SelectItem>
        </SelectContent>
      </Select>

      {(hasAdoptableFilters(params) || search) && (
        <Button
          variant="ghost"
          size="sm"
          className="cursor-pointer"
          onClick={() => {
            setSearch("");
            router.replace(adoptablePetsHref(), { scroll: false });
          }}
        >
          <X /> Limpar
        </Button>
      )}
    </div>
  );
}
