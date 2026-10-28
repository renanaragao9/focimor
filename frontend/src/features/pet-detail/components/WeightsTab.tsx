"use client";

import { Minus, Plus, Scale, TrendingDown, TrendingUp } from "lucide-react";
import { useRouter } from "next/navigation";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Pagination } from "@/components/Pagination";
import { RowActions } from "@/components/RowActions";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { RecordCard } from "@/features/pet-detail/components/RecordCard";
import { RecordsEmpty } from "@/features/pet-detail/components/RecordsEmpty";
import { PetDetailInsights } from "@/features/pet-detail/components/PetDetailInsights";
import { TabHeader } from "@/features/pet-detail/components/TabHeader";
import { WeightFormDialog } from "@/features/pet-detail/components/WeightFormDialog";
import { weightService } from "@/features/pet-detail/services/petDetailService";
import type { Weight } from "@/features/pet-detail/types/petDetailTypes";
import { petDetailHref } from "@/features/pet-detail/utils/petDetailParams";
import { summarizeMeasures, weightTrend } from "@/features/pet-detail/utils/petDetailDisplay";
import { useRecordDialogs } from "@/hooks/use-record-dialogs";
import type { ApiPage } from "@/lib/api/types";
import { formatDecimal } from "@/lib/numbers";
import { cn } from "@/lib/utils";

interface WeightsTabProps {
  petUuid: string;
  page: ApiPage<Weight>;
}

function TrendCell({ latest, previous }: { latest: Weight; previous: Weight | undefined }) {
  const trend = weightTrend(latest, previous);
  if (!trend) return <span className="text-muted-foreground">—</span>;

  const Icon = trend.kg > 0 ? TrendingUp : trend.kg < 0 ? TrendingDown : Minus;

  return (
    <span className={cn("flex items-center gap-1.5", trend.kg === 0 && "text-muted-foreground")}>
      <Icon className="size-4" aria-hidden="true" /> {trend.label}
    </span>
  );
}

export function WeightsTab({ petUuid, page }: WeightsTabProps) {
  const router = useRouter();
  const dialogs = useRecordDialogs<Weight>();
  const latest = page.meta.current_page === 1 ? page.data[0] : undefined;
  const validWeights = page.data.map((weight) => Number(weight.weight_kg)).filter((weight) => Number.isFinite(weight));
  const average = validWeights.length > 0 ? validWeights.reduce((sum, weight) => sum + weight, 0) / validWeights.length : null;
  const trend = latest ? weightTrend(latest, page.data[1]) : null;

  return (
    <>
      <TabHeader
        title="Pesos e medidas"
        description="Acompanhe a evolução do peso, da altura e da largura."
        action={
          <Button className="cursor-pointer" onClick={dialogs.openCreate}>
            <Plus /> Nova medição
          </Button>
        }
      />

      {latest && (
        <Card className="rounded-sm border-0 bg-linear-to-br from-sun-soft via-brand-soft to-sky-soft">
          <CardContent className="flex flex-wrap items-center justify-between gap-4">
            <div>
              <p className="text-xs font-medium uppercase tracking-wide text-muted-foreground">Último peso</p>
              <p className="text-3xl font-bold">{formatDecimal(latest.weight_kg)} kg</p>
              <p className="text-sm text-muted-foreground">
                em {latest.measured_at}
                {summarizeMeasures(latest) && ` · ${summarizeMeasures(latest)}`}
              </p>
            </div>
            <div className="text-sm">
              <p className="text-xs font-medium uppercase tracking-wide text-muted-foreground">Desde a medição anterior</p>
              <TrendCell latest={latest} previous={page.data[1]} />
            </div>
          </CardContent>
        </Card>
      )}

      {page.data.length === 0 ? (
        <RecordsEmpty icon={Scale} title="Nenhuma medição registrada" description="Registre o peso e as medidas do seu pet para acompanhar o crescimento e a saúde ao longo do tempo." />
      ) : (
        <>
          <PetDetailInsights
            cards={[
              {
                label: "Último peso",
                value: latest ? `${formatDecimal(latest.weight_kg)} kg` : "—",
                description: latest ? `medido em ${latest.measured_at}` : "sem medição",
                icon: Scale,
                tone: "sun",
              },
              { label: "Média", value: average === null ? "—" : `${formatDecimal(average)} kg`, description: "das medições visíveis", icon: Scale, tone: "sky" },
              {
                label: "Variação",
                value: trend?.label ?? "—",
                description: "desde a medição anterior",
                icon: trend && trend.kg < 0 ? TrendingDown : TrendingUp,
                tone: trend && trend.kg < 0 ? "mint" : "grape",
              },
            ]}
            chartTitle="Evolução do peso"
            bars={[...page.data]
              .reverse()
              .slice(-8)
              .map((weight) => ({ label: weight.measured_at.slice(0, 5), value: Number(weight.weight_kg), caption: `${formatDecimal(weight.weight_kg)} kg` }))}
          />
          <div className="grid gap-3 md:grid-cols-2">
            {page.data.map((weight, index) => (
              <RecordCard
                key={weight.id}
                icon={Scale}
                tone="sun"
                title={`${formatDecimal(weight.weight_kg)} kg`}
                subtitle={`Medido em ${weight.measured_at}`}
                stats={[
                  { label: "Altura", value: weight.height_cm ? `${formatDecimal(weight.height_cm)} cm` : "—" },
                  { label: "Largura", value: weight.width_cm ? `${formatDecimal(weight.width_cm)} cm` : "—" },
                  { label: "Variação", value: <TrendCell latest={weight} previous={page.data[index + 1]} /> },
                ]}
                actions={<RowActions label={`medição de ${weight.measured_at}`} onEdit={() => dialogs.openEdit(weight, weight.id)} onDelete={() => dialogs.setItemToDelete(weight)} />}
              />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => petDetailHref(petUuid, { tab: "pesos", page: target })} />
        </>
      )}

      <WeightFormDialog key={dialogs.formKey} petUuid={petUuid} weight={dialogs.formItem} open={dialogs.formOpen} onOpenChange={dialogs.setFormOpen} onSaved={() => router.refresh()} />

      <DeleteDialog
        name={dialogs.itemToDelete ? `${formatDecimal(dialogs.itemToDelete.weight_kg)} kg em ${dialogs.itemToDelete.measured_at}` : null}
        title="Excluir medição"
        warning="A medição será removida do histórico do pet."
        successMessage="Medição excluída."
        errorMessage="Não foi possível excluir a medição."
        onConfirm={async () => {
          if (dialogs.itemToDelete) await weightService.remove(petUuid, dialogs.itemToDelete.id);
        }}
        onDeleted={() => router.refresh()}
        onOpenChange={(open) => !open && dialogs.setItemToDelete(null)}
      />
    </>
  );
}
