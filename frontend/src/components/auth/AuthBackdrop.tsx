import { Bone, Cat, Dog, Heart, PawPrint } from "lucide-react";

interface DotGridProps {
  cols: number;
  rows: number;
  className?: string;
}

function DotGrid({ cols, rows, className }: DotGridProps) {
  return (
    <svg className={className} width={cols * 20} height={rows * 20} aria-hidden="true">
      {Array.from({ length: rows }).flatMap((_, row) => Array.from({ length: cols }).map((__, col) => <circle key={`${row}-${col}`} cx={col * 20 + 4} cy={row * 20 + 4} r="2" fill="currentColor" />))}
    </svg>
  );
}

export function AuthBackdrop() {
  return (
    <div className="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
      <svg className="absolute inset-y-0 right-0 hidden h-full w-[46%] lg:block" viewBox="0 0 460 800" preserveAspectRatio="none">
        <defs>
          <linearGradient id="enter-wave" x1="0" y1="0" x2="0.6" y2="1">
            <stop offset="0%" stopColor="var(--brand)" />
            <stop offset="100%" stopColor="var(--brand)" stopOpacity="0.72" />
          </linearGradient>
        </defs>
        <path d="M 80 0 C 55 150, 0 320, 40 480 C 80 640, 110 730, 70 800 L 460 800 L 460 0 Z" fill="url(#enter-wave)" />
        <path d="M 120 0 C 95 150, 40 320, 80 480 C 120 640, 150 730, 110 800" fill="none" stroke="white" strokeOpacity="0.35" strokeWidth="2" />
      </svg>

      <PawPrint className="absolute -left-16 bottom-10 hidden size-96 -rotate-12 text-foreground opacity-5 lg:block" strokeWidth={1} />
      <PawPrint className="absolute -right-10 top-8 hidden size-56 rotate-12 text-white opacity-20 lg:block" strokeWidth={1.25} />
      <PawPrint className="absolute -bottom-6 right-[18%] hidden size-36 -rotate-6 text-white opacity-20 lg:block" strokeWidth={1.25} />

      <Dog className="absolute left-[40%] top-[14%] hidden size-12 rotate-6 text-brand opacity-10 lg:block" strokeWidth={1.5} />
      <Cat className="absolute left-[24%] top-[12%] hidden size-9 -rotate-12 text-brand opacity-10 lg:block" strokeWidth={1.5} />
      <Heart className="absolute left-[8%] top-[22%] hidden size-10 text-brand opacity-10 lg:block" strokeWidth={1.5} />
      <Bone className="absolute left-[30%] top-[80%] hidden size-12 rotate-3 text-brand opacity-10 lg:block" strokeWidth={1.5} />
      <Heart className="absolute left-[46%] top-[76%] hidden size-9 rotate-12 text-brand opacity-10 lg:block" strokeWidth={1.5} />
      <Cat className="absolute left-[3%] top-[48%] hidden size-8 rotate-12 text-brand opacity-10 lg:block" strokeWidth={1.5} />
      <Dog className="absolute left-[40%] top-[34%] hidden size-8 -rotate-12 text-brand opacity-10 lg:block" strokeWidth={1.5} />
      <PawPrint className="absolute left-[18%] top-[88%] hidden size-8 -rotate-6 text-brand opacity-10 lg:block" strokeWidth={1.5} />
      <Bone className="absolute left-[50%] top-[58%] hidden size-9 -rotate-12 text-brand opacity-10 lg:block" strokeWidth={1.5} />

      <DotGrid cols={5} rows={5} className="absolute left-4 top-4 hidden text-brand opacity-40 lg:block" />
      <DotGrid cols={4} rows={4} className="absolute left-[38%] top-[58%] hidden text-brand opacity-25 lg:block" />
      <DotGrid cols={4} rows={4} className="absolute bottom-24 right-6 hidden text-white opacity-50 lg:block" />
    </div>
  );
}
