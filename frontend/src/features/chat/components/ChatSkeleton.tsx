import { Skeleton } from "@/components/ui/skeleton";

export function ChatSkeleton() {
  return (
    <div className="flex min-h-dvh flex-col" aria-busy="true">
      <header className="flex items-center justify-between border-b px-6 py-3">
        <Skeleton className="h-7 w-40" />
        <Skeleton className="h-9 w-24" />
      </header>
      <main className="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-3 px-6 py-8">
        <Skeleton className="h-16 w-full" />
        <Skeleton className="h-96 w-full" />
      </main>
    </div>
  );
}
