"use client";

import { Pencil, Trash2 } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";

import { DeleteDialog } from "@/components/DeleteDialog";
import { Button } from "@/components/ui/button";
import { PostFormDialog } from "@/features/ong-panel/components/PostFormDialog";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { panelHref } from "@/features/ong-panel/utils/panelParams";
import type { OngPost } from "@/features/ong/types/ongTypes";

export function PostDetailActions({ ongUuid, post }: { ongUuid: string; post: OngPost }) {
  const router = useRouter();
  const [editOpen, setEditOpen] = useState(false);
  const [deleteOpen, setDeleteOpen] = useState(false);

  return (
    <>
      <Button className="cursor-pointer" onClick={() => setEditOpen(true)}>
        <Pencil /> Editar
      </Button>
      <Button variant="outline" className="cursor-pointer" onClick={() => setDeleteOpen(true)}>
        <Trash2 /> Excluir
      </Button>

      <PostFormDialog key={post.uuid + (editOpen ? "-open" : "")} ongUuid={ongUuid} post={post} open={editOpen} onOpenChange={setEditOpen} onSaved={() => router.refresh()} />

      <DeleteDialog
        name={deleteOpen ? post.title : null}
        title="Excluir post"
        warning="O post deixa de aparecer na página da ONG e no feed."
        successMessage="Post excluído."
        errorMessage="Não foi possível excluir o post."
        onConfirm={async () => {
          await ongPanelService.removePost(ongUuid, post.uuid);
        }}
        onDeleted={() => router.replace(panelHref(ongUuid, { tab: "posts", page: 1 }))}
        onOpenChange={setDeleteOpen}
      />
    </>
  );
}
