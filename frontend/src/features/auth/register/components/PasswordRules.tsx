import { Check, Circle } from "lucide-react";

import { passwordRules } from "@/features/auth/register/validators/registerValidators";
import { cn } from "@/lib/utils";

export function PasswordRules({ password }: { password: string }) {
  return (
    <ul className="grid grid-cols-1 gap-1 sm:grid-cols-2" aria-label="Requisitos da senha">
      {passwordRules(password).map((rule) => (
        <li key={rule.key} className={cn("flex items-center gap-1.5 text-xs", rule.met ? "text-foreground" : "text-muted-foreground")}>
          {rule.met ? <Check className="size-3.5 text-brand" aria-label="Atendido" /> : <Circle className="size-3.5" aria-label="Pendente" />}
          {rule.label}
        </li>
      ))}
    </ul>
  );
}
