import { describe, expect, it } from "vitest";

import type { ChatMessage } from "@/features/chat/types/chatTypes";
import {
  canSend,
  describeSendError,
  dropPending,
  groupByDay,
  hasUnreadIncoming,
  isNearBottom,
  markIncomingRead,
  mergeMessages,
  pendingMessage,
  previewOf,
  remainingCharacters,
  resolvePending,
} from "@/features/chat/utils/messages";
import { ApiError } from "@/lib/api/errors";

function message(uuid: string, createdAt: string, extra: Partial<ChatMessage> = {}): ChatMessage {
  return { uuid, body: uuid, is_mine: false, read_at: null, created_at: createdAt, ...extra };
}

describe("mergeMessages", () => {
  it("ordena do mais antigo ao mais novo e remove duplicadas", () => {
    const merged = mergeMessages([message("b", "01/01/2026 10:01:00")], [message("c", "01/01/2026 10:02:00"), message("a", "01/01/2026 10:00:00"), message("b", "01/01/2026 10:01:00")]);

    expect(merged.map((item) => item.uuid)).toEqual(["a", "b", "c"]);
  });

  it("atualiza campos de uma mensagem já existente", () => {
    const merged = mergeMessages([message("a", "01/01/2026 10:00:00", { is_mine: true })], [message("a", "01/01/2026 10:00:00", { is_mine: true, read_at: "01/01/2026 10:05:00" })]);

    expect(merged).toHaveLength(1);
    expect(merged[0].read_at).toBe("01/01/2026 10:05:00");
  });

  it("mantém as pendentes no fim", () => {
    const pending = pendingMessage(1, "oi", "01/01/2026 09:00:00");
    const merged = mergeMessages([pending], [message("a", "01/01/2026 10:00:00")]);

    expect(merged.map((item) => item.uuid)).toEqual(["a", pending.uuid]);
  });
});

describe("mensagens pendentes", () => {
  it("troca a pendente pela confirmada sem duplicar", () => {
    const pending = pendingMessage(1, "oi", "01/01/2026 10:00:00");
    const sent = message("real", "01/01/2026 10:00:01", { is_mine: true, body: "oi" });

    expect(resolvePending([pending], pending.uuid, sent).map((item) => item.uuid)).toEqual(["real"]);
  });

  it("descarta a pendente que falhou", () => {
    const pending = pendingMessage(1, "oi", "01/01/2026 10:00:00");

    expect(dropPending([pending], pending.uuid)).toEqual([]);
  });
});

describe("groupByDay", () => {
  it("agrupa por dia", () => {
    const now = new Date("2026-03-05T12:00:00Z");
    const groups = groupByDay([message("a", "04/03/2026 10:00:00"), message("b", "04/03/2026 11:00:00"), message("c", "05/03/2026 09:00:00")], now, "UTC");

    expect(groups.map((group) => [group.label, group.messages.length])).toEqual([
      ["Ontem", 2],
      ["Hoje", 1],
    ]);
  });
});

describe("leitura", () => {
  it("detecta mensagens recebidas não lidas", () => {
    expect(hasUnreadIncoming([message("a", "01/01/2026 10:00:00")])).toBe(true);
    expect(hasUnreadIncoming([message("a", "01/01/2026 10:00:00", { is_mine: true })])).toBe(false);
    expect(hasUnreadIncoming([message("a", "01/01/2026 10:00:00", { read_at: "01/01/2026 10:01:00" })])).toBe(false);
  });

  it("marca só as recebidas como lidas", () => {
    const result = markIncomingRead([message("a", "01/01/2026 10:00:00"), message("b", "01/01/2026 10:00:00", { is_mine: true })], "01/01/2026 11:00:00");

    expect(result[0].read_at).toBe("01/01/2026 11:00:00");
    expect(result[1].read_at).toBeNull();
  });
});

describe("envio", () => {
  it("valida o tamanho do texto", () => {
    expect(canSend("   ")).toBe(false);
    expect(canSend("oi")).toBe(true);
    expect(canSend("a".repeat(2000))).toBe(true);
    expect(canSend("a".repeat(2001))).toBe(false);
    expect(remainingCharacters("abc")).toBe(1997);
  });

  it("explica o limite de envios", () => {
    expect(describeSendError(new ApiError("x", 429))).toContain("rápido demais");
    expect(describeSendError(new ApiError("Falhou", 500))).toBe("Falhou");
    expect(describeSendError(new Error("x"))).toContain("Não foi possível");
  });
});

describe("pré-visualização e rolagem", () => {
  it("prefixa as minhas mensagens com Você", () => {
    expect(previewOf(message("a", "01/01/2026 10:00:00", { body: "oi\n  tudo", is_mine: true }))).toBe("Você: oi tudo");
    expect(previewOf(message("a", "01/01/2026 10:00:00", { body: "oi" }))).toBe("oi");
    expect(previewOf(null)).toBeNull();
  });

  it("detecta se está perto do fim", () => {
    expect(isNearBottom({ scrollHeight: 1000, scrollTop: 450, clientHeight: 500 })).toBe(true);
    expect(isNearBottom({ scrollHeight: 1000, scrollTop: 100, clientHeight: 500 })).toBe(false);
  });
});
