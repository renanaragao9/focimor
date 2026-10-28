"use client";

import { CircleAlert, Plus, ShieldCheck, Syringe } from "lucide-react";
import { useRouter } from "next/navigation";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Pagination } from "@/components/Pagination";
import { RowActions } from "@/components/RowActions";
import { Button } from "@/components/ui/button";
import { DueBadge } from "@/features/pet-detail/components/DueBadge";
import { PetDetailInsights } from "@/features/pet-detail/components/PetDetailInsights";
import { RecordCard } from "@/features/pet-detail/components/RecordCard";
import { RecordsEmpty } from "@/features/pet-detail/components/RecordsEmpty";
import { TabHeader } from "@/features/pet-detail/components/TabHeader";
import { VaccineFormDialog } from "@/features/pet-detail/components/VaccineFormDialog";
import { vaccineService } from "@/features/pet-detail/services/petDetailService";
import type { Vaccine } from "@/features/pet-detail/types/petDetailTypes";
import { petDetailHref } from "@/features/pet-detail/utils/petDetailParams";
import { dueStatus } from "@/features/pet-detail/utils/petDetailDisplay";
import { useRecordDialogs } from "@/hooks/use-record-dialogs";
import type { ApiPage } from "@/lib/api/types";

interface VaccinesTabProps {
  petUuid: string;
  page: ApiPage<Vaccine>;
}

export function VaccinesTab({ petUuid, page }: VaccinesTabProps) {
  const router = useRouter();
  const dialogs = useRecordDialogs<Vaccine>();
  const vaccineStatus = page.data.reduce(
    (summary, vaccine) => {
      const due = dueStatus(vaccine.next_dose_at, new Date());
      if (!due) summary.withoutDate += 1;
      else if (due.status === "overdue") summary.overdue += 1;
      else if (due.status === "soon") summary.soon += 1;
      else summary.ok += 1;
      return summary;
    },
    { ok: 0, soon: 0, overdue: 0, withoutDate: 0 },
  );

  return (
    <>
      <TabHeader
        title="Vacinas"
        description="Histórico de vacinação e próximas doses."
        action={
          <Button className="cursor-pointer" onClick={dialogs.openCreate}>
            <Plus /> Nova vacina
          </Button>
        }
      />

      {page.data.length === 0 ? (
        <RecordsEmpty icon={Syringe} title="Nenhuma vacina registrada" description="Registre as vacinas para acompanhar as próximas doses e manter a carteirinha em dia." />
      ) : (
        <>
          <PetDetailInsights
            cards={[
              { label: "Vacinas", value: String(page.data.length), description: "registros no histórico", icon: Syringe, tone: "mint" },
              { label: "Em dia", value: String(vaccineStatus.ok), description: "doses sem urgência", icon: ShieldCheck, tone: "sky" },
              { label: "Atenção", value: String(vaccineStatus.overdue + vaccineStatus.soon), description: "doses próximas ou atrasadas", icon: CircleAlert, tone: "sun" },
            ]}
            chartTitle="Situação das doses"
            bars={[
              { label: "Em dia", value: vaccineStatus.ok },
              { label: "Próximas", value: vaccineStatus.soon },
              { label: "Atrasadas", value: vaccineStatus.overdue },
              { label: "Sem data", value: vaccineStatus.withoutDate },
            ]}
          />
          <div className="grid gap-3 md:grid-cols-2">
            {page.data.map((vaccine) => (
              <RecordCard
                key={vaccine.id}
                icon={Syringe}
                tone="mint"
                title={vaccine.name}
                subtitle={`Aplicada em ${vaccine.applied_at}`}
                stats={[
                  { label: "Próxima dose", value: <DueBadge dueDate={vaccine.next_dose_at} /> },
                  { label: "Lote", value: vaccine.batch ?? "—" },
                  { label: "Veterinário", value: vaccine.veterinarian ?? "—" },
                ]}
                note={vaccine.notes}
                actions={<RowActions label={vaccine.name} onEdit={() => dialogs.openEdit(vaccine, vaccine.id)} onDelete={() => dialogs.setItemToDelete(vaccine)} />}
              />
            ))}
          </div>
          <Pagination meta={page.meta} buildHref={(target) => petDetailHref(petUuid, { tab: "vacinas", page: target })} />
        </>
      )}

      <VaccineFormDialog key={dialogs.formKey} petUuid={petUuid} vaccine={dialogs.formItem} open={dialogs.formOpen} onOpenChange={dialogs.setFormOpen} onSaved={() => router.refresh()} />

      <DeleteDialog
        name={dialogs.itemToDelete?.name ?? null}
        title="Excluir vacina"
        warning="O registro desta vacina será removido do histórico do pet."
        successMessage="Vacina excluída."
        errorMessage="Não foi possível excluir a vacina."
        onConfirm={async () => {
          if (dialogs.itemToDelete) await vaccineService.remove(petUuid, dialogs.itemToDelete.id);
        }}
        onDeleted={() => router.refresh()}
        onOpenChange={(open) => !open && dialogs.setItemToDelete(null)}
      />
    </>
  );
}
