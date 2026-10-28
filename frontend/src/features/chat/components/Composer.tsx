"use client";

import { SendHorizontal } from "lucide-react";
import { useRef, useState, type FormEvent, type KeyboardEvent } from "react";

import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import { MAX_MESSAGE_LENGTH } from "@/features/chat/types/chatTypes";
import { canSend, remainingCharacters } from "@/features/chat/utils/messages";

interface ComposerProps {
  onSend: (body: string) => Promise<boolean>;
}

const COUNTER_FROM = 200;
const MAX_HEIGHT_PX = 160;

export function Composer({ onSend }: ComposerProps) {
  const [body, setBody] = useState("");
  const field = useRef<HTMLTextAreaElement>(null);
  const remaining = remainingCharacters(body);

  function resize(): void {
    const element = field.current;
    if (!element) return;

    element.style.height = "auto";
    element.style.height = `${Math.min(element.scrollHeight, MAX_HEIGHT_PX)}px`;
  }

  async function submit(): Promise<void> {
    if (!canSend(body)) return;

    const text = body;
    setBody("");
    requestAnimationFrame(resize);

    const sent = await onSend(text);
    if (!sent) setBody((current) => current || text);
    field.current?.focus();
  }

  function handleSubmit(event: FormEvent<HTMLFormElement>): void {
    event.preventDefault();
    void submit();
  }

  function handleKeyDown(event: KeyboardEvent<HTMLTextAreaElement>): void {
    if (event.key !== "Enter" || event.shiftKey || event.nativeEvent.isComposing) return;

    event.preventDefault();
    void submit();
  }

  return (
    <form onSubmit={handleSubmit} className="flex items-end gap-2 border-t bg-background p-3">
      <div className="flex flex-1 flex-col gap-1">
        <Textarea
          ref={field}
          rows={1}
          value={body}
          maxLength={MAX_MESSAGE_LENGTH + 50}
          placeholder="Escreva uma mensagem"
          aria-label="Mensagem"
          className="max-h-40 min-h-10 resize-none"
          onChange={(event) => {
            setBody(event.target.value);
            resize();
          }}
          onKeyDown={handleKeyDown}
        />
        {remaining <= COUNTER_FROM && (
          <p className={remaining < 0 ? "text-xs text-destructive" : "text-xs text-muted-foreground"}>{remaining < 0 ? `${-remaining} caracteres a mais` : `${remaining} caracteres restantes`}</p>
        )}
      </div>
      <Button type="submit" size="icon" className="size-10 shrink-0 cursor-pointer" aria-label="Enviar mensagem" disabled={!canSend(body)}>
        <SendHorizontal />
      </Button>
    </form>
  );
}
