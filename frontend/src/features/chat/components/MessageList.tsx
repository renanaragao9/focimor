"use client";

import { Loader2 } from "lucide-react";
import { useEffect, useRef } from "react";

import { Button } from "@/components/ui/button";
import { MessageBubble } from "@/features/chat/components/MessageBubble";
import type { ChatMessage, ChatScope } from "@/features/chat/types/chatTypes";
import { groupByDay, isNearBottom } from "@/features/chat/utils/messages";

interface MessageListProps {
  messages: ChatMessage[];
  petName: string;
  scope: ChatScope;
  hasMore: boolean;
  isLoadingOlder: boolean;
  onLoadOlder: () => Promise<void>;
}

export function MessageList({ messages, petName, scope, hasMore, isLoadingOlder, onLoadOlder }: MessageListProps) {
  const container = useRef<HTMLDivElement>(null);
  const stickToBottom = useRef(true);
  const anchorHeight = useRef<number | null>(null);

  useEffect(() => {
    const element = container.current;
    if (!element) return;

    if (anchorHeight.current !== null) {
      element.scrollTop += element.scrollHeight - anchorHeight.current;
      anchorHeight.current = null;
      return;
    }

    if (stickToBottom.current) element.scrollTop = element.scrollHeight;
  }, [messages]);

  function handleScroll(): void {
    if (container.current) stickToBottom.current = isNearBottom(container.current);
  }

  async function handleLoadOlder(): Promise<void> {
    anchorHeight.current = container.current?.scrollHeight ?? null;
    await onLoadOlder();
  }

  const groups = groupByDay(messages, new Date());

  return (
    <div ref={container} onScroll={handleScroll} className="flex flex-1 flex-col gap-3 overflow-y-auto px-4 py-4" role="log" aria-live="polite" aria-label={`Conversa com ${petName}`}>
      {hasMore && (
        <Button variant="ghost" size="sm" className="mx-auto cursor-pointer" disabled={isLoadingOlder} onClick={() => void handleLoadOlder()}>
          {isLoadingOlder && <Loader2 className="animate-spin" />}
          Carregar mensagens anteriores
        </Button>
      )}

      {messages.length === 0 && (
        <div className="m-auto flex max-w-xs flex-col items-center gap-1 text-center">
          <p className="font-semibold">{scope === "adoptions" ? "Comece a conversa" : `Diga olá para ${petName}!`}</p>
          <p className="text-sm text-muted-foreground">
            {scope === "adoptions" ? `O interesse foi aceito. Combinem os detalhes da visita e da adoção de ${petName}.` : "Vocês deram match. Que tal combinar um passeio ou um encontro?"}
          </p>
        </div>
      )}

      {groups.map((group) => (
        <section key={group.key} className="flex flex-col gap-2" aria-label={group.label}>
          <p className="mx-auto rounded-full bg-muted px-3 py-0.5 text-xs font-medium text-muted-foreground">{group.label}</p>
          {group.messages.map((message) => (
            <MessageBubble key={message.uuid} message={message} />
          ))}
        </section>
      ))}
    </div>
  );
}
