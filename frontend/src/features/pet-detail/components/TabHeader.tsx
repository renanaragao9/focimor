import type { ReactNode } from "react";

interface TabHeaderProps {
  title: string;
  description: string;
  action?: ReactNode;
}

export function TabHeader({ title, description, action }: TabHeaderProps) {
  return (
    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 className="text-xl font-semibold">{title}</h2>
        <p className="text-sm text-muted-foreground">{description}</p>
      </div>
      {action}
    </div>
  );
}
