"use client";

import { ImagePlus, Loader2 } from "lucide-react";
import { useRouter } from "next/navigation";
import { useRef, useState, type ChangeEvent } from "react";
import { toast } from "sonner";

import { Button } from "@/components/ui/button";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { IMAGE_TYPES, MAX_LOGO_BYTES } from "@/features/ong-panel/types/ongPanelTypes";
import { ApiError } from "@/lib/api/errors";

export function LogoUploadButton({ ongUuid }: { ongUuid: string }) {
  const router = useRouter();
  const input = useRef<HTMLInputElement>(null);
  const [isUploading, setIsUploading] = useState(false);

  async function handleChange(event: ChangeEvent<HTMLInputElement>): Promise<void> {
    const file = event.target.files?.[0];
    event.target.value = "";
    if (!file) return;
    if (!(IMAGE_TYPES as readonly string[]).includes(file.type)) return void toast.error("A imagem deve ser JPG, PNG ou WEBP.");
    if (file.size > MAX_LOGO_BYTES) return void toast.error("A imagem não pode ter mais de 2 MB.");

    setIsUploading(true);
    try {
      await ongPanelService.uploadLogo(ongUuid, file);
      toast.success("Imagem de perfil atualizada.");
      router.refresh();
    } catch (caught) {
      toast.error(caught instanceof ApiError ? caught.message : "Não foi possível enviar a imagem.");
    } finally {
      setIsUploading(false);
    }
  }

  return (
    <>
      <input ref={input} type="file" accept={IMAGE_TYPES.join(",")} hidden data-testid="logo-input" onChange={(event) => void handleChange(event)} />
      <Button type="button" variant="outline" className="w-full cursor-pointer bg-background sm:w-auto" disabled={isUploading} onClick={() => input.current?.click()}>
        {isUploading ? <Loader2 className="animate-spin" /> : <ImagePlus />} Alterar imagem de perfil
      </Button>
    </>
  );
}
