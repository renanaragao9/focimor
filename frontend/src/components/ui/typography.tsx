import type { ComponentProps } from "react";

import { cn } from "@/lib/utils";

export function H1({ className, ...props }: ComponentProps<"h1">) {
  return <h1 className={cn("text-4xl font-extrabold tracking-tight text-balance", className)} {...props} />;
}

export function H2({ className, ...props }: ComponentProps<"h2">) {
  return <h2 className={cn("text-3xl font-semibold tracking-tight", className)} {...props} />;
}

export function H3({ className, ...props }: ComponentProps<"h3">) {
  return <h3 className={cn("text-2xl font-semibold tracking-tight", className)} {...props} />;
}

export function H4({ className, ...props }: ComponentProps<"h4">) {
  return <h4 className={cn("text-xl font-semibold tracking-tight", className)} {...props} />;
}

export function Lead({ className, ...props }: ComponentProps<"p">) {
  return <p className={cn("text-xl text-muted-foreground", className)} {...props} />;
}

export function P({ className, ...props }: ComponentProps<"p">) {
  return <p className={cn("leading-7", className)} {...props} />;
}

export function Small({ className, ...props }: ComponentProps<"small">) {
  return <small className={cn("text-sm font-medium leading-none", className)} {...props} />;
}

export function Muted({ className, ...props }: ComponentProps<"p">) {
  return <p className={cn("text-sm text-muted-foreground", className)} {...props} />;
}

export function InlineCode({ className, ...props }: ComponentProps<"code">) {
  return (
    <code
      className={cn("relative rounded bg-muted px-[0.3rem] py-[0.2rem] font-mono text-sm font-semibold", className)}
      {...props}
    />
  );
}
