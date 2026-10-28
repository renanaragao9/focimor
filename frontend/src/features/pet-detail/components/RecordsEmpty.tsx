import type { LucideIcon } from "lucide-react";

interface RecordsEmptyProps {
  icon: LucideIcon;
  title: string;
  description: string;
}

export function RecordsEmpty({ icon: Icon, title, description }: RecordsEmptyProps) {
  return (
    <div className="flex flex-col items-center gap-3 rounded-sm border border-dashed px-6 py-12 text-center">
      <span className="flex size-14 items-center justify-center rounded-full bg-brand-soft">
        <Icon className="size-7 text-brand" aria-hidden="true" />
      </span>
      <div className="flex flex-col gap-1">
        <h3 className="text-lg font-semibold">{title}</h3>
        <p className="max-w-md text-sm text-muted-foreground">{description}</p>
      </div>
    </div>
  );
}
