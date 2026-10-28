"use client";

import { CalendarClock, ClipboardList, HeartPulse, Plus, Stethoscope } from "lucide-react";
import { useRouter } from "next/navigation";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Pagination } from "@/components/Pagination";
import { RowActions } from "@/components/RowActions";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { DueBadge } from "@/features/pet-detail/components/DueBadge";
import { HealthRecordFormDialog } from "@/features/pet-detail/components/HealthRecordFormDialog";
import { PetDetailInsights } from "@/features/pet-detail/components/PetDetailInsights";
import { RecordCard } from "@/features/pet-detail/components/RecordCard";
import { RecordsEmpty } from "@/features/pet-detail/components/RecordsEmpty";
import { TabHeader } from "@/features/pet-detail/components/TabHeader";
import { healthRecordService } from "@/features/pet-detail/services/petDetailService";
import type { HealthRecord, HealthRecordType } from "@/features/pet-detail/types/petDetailTypes";
import { petDetailHref } from "@/features/pet-detail/utils/petDetailParams";
import { useRecordDialogs } from "@/hooks/use-record-dialogs";
import type { ApiPage } from "@/lib/api/types";
import { formatBrl } from "@/lib/numbers";
import { dueStatus } from "@/features/pet-detail/utils/petDetailDisplay";

interface HealthRecordsTabProps {
  petUuid: string;
  page: ApiPage<HealthRecord>;
  types: HealthRecordType[];
  typeId: string;
}

const ALL = "all";

export function HealthRecordsTab({ petUuid, page, types, typeId }: HealthRecordsTabProps) {
  const router = useRouter();
  const dialogs = useRecordDialogs<HealthRecord>();
  const typeCounts = page.data.reduce((counts, record) => {
    const label = record.type?.name ?? "Outros";
    counts.set(label, (counts.get(label) ?? 0) + 1);
    return counts;
  }, new Map<string, number>());
  const upcoming = page.data.filter((record) => {
    const due = dueStatus(record.next_due_at, new Date());
    return due && due.status !== "overdue";
  }).length;

  return (
    <>
      <TabHeader
        title="Histórico de saúde"
        description="Consultas, exames, cirurgias, medicações e retornos."
        action={
          <Button className="cursor-pointer" onClick={dialogs.openCreate}>
            <Plus /> Novo registro
          </Button>
        }
      />

      <Select value={typeId || ALL} onValueChange={(value) => router.replace(petDetailHref(petUuid, { tab: "saude", typeId: value === ALL ? "" : value }), { scroll: false })}>
        <SelectTrigger aria-label="Filtrar por tipo" className="w-full cursor-pointer sm:w-56">
          <SelectValue placeholder="Tipo" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value={ALL}>Todos os tipos</SelectItem>
          {types.map((type) => (
            <SelectItem key={type.id} value={String(type.id)}>
              {type.name}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>

      {page.data.length === 0 ? (
        <RecordsEmpty
          icon={ClipboardList}
          title={typeId ? "Nenhum registro deste tipo" : "Nenhum registro de saúde"}
          description={typeId ? "Escolha outro tipo ou limpe o filtro." : "Guarde aqui consultas, exames e medicações para ter o histórico completo do seu pet."}
        />
      ) : (
        <>
          <PetDetailInsights
            cards={[
              { label: "Registros", value: String(page.data.length), description: "atendimentos no histórico", icon: HeartPulse, tone: "sky" },
              { label: "Categorias", value: String(typeCounts.size), description: "tipos acompanhados", icon: Stethoscope, tone: "grape" },
              { label: "Retornos", value: String(upcoming), description: "agendados ou próximos", icon: CalendarClock, tone: "sun" },
              { label: "Último exame em", value: page.data[0].performed_at, description: page.data[0].title, icon: ClipboardList, tone: "mint" },
            ]}
            chartTitle="Atendimentos por tipo"
            bars={Array.from(typeCounts, ([label, value]) => ({ label, value }))}
          />
          <div className="grid gap-3 md:grid-cols-2">
            {page.data.map((record) => (
              <RecordCard
                key={record.id}
                icon={Stethoscope}
                tone="sky"
                title={record.title}
                subtitle={`Realizado em ${record.performed_at}`}
                badge={record.type ? <Badge className="border-0 bg-sky-soft text-sky">{record.type.name}</Badge> : null}
                stats={[
                  { label: "Retorno", value: <DueBadge dueDate={record.next_due_at} /> },
                  { label: "Veterinário", value: record.veterinarian ?? "—" },
                  { label: "Custo", value: formatBrl(record.cost) || "—" },
                ]}
                note={record.description}
                actions={<RowActions label={record.title} onEdit={() => dialogs.openEdit(record, record.id)} onDelete={() => dialogs.setItemToDelete(record)} />}
              />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => petDetailHref(petUuid, { tab: "saude", typeId, page: target })} />
        </>
      )}

      <HealthRecordFormDialog
        key={dialogs.formKey}
        petUuid={petUuid}
        record={dialogs.formItem}
        types={types}
        open={dialogs.formOpen}
        onOpenChange={dialogs.setFormOpen}
        onSaved={() => router.refresh()}
      />

      <DeleteDialog
        name={dialogs.itemToDelete?.title ?? null}
        title="Excluir registro"
        warning="O registro será removido do histórico de saúde do pet."
        successMessage="Registro excluído."
        errorMessage="Não foi possível excluir o registro."
        onConfirm={async () => {
          if (dialogs.itemToDelete) await healthRecordService.remove(petUuid, dialogs.itemToDelete.id);
        }}
        onDeleted={() => router.refresh()}
        onOpenChange={(open) => !open && dialogs.setItemToDelete(null)}
      />
    </>
  );
}
