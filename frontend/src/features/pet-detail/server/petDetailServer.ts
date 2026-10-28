import "server-only";

import { RECORDS_PER_PAGE, type HealthRecord, type HealthRecordType, type PetWithDetail, type Vaccine, type Weight } from "@/features/pet-detail/types/petDetailTypes";
import { apiServer, apiServerPage } from "@/lib/api/server";

export function fetchPet(uuid: string) {
  return apiServer<PetWithDetail>(`pets/${uuid}`);
}

export function fetchVaccines(uuid: string, page: number) {
  return apiServerPage<Vaccine>(`pets/${uuid}/vaccines`, { query: { page, per_page: RECORDS_PER_PAGE, sort: "applied_at", direction: "desc" } });
}

export function fetchHealthRecords(uuid: string, page: number, typeId: string) {
  return apiServerPage<HealthRecord>(`pets/${uuid}/health-records`, {
    query: { page, per_page: RECORDS_PER_PAGE, sort: "performed_at", direction: "desc", "filters[health_record_type_id]": typeId || undefined },
  });
}

export function fetchWeights(uuid: string, page: number) {
  return apiServerPage<Weight>(`pets/${uuid}/weights`, { query: { page, per_page: RECORDS_PER_PAGE, sort: "measured_at", direction: "desc" } });
}

export function fetchHealthRecordTypes() {
  return apiServer<HealthRecordType[]>("selects/health-record-types");
}
