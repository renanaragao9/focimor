import { Skeleton } from "@/components/ui/skeleton";

export function MatchesSkeleton() {
  return (
    <div className="flex min-h-dvh flex-col" aria-busy="true">
      <header className="flex items-center justify-between border-b px-6 py-3">
        <Skeleton className="h-7 w-40" />
        <Skeleton className="h-9 w-24" />
      </header>
      <main className="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-3 px-6 py-8">
        <Skeleton className="h-10 w-72" />
        {Array.from({ length: 5 }).map((_, index) => (
          <Skeleton key={index} className="h-24 w-full" />
        ))}
      </main>
    </div>
  );
}
