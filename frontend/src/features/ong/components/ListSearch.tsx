"use client";

import { Search } from "lucide-react";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";

import { InputGroup, InputGroupAddon, InputGroupInput } from "@/components/ui/input-group";
import { listHref } from "@/features/ong/utils/ongParams";

interface ListSearchProps {
  basePath: "/ongs" | "/posts";
  value: string;
  placeholder: string;
}

const SEARCH_DEBOUNCE_MS = 400;

export function ListSearch({ basePath, value, placeholder }: ListSearchProps) {
  const router = useRouter();
  const [search, setSearch] = useState(value);

  useEffect(() => {
    const term = search.trim();
    if (term === value) return;

    const timer = window.setTimeout(() => router.replace(listHref(basePath, { search: term }), { scroll: false }), SEARCH_DEBOUNCE_MS);
    return () => window.clearTimeout(timer);
  }, [search, value, basePath, router]);

  return (
    <InputGroup className="h-10 sm:max-w-sm">
      <InputGroupAddon>
        <Search />
      </InputGroupAddon>
      <InputGroupInput type="search" placeholder={placeholder} aria-label={placeholder} value={search} onChange={(event) => setSearch(event.target.value)} />
    </InputGroup>
  );
}
