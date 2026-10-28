import { Skeleton } from "@/components/ui/skeleton";

export function WelcomeSkeleton() {
  return (
    <div className="flex min-h-dvh flex-col" aria-busy="true">
      <header className="flex items-center justify-between px-6 py-4">
        <Skeleton className="h-7 w-32" />
        <Skeleton className="h-9 w-24" />
      </header>
      <main className="mx-auto flex w-full max-w-4xl flex-1 flex-col items-center gap-10 px-6 py-10">
        <div className="flex flex-col items-center gap-4">
          <Skeleton className="size-24 rounded-full" />
          <Skeleton className="h-10 w-64" />
          <Skeleton className="h-6 w-96 max-w-full" />
        </div>
        <div className="grid w-full gap-4 sm:grid-cols-2">
          {Array.from({ length: 4 }).map((_, index) => (
            <Skeleton key={index} className="h-24" />
          ))}
        </div>
      </main>
    </div>
  );
}
