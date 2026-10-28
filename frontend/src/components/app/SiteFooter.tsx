import { PawPrint } from "lucide-react";

export function SiteFooter() {
  return (
    <footer className="relative border-t bg-white px-4 py-8 sm:px-6 dark:bg-black">
      <div className="mx-auto flex w-full max-w-6xl flex-col items-center justify-between gap-3 text-sm text-muted-foreground sm:flex-row">
        <span className="flex items-center gap-2 font-semibold text-foreground">
          <PawPrint className="size-5 text-brand" aria-hidden="true" /> Focimor
        </span>
        <p>Focinho + amor: conectando pets, famílias e ONGs.</p>
      </div>
      <p className="mx-auto mt-6 max-w-6xl border-t pt-4 text-center text-xs text-muted-foreground">
        © Focimor · Desenvolvido por <span className="font-semibold text-foreground">AragaoLabs</span>
      </p>
    </footer>
  );
}
