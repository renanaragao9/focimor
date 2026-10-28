import type { Adoption, AdoptionStatus } from "@/features/adoption/types/adoptionTypes";

export const STATUS_LABELS: Record<AdoptionStatus, string> = {
  pending: "Pendente",
  accepted: "Aceito",
  declined: "Recusado",
  adopted: "Adotado",
  cancelled: "Cancelado",
};

export const STATUS_CLASSES: Record<AdoptionStatus, string> = {
  pending: "bg-sun-soft text-sun",
  accepted: "bg-mint-soft text-mint",
  adopted: "bg-brand-soft text-brand-deep dark:text-brand",
  declined: "bg-muted text-muted-foreground",
  cancelled: "bg-muted text-muted-foreground",
};

export type AdoptionAction = "accept" | "decline" | "adopt" | "cancel";

export function availableActions(adoption: Pick<Adoption, "role" | "status">): AdoptionAction[] {
  const { role, status } = adoption;
  if (status === "pending") return role === "owner" ? ["accept", "decline"] : ["cancel"];
  if (status === "accepted") return role === "owner" ? ["adopt", "decline"] : ["cancel"];
  return [];
}

export type ChatAccess = "open" | "read-only" | "closed";

export function chatAccess(status: AdoptionStatus): ChatAccess {
  if (status === "accepted") return "open";
  if (status === "adopted") return "read-only";
  return "closed";
}

export function counterpartLabel(adoption: Pick<Adoption, "role" | "pet" | "adopter">): string {
  if (adoption.role === "owner") return adoption.adopter ? `${adoption.adopter.name} quer adotar` : "Interessado";
  return adoption.pet.ong?.name ?? "Tutor do pet";
}
