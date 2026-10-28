import { CircleCheck, CircleX, Clock, PhoneCall, type LucideIcon } from "lucide-react";

import type { PreRegistrationStatus } from "@/features/ong-register/types/ongRegisterTypes";

export interface StatusInfo {
  label: string;
  description: string;
  icon: LucideIcon;
}

const STATUS_INFO: Record<PreRegistrationStatus, StatusInfo> = {
  pending: { label: "Recebido", description: "Recebemos o seu pré-cadastro e ele está na fila de análise. O prazo é de até 2 dias úteis.", icon: Clock },
  contacted: { label: "Em contato", description: "Nossa equipe já entrou em contato. Confira o seu e-mail e o seu telefone para continuar.", icon: PhoneCall },
  approved: { label: "Aprovado", description: "A sua ONG foi aprovada. Entre com a conta do e-mail do pré-cadastro e acesse o painel em Minha ONG.", icon: CircleCheck },
  rejected: { label: "Não aprovado", description: "Não foi possível aprovar este pré-cadastro. Fale com a equipe para entender o motivo e ver como seguir.", icon: CircleX },
};

const FALLBACK: StatusInfo = { label: "Em análise", description: "O seu pré-cadastro está sendo analisado pela nossa equipe.", icon: Clock };

export function statusInfo(status: string): StatusInfo {
  return STATUS_INFO[status as PreRegistrationStatus] ?? FALLBACK;
}

const DATE_FORMAT = new Intl.DateTimeFormat("pt-BR", { timeZone: "America/Sao_Paulo", day: "2-digit", month: "2-digit", year: "numeric", hour: "2-digit", minute: "2-digit" });

export function formatProgressDate(iso: string | null): string | null {
  if (!iso) return null;
  const date = new Date(iso);

  return Number.isNaN(date.getTime()) ? null : DATE_FORMAT.format(date).replace(",", " às");
}
