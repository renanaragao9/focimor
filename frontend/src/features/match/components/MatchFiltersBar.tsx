"use client";

import { SlidersHorizontal, X } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { Button } from "@/components/ui/button";
import { Drawer, DrawerContent, DrawerDescription, DrawerHeader, DrawerTitle } from "@/components/ui/drawer";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { DISTANCE_OPTIONS_KM, type MatchFilters } from "@/features/match/types/matchTypes";
import { hasMatchFilters, matchHref } from "@/features/match/utils/matchFilters";
import type { CatalogItem, Pet } from "@/features/pet/types/petTypes";

interface MatchFiltersBarProps {
  filters: MatchFilters;
  pets?: Pet[];
  species: CatalogItem[];
  sizes: CatalogItem[];
  basePath?: "/match" | "/adocao/descobrir";
  anySpeciesLabel?: string;
}

const ANY = "any";

export function MatchFiltersBar({ filters, pets = [], species, sizes, basePath = "/match", anySpeciesLabel = "Mesma espécie" }: MatchFiltersBarProps) {
  const router = useRouter();
  const [mobileOpen, setMobileOpen] = useState(false);

  function apply(next: Partial<MatchFilters>): void {
    setMobileOpen(false);
    router.replace(matchHref({ ...filters, ...next }, basePath), { scroll: false });
  }

  function renderFilterFields(mobile = false) {
    return (
      <>
        {pets.length > 1 && (
          <Select value={filters.petUuid} onValueChange={(value) => apply({ petUuid: value })}>
            <SelectTrigger aria-label="Dar match como" className={mobile ? "w-full cursor-pointer" : "w-44 cursor-pointer"}>
              <SelectValue placeholder="Seu pet" />
            </SelectTrigger>
            <SelectContent>
              {pets.map((pet) => (
                <SelectItem key={pet.uuid} value={pet.uuid}>
                  {pet.name}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        )}

        <Select value={filters.speciesId || ANY} onValueChange={(value) => apply({ speciesId: value === ANY ? "" : value })}>
          <SelectTrigger aria-label="Espécie" className={mobile ? "w-full cursor-pointer" : "w-44 cursor-pointer"}>
            <SelectValue placeholder="Espécie" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value={ANY}>{anySpeciesLabel}</SelectItem>
            {species.map((item) => (
              <SelectItem key={item.id} value={String(item.id)}>
                {item.name}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>

        <Select value={filters.sizeId || ANY} onValueChange={(value) => apply({ sizeId: value === ANY ? "" : value })}>
          <SelectTrigger aria-label="Porte" className={mobile ? "w-full cursor-pointer" : "w-44 cursor-pointer"}>
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

        <Select value={filters.gender || ANY} onValueChange={(value) => apply({ gender: value === ANY ? "" : (value as MatchFilters["gender"]) })}>
          <SelectTrigger aria-label="Sexo" className={mobile ? "w-full cursor-pointer" : "w-44 cursor-pointer"}>
            <SelectValue placeholder="Sexo" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value={ANY}>Qualquer sexo</SelectItem>
            <SelectItem value="male">Machos</SelectItem>
            <SelectItem value="female">Fêmeas</SelectItem>
          </SelectContent>
        </Select>

        <Select value={filters.maxDistanceKm || ANY} onValueChange={(value) => apply({ maxDistanceKm: value === ANY ? "" : value })}>
          <SelectTrigger aria-label="Distância" className={mobile ? "w-full cursor-pointer" : "w-44 cursor-pointer"}>
            <SelectValue placeholder="Distância" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value={ANY}>Qualquer distância</SelectItem>
            {DISTANCE_OPTIONS_KM.map((km) => (
              <SelectItem key={km} value={String(km)}>
                Até {km} km
              </SelectItem>
            ))}
          </SelectContent>
        </Select>

        {hasMatchFilters(filters) && (
          <Button variant="ghost" size="sm" className={`${mobile ? "w-full" : ""} cursor-pointer`} onClick={() => apply({ speciesId: "", sizeId: "", gender: "", maxDistanceKm: "" })}>
            <X /> Limpar
          </Button>
        )}
      </>
    );
  }

  return (
    <>
      <Button variant="outline" className="w-full sm:hidden" onClick={() => setMobileOpen(true)}>
        <SlidersHorizontal /> Filtros
      </Button>

      <div className="hidden flex-wrap items-center justify-center gap-2 sm:flex lg:flex-col lg:items-stretch lg:justify-start">{renderFilterFields()}</div>

      <Drawer open={mobileOpen} onOpenChange={setMobileOpen}>
        <DrawerContent>
          <DrawerHeader>
            <DrawerTitle>Filtros</DrawerTitle>
            <DrawerDescription>Escolha quais pets deseja encontrar.</DrawerDescription>
          </DrawerHeader>
          <div className="flex flex-col gap-3 px-4 pb-6">{renderFilterFields(true)}</div>
        </DrawerContent>
      </Drawer>
    </>
  );
}
