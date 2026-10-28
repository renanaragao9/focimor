"use client";

import { useCallback, useMemo, useEffect, useRef, useState } from "react";

import { chatServiceFor } from "@/features/chat/services/chatService";
import { POLL_INTERVAL_MS, type ChatMessage, type ChatScope } from "@/features/chat/types/chatTypes";
import { describeSendError, dropPending, hasUnreadIncoming, markIncomingRead, mergeMessages, normalizeBody, pendingMessage, resolvePending } from "@/features/chat/utils/messages";
import { toApiDateTime } from "@/lib/dateTime";

interface UseChatOptions {
  scope: ChatScope;
  matchUuid: string;
  initial: ChatMessage[];
  initialHasMore: boolean;
  onError: (message: string) => void;
}

export function useChat({ scope, matchUuid, initial, initialHasMore, onError }: UseChatOptions) {
  const chatService = useMemo(() => chatServiceFor(scope), [scope]);
  const [messages, setMessages] = useState<ChatMessage[]>(() => mergeMessages([], initial));
  const [hasMore, setHasMore] = useState(initialHasMore);
  const [isLoadingOlder, setIsLoadingOlder] = useState(false);
  const pagesLoaded = useRef(1);
  const pendingCounter = useRef(0);
  const isPolling = useRef(false);

  const acknowledge = useCallback((): Promise<void> => {
    return chatService
      .markRead(matchUuid)
      .then(() => setMessages((current) => markIncomingRead(current, toApiDateTime(new Date()))))
      .catch(() => undefined);
  }, [chatService, matchUuid]);

  useEffect(() => {
    if (hasUnreadIncoming(initial)) void acknowledge();
  }, [initial, acknowledge]);

  useEffect(() => {
    const timer = window.setInterval(async () => {
      if (document.visibilityState !== "visible" || isPolling.current) return;

      isPolling.current = true;
      try {
        const page = await chatService.messages(matchUuid, 1);
        setMessages((current) => mergeMessages(current, page.data));
        if (hasUnreadIncoming(page.data)) void acknowledge();
      } catch {
        return;
      } finally {
        isPolling.current = false;
      }
    }, POLL_INTERVAL_MS);

    return () => window.clearInterval(timer);
  }, [chatService, matchUuid, acknowledge]);

  const send = useCallback(
    async (body: string): Promise<boolean> => {
      const text = normalizeBody(body);
      pendingCounter.current += 1;
      const pending = pendingMessage(pendingCounter.current, text, toApiDateTime(new Date()));

      setMessages((current) => [...current, pending]);

      try {
        const sent = await chatService.send(matchUuid, text);
        setMessages((current) => resolvePending(current, pending.uuid, sent));
        return true;
      } catch (caught) {
        setMessages((current) => dropPending(current, pending.uuid));
        onError(describeSendError(caught));
        return false;
      }
    },
    [chatService, matchUuid, onError],
  );

  const loadOlder = useCallback(async (): Promise<void> => {
    if (isLoadingOlder || !hasMore) return;

    setIsLoadingOlder(true);
    try {
      const nextPage = pagesLoaded.current + 1;
      const page = await chatService.messages(matchUuid, nextPage);

      pagesLoaded.current = nextPage;
      setMessages((current) => mergeMessages(current, page.data));
      setHasMore(page.meta.current_page < page.meta.last_page);
    } catch {
      onError("Não foi possível carregar as mensagens anteriores.");
    } finally {
      setIsLoadingOlder(false);
    }
  }, [chatService, matchUuid, hasMore, isLoadingOlder, onError]);

  return { messages, hasMore, isLoadingOlder, send, loadOlder };
}
