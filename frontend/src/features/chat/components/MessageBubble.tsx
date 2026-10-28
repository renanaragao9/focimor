import { Check, CheckCheck, Clock } from "lucide-react";

import { LocalTime } from "@/features/chat/components/LocalTime";
import type { ChatMessage } from "@/features/chat/types/chatTypes";
import { cn } from "@/lib/utils";

interface MessageBubbleProps {
  message: ChatMessage;
}

export function MessageBubble({ message }: MessageBubbleProps) {
  const StatusIcon = message.pending ? Clock : message.read_at ? CheckCheck : Check;

  return (
    <div className={cn("flex", message.is_mine ? "justify-end" : "justify-start")}>
      <div
        className={cn(
          "flex max-w-[80%] flex-col gap-1 rounded-2xl px-3.5 py-2 text-sm shadow-xs",
          message.is_mine ? "rounded-br-sm bg-primary text-primary-foreground" : "rounded-bl-sm bg-muted text-foreground",
        )}
      >
        <p className="whitespace-pre-wrap wrap-break-word">{message.body}</p>
        <div className={cn("flex items-center justify-end gap-1 text-[11px]", message.is_mine ? "text-primary-foreground/80" : "text-muted-foreground")}>
          <LocalTime value={message.created_at} kind="clock" />
          {message.is_mine && (
            <StatusIcon className={cn("size-3.5", message.read_at && !message.pending && "text-white")} aria-label={message.pending ? "Enviando" : message.read_at ? "Lida" : "Enviada"} />
          )}
        </div>
      </div>
    </div>
  );
}
