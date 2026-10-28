"use client";

import { useCallback, type ReactNode } from "react";
import { toast } from "sonner";

import { Composer } from "@/features/chat/components/Composer";
import { MessageList } from "@/features/chat/components/MessageList";
import { useChat } from "@/features/chat/hooks/useChat";
import type { ChatMessage, ChatScope } from "@/features/chat/types/chatTypes";
import { useIsClient } from "@/hooks/use-is-client";
import { cn } from "@/lib/utils";

interface ChatRoomProps {
  scope: ChatScope;
  uuid: string;
  petName: string;
  header: ReactNode;
  initial: ChatMessage[];
  initialHasMore: boolean;
  canSend?: boolean;
  className?: string;
}

export function ChatRoom({ scope, uuid, petName, header, initial, initialHasMore, canSend = true, className = "h-[calc(100dvh-11rem)] min-h-96" }: ChatRoomProps) {
  const isClient = useIsClient();
  const onError = useCallback((message: string) => toast.error(message), []);
  const { messages, hasMore, isLoadingOlder, send, loadOlder } = useChat({ scope, matchUuid: uuid, initial, initialHasMore, onError });

  return (
    <div className={cn("flex flex-col overflow-hidden rounded-sm border bg-card", className)}>
      {header}
      {isClient ? (
        <MessageList messages={messages} petName={petName} scope={scope} hasMore={hasMore} isLoadingOlder={isLoadingOlder} onLoadOlder={loadOlder} />
      ) : (
        <div className="flex-1" aria-busy="true" />
      )}
      {canSend ? <Composer onSend={send} /> : <p className="border-t px-4 py-3 text-center text-sm text-muted-foreground">Esta conversa foi encerrada. Só é possível ler as mensagens.</p>}
    </div>
  );
}
