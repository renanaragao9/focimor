import { Skeleton } from "@/components/ui/skeleton";

export function MatchSkeleton() {
  return (
    <div className="flex min-h-dvh flex-col" aria-busy="true">
      <header className="flex items-center justify-between border-b px-6 py-3">
        <Skeleton className="h-7 w-40" />
        <Skeleton className="h-9 w-24" />
      </header>
      <main className="mx-auto flex w-full max-w-md flex-1 flex-col items-center gap-5 px-6 py-8">
        <Skeleton className="h-9 w-full" />
        <Skeleton className="h-[min(66dvh,36rem)] w-full rounded-2xl" />
        <div className="flex gap-5">
          <Skeleton className="size-16 rounded-full" />
          <Skeleton className="size-12 rounded-full" />
          <Skeleton className="size-16 rounded-full" />
        </div>
      </main>
    </div>
  );
}
