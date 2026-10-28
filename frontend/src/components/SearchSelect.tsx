"use client";

import { Check, ChevronsUpDown, Loader2, Search } from "lucide-react";
import { useEffect, useState } from "react";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { cn } from "@/lib/utils";

export interface SearchSelectOption {
  value: string;
  label: string;
}

interface SearchSelectProps {
  id?: string;
  value: string;
  selectedLabel: string;
  placeholder: string;
  searchPlaceholder?: string;
  emptyLabel?: string;
  clearLabel?: string;
  disabled?: boolean;
  invalid?: boolean;
  search: (query: string) => Promise<SearchSelectOption[]>;
  onChange: (option: SearchSelectOption | null) => void;
}

const DEBOUNCE_MS = 300;

interface Loaded {
  query: string;
  options: SearchSelectOption[];
}

export function SearchSelect({
  id,
  value,
  selectedLabel,
  placeholder,
  searchPlaceholder = "Buscar...",
  emptyLabel = "Nada encontrado.",
  clearLabel,
  disabled,
  invalid,
  search,
  onChange,
}: SearchSelectProps) {
  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState("");
  const [debounced, setDebounced] = useState("");
  const [loaded, setLoaded] = useState<Loaded | null>(null);
  const [failed, setFailed] = useState(false);

  useEffect(() => {
    const timer = window.setTimeout(() => setDebounced(query.trim()), DEBOUNCE_MS);
    return () => window.clearTimeout(timer);
  }, [query]);

  useEffect(() => {
    if (!open) return;

    let cancelled = false;
    search(debounced)
      .then((options) => {
        if (cancelled) return;
        setFailed(false);
        setLoaded({ query: debounced, options });
      })
      .catch(() => {
        if (cancelled) return;
        setFailed(true);
        setLoaded({ query: debounced, options: [] });
      });

    return () => {
      cancelled = true;
    };
  }, [open, debounced, search]);

  const isLoading = loaded === null || loaded.query !== debounced;
  const options = loaded?.options ?? [];

  function handleOpenChange(next: boolean): void {
    setOpen(next);
    if (!next) {
      setQuery("");
      setDebounced("");
    }
  }

  function select(option: SearchSelectOption | null): void {
    onChange(option);
    handleOpenChange(false);
  }

  return (
    <Popover open={open} onOpenChange={handleOpenChange}>
      <PopoverTrigger asChild>
        <Button
          id={id}
          type="button"
          variant="outline"
          role="combobox"
          aria-expanded={open}
          aria-invalid={invalid}
          disabled={disabled}
          className={cn("w-full cursor-pointer justify-between font-normal", !value && "text-muted-foreground")}
        >
          <span className="truncate">{value ? selectedLabel : placeholder}</span>
          <ChevronsUpDown className="size-4 shrink-0 opacity-50" aria-hidden="true" />
        </Button>
      </PopoverTrigger>
      <PopoverContent align="start" className="w-(--radix-popover-trigger-width) gap-2 p-2">
        <div className="relative">
          <Search className="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" aria-hidden="true" />
          <Input autoFocus value={query} onChange={(event) => setQuery(event.target.value)} placeholder={searchPlaceholder} aria-label={searchPlaceholder} className="pl-8" />
          {isLoading && <Loader2 className="absolute right-2.5 top-1/2 size-4 -translate-y-1/2 animate-spin text-muted-foreground" aria-label="Buscando" />}
        </div>

        <ul role="listbox" className="flex max-h-60 flex-col gap-0.5 overflow-y-auto">
          {clearLabel && (
            <li>
              <button
                type="button"
                role="option"
                aria-selected={!value}
                onClick={() => select(null)}
                className="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-left text-sm text-muted-foreground hover:bg-muted"
              >
                {clearLabel}
              </button>
            </li>
          )}
          {options.map((option) => (
            <li key={option.value}>
              <button
                type="button"
                role="option"
                aria-selected={option.value === value}
                onClick={() => select(option)}
                className="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-left text-sm hover:bg-muted"
              >
                <Check className={cn("size-4 shrink-0 text-brand", option.value === value ? "opacity-100" : "opacity-0")} aria-hidden="true" />
                <span className="truncate">{option.label}</span>
              </button>
            </li>
          ))}
          {!isLoading && options.length === 0 && <li className="px-2 py-3 text-center text-sm text-muted-foreground">{failed ? "Não foi possível buscar. Tente de novo." : emptyLabel}</li>}
        </ul>
      </PopoverContent>
    </Popover>
  );
}
