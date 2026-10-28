import { MAX_MESSAGE_LENGTH, NEAR_BOTTOM_PX, type ChatMessage } from "@/features/chat/types/chatTypes";
import { ApiError } from "@/lib/api/errors";
import { dayKey, dayLabel, parseApiDateTime } from "@/lib/dateTime";

export const PENDING_PREFIX = "pending-";

export function isPending(message: ChatMessage): boolean {
  return message.uuid.startsWith(PENDING_PREFIX);
}

function timestamp(message: ChatMessage): number {
  return parseApiDateTime(message.created_at)?.getTime() ?? 0;
}

export function mergeMessages(current: readonly ChatMessage[], incoming: readonly ChatMessage[]): ChatMessage[] {
  const confirmed = new Map<string, ChatMessage>();
  const order = new Map<string, number>();

  [...current.filter((message) => !isPending(message)), ...incoming].forEach((message) => {
    if (!order.has(message.uuid)) order.set(message.uuid, order.size);
    confirmed.set(message.uuid, { ...confirmed.get(message.uuid), ...message });
  });

  const sorted = [...confirmed.values()].sort((a, b) => timestamp(a) - timestamp(b) || (order.get(a.uuid) ?? 0) - (order.get(b.uuid) ?? 0));

  return [...sorted, ...current.filter(isPending)];
}

export function pendingMessage(id: number, body: string, createdAt: string): ChatMessage {
  return { uuid: `${PENDING_PREFIX}${id}`, body, is_mine: true, read_at: null, created_at: createdAt, pending: true };
}

export function resolvePending(current: readonly ChatMessage[], pendingId: string, sent: ChatMessage): ChatMessage[] {
  return mergeMessages(
    current.filter((message) => message.uuid !== pendingId),
    [sent],
  );
}

export function dropPending(current: readonly ChatMessage[], pendingId: string): ChatMessage[] {
  return current.filter((message) => message.uuid !== pendingId);
}

export interface MessageGroup {
  key: string;
  label: string;
  messages: ChatMessage[];
}

export function groupByDay(messages: readonly ChatMessage[], now: Date, timeZone?: string): MessageGroup[] {
  const groups: MessageGroup[] = [];

  for (const message of messages) {
    const date = parseApiDateTime(message.created_at) ?? now;
    const key = dayKey(date, timeZone);
    const last = groups[groups.length - 1];

    if (last?.key === key) last.messages.push(message);
    else groups.push({ key, label: dayLabel(date, now, timeZone), messages: [message] });
  }

  return groups;
}

export function hasUnreadIncoming(messages: readonly ChatMessage[]): boolean {
  return messages.some((message) => !message.is_mine && message.read_at === null && !isPending(message));
}

export function normalizeBody(body: string): string {
  return body.trim();
}

export function canSend(body: string): boolean {
  const length = normalizeBody(body).length;
  return length > 0 && length <= MAX_MESSAGE_LENGTH;
}

export function remainingCharacters(body: string): number {
  return MAX_MESSAGE_LENGTH - body.length;
}

export function previewOf(message: ChatMessage | null | undefined): string | null {
  if (!message) return null;

  const text = message.body.replace(/\s+/g, " ").trim();
  return message.is_mine ? `Você: ${text}` : text;
}

export function isNearBottom(metrics: { scrollHeight: number; scrollTop: number; clientHeight: number }, threshold = NEAR_BOTTOM_PX): boolean {
  return metrics.scrollHeight - metrics.scrollTop - metrics.clientHeight <= threshold;
}

export function markIncomingRead(messages: readonly ChatMessage[], readAt: string): ChatMessage[] {
  return messages.map((message) => (!message.is_mine && message.read_at === null ? { ...message, read_at: readAt } : message));
}

export function describeSendError(error: unknown): string {
  if (error instanceof ApiError) {
    if (error.status === 429) return "Você está enviando mensagens rápido demais. Aguarde um instante.";
    return error.message;
  }

  return "Não foi possível enviar a mensagem. Tente de novo.";
}
