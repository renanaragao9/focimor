import { SearchX } from "lucide-react";
import Link from "next/link";

import { SiteShell } from "@/components/app/SiteShell";
import { Button } from "@/components/ui/button";

interface NotFoundStateProps {
  title: string;
  text: string;
  href: string;
  label: string;
  secondary?: { href: string; label: string };
}

export function NotFoundState({ title, text, href, label, secondary }: NotFoundStateProps) {
  return (
    <SiteShell>
      <div className="flex flex-1 flex-col items-center justify-center gap-5 py-16 text-center">
        <span className="flex size-20 items-center justify-center rounded-full bg-brand-soft">
          <SearchX className="size-10 text-brand" aria-hidden="true" />
        </span>
        <div className="flex flex-col gap-1">
          <h1 className="text-2xl font-bold">{title}</h1>
          <p className="max-w-md text-muted-foreground">{text}</p>
        </div>
        <div className="flex flex-col gap-3 sm:flex-row">
          <Button asChild>
            <Link href={href}>{label}</Link>
          </Button>
          {secondary && (
            <Button variant="outline" asChild>
              <Link href={secondary.href}>{secondary.label}</Link>
            </Button>
          )}
        </div>
      </div>
    </SiteShell>
  );
}
