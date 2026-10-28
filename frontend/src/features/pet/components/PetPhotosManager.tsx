"use client";

import { ImagePlus, Loader2, Star, Trash2 } from "lucide-react";
import Image from "next/image";
import { useRef, useState, type ChangeEvent } from "react";
import { toast } from "sonner";

import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { MAX_PET_PHOTOS, MAX_PHOTO_BYTES, PHOTO_TYPES, type PetPhoto } from "@/features/pet/types/petTypes";
import { petService } from "@/features/pet/services/petService";
import { ApiError } from "@/lib/api/errors";

interface PetPhotosManagerProps {
  petUuid: string;
  photos: PetPhoto[];
  onChange: (photos: PetPhoto[]) => void;
}

export function PetPhotosManager({ petUuid, photos, onChange }: PetPhotosManagerProps) {
  const inputRef = useRef<HTMLInputElement>(null);
  const [isBusy, setIsBusy] = useState(false);
  const isFull = photos.length >= MAX_PET_PHOTOS;

  async function run(action: () => Promise<unknown>, failure: string): Promise<void> {
    setIsBusy(true);
    try {
      await action();
    } catch (caught) {
      toast.error(caught instanceof ApiError ? caught.message : failure);
    }
    try {
      onChange(await petService.photos(petUuid));
    } finally {
      setIsBusy(false);
    }
  }

  async function handleFiles(event: ChangeEvent<HTMLInputElement>): Promise<void> {
    const files = Array.from(event.target.files ?? []).slice(0, MAX_PET_PHOTOS - photos.length);
    event.target.value = "";
    if (files.length === 0) return;

    await run(async () => {
      for (const file of files) {
        if (!(PHOTO_TYPES as readonly string[]).includes(file.type)) {
          toast.error(`"${file.name}" não é JPG, PNG ou WEBP.`);
          continue;
        }
        if (file.size > MAX_PHOTO_BYTES) {
          toast.error(`"${file.name}" tem mais de 5 MB.`);
          continue;
        }
        await petService.uploadPhoto(petUuid, file);
      }
    }, "Não foi possível enviar a foto.");
  }

  return (
    <div className="flex flex-col gap-3">
      <div className="flex items-center justify-between gap-3">
        <div>
          <h3 className="text-sm font-semibold">Fotos</h3>
          <p className="text-xs text-muted-foreground">
            {photos.length} de {MAX_PET_PHOTOS} · JPG, PNG ou WEBP até 5 MB
          </p>
        </div>
        <input ref={inputRef} type="file" accept={PHOTO_TYPES.join(",")} multiple hidden data-testid="photo-input" onChange={(event) => void handleFiles(event)} />
        <Button type="button" variant="outline" size="sm" className="cursor-pointer" disabled={isBusy || isFull} onClick={() => inputRef.current?.click()}>
          {isBusy ? <Loader2 className="animate-spin" /> : <ImagePlus />}
          Adicionar fotos
        </Button>
      </div>

      {photos.length === 0 ? (
        <p className="rounded-sm border border-dashed px-4 py-6 text-center text-sm text-muted-foreground">Nenhuma foto ainda. A primeira vira a foto principal.</p>
      ) : (
        <ul className="grid grid-cols-1 gap-4 min-[480px]:grid-cols-2 lg:grid-cols-3">
          {photos.map((photo, index) => (
            <li key={photo.id} className="overflow-hidden rounded-sm border bg-card shadow-sm">
              <div className="relative aspect-square bg-muted">
                <Image src={photo.url} alt={`Foto ${index + 1}`} fill unoptimized sizes="(min-width: 1024px) 18rem, (min-width: 480px) 50vw, 100vw" className="object-cover" />
                {photo.is_primary && <Badge className="absolute left-3 top-3">Principal</Badge>}
              </div>
              <div className="flex gap-2 p-3">
                {!photo.is_primary && (
                  <Button
                    type="button"
                    size="sm"
                    variant="outline"
                    className="flex-1 cursor-pointer"
                    disabled={isBusy}
                    onClick={() => void run(() => petService.setPrimaryPhoto(petUuid, photo.id), "Não foi possível definir a foto principal.")}
                  >
                    <Star /> Definir principal
                  </Button>
                )}
                <Button
                  type="button"
                  size="sm"
                  variant="destructive"
                  className="flex-1 cursor-pointer"
                  disabled={isBusy}
                  onClick={() => void run(() => petService.removePhoto(petUuid, photo.id), "Não foi possível excluir a foto.")}
                >
                  <Trash2 /> Excluir
                </Button>
              </div>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
