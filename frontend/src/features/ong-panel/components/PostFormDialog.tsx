"use client";

import { ImagePlus } from "lucide-react";
import { useRef, useState, type FormEvent } from "react";
import { toast } from "sonner";

import { DateField, TextAreaField, TextField } from "@/components/FormFields";
import { RecordFormDialog } from "@/components/RecordFormDialog";
import { Button } from "@/components/ui/button";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { IMAGE_TYPES, MAX_COVER_BYTES, type PostValues } from "@/features/ong-panel/types/ongPanelTypes";
import { POST_API_FIELDS, postToValues, toPostPayload, validatePost, type PostErrors } from "@/features/ong-panel/validators/panelValidators";
import type { OngPost } from "@/features/ong/types/ongTypes";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";

interface PostFormDialogProps {
  ongUuid: string;
  post: OngPost | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved: () => void;
}

export function PostFormDialog({ ongUuid, post, open, onOpenChange, onSaved }: PostFormDialogProps) {
  const coverInput = useRef<HTMLInputElement>(null);
  const [values, setValues] = useState<PostValues>(post ? postToValues(post) : { title: "", body: "", publishedAt: "" });
  const [cover, setCover] = useState<File | null>(null);
  const [errors, setErrors] = useState<PostErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  function bind<K extends keyof PostValues>(key: K) {
    return { value: values[key], error: errors[key], disabled: isSaving, onChange: (value: string) => setValues((current) => ({ ...current, [key]: value })) };
  }

  function pickCover(file: File | undefined): void {
    if (!file) return;
    if (!(IMAGE_TYPES as readonly string[]).includes(file.type)) return void toast.error("A capa deve ser JPG, PNG ou WEBP.");
    if (file.size > MAX_COVER_BYTES) return void toast.error("A capa não pode ter mais de 5 MB.");
    setCover(file);
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validatePost(values);
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      const payload = toPostPayload(values);
      const saved = post ? await ongPanelService.updatePost(ongUuid, post.uuid, payload) : await ongPanelService.createPost(ongUuid, payload);
      if (cover) await ongPanelService.uploadCover(ongUuid, saved.uuid, cover);
      toast.success(post ? "Post atualizado." : "Post criado.");
      onSaved();
      onOpenChange(false);
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, POST_API_FIELDS));
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível salvar o post. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <RecordFormDialog
      open={open}
      onOpenChange={onOpenChange}
      title={post ? "Editar post" : "Novo post"}
      description="Sem data de publicação o post fica como rascunho."
      submitLabel={post ? "Salvar alterações" : "Criar post"}
      isSaving={isSaving}
      formError={formError}
      onSubmit={handleSubmit}
    >
      <TextField id="post-title" label="Título" required {...bind("title")} />
      <TextAreaField id="post-body" label="Conteúdo" required rows={8} {...bind("body")} />
      <DateField id="post-date" label="Publicar em" hint="Deixe vazio para manter como rascunho." {...bind("publishedAt")} />

      <div className="flex items-center gap-3">
        <input
          ref={coverInput}
          type="file"
          accept={IMAGE_TYPES.join(",")}
          hidden
          data-testid="cover-input"
          onChange={(event) => {
            pickCover(event.target.files?.[0]);
            event.target.value = "";
          }}
        />
        <Button type="button" variant="outline" size="sm" className="cursor-pointer" disabled={isSaving} onClick={() => coverInput.current?.click()}>
          <ImagePlus /> {post?.cover_url || cover ? "Trocar capa" : "Adicionar capa"}
        </Button>
        {cover && <span className="truncate text-xs text-muted-foreground">{cover.name}</span>}
      </div>
    </RecordFormDialog>
  );
}
