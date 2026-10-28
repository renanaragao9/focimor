import { Skeleton } from "@/components/ui/skeleton";

export function PetsSkeleton() {
  return (
    <div className="flex min-h-dvh flex-col" aria-busy="true">
      <header className="flex items-center justify-between border-b px-6 py-3">
        <Skeleton className="h-7 w-40" />
        <Skeleton className="h-9 w-24" />
      </header>
      <main className="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 px-6 py-8">
        <Skeleton className="h-10 w-full" />
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: 6 }).map((_, index) => (
            <Skeleton key={index} className="h-72" />
          ))}
        </div>
      </main>
    </div>
  );
}
