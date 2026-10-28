"use client";

import { Plus, UserMinus } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";
import { toast } from "sonner";

import { DeleteDialog } from "@/components/DeleteDialog";
import { TextField } from "@/components/FormFields";
import { RecordFormDialog } from "@/components/RecordFormDialog";
import { Button } from "@/components/ui/button";
import { Field, FieldLabel } from "@/components/ui/field";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { ROLE_LABELS, type MemberValues, type OngMember, type OngRole } from "@/features/ong-panel/types/ongPanelTypes";
import { MEMBER_API_FIELDS, validateMember, type MemberErrors } from "@/features/ong-panel/validators/panelValidators";
import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";

interface MembersPanelProps {
  ongUuid: string;
  members: OngMember[];
  canManage: boolean;
  currentEmail: string;
}

export function MembersPanel({ ongUuid, members, canManage, currentEmail }: MembersPanelProps) {
  const router = useRouter();
  const [addOpen, setAddOpen] = useState(false);
  const [addKey, setAddKey] = useState("add-0");
  const [toRemove, setToRemove] = useState<OngMember | null>(null);

  async function changeRole(member: OngMember, role: OngRole): Promise<void> {
    try {
      await ongPanelService.setMemberRole(ongUuid, member.id, role);
      toast.success("Papel atualizado.");
      router.refresh();
    } catch (caught) {
      toast.error(caught instanceof ApiError ? caught.message : "Não foi possível alterar o papel.");
    }
  }

  return (
    <>
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="text-xl font-semibold">Membros</h2>
          <p className="text-sm text-muted-foreground">Quem pode cuidar dos pets e dos posts da ONG. A pessoa precisa ter conta no Focimor.</p>
        </div>
        {canManage && (
          <Button
            className="cursor-pointer"
            onClick={() => {
              setAddKey(`add-${Date.now()}`);
              setAddOpen(true);
            }}
          >
            <Plus /> Adicionar membro
          </Button>
        )}
      </div>

      <div className="flex flex-col gap-2">
        {members.map((member) => {
          const isSelf = member.email.toLowerCase() === currentEmail.toLowerCase();

          return (
            <article key={member.id} className="flex flex-wrap items-center gap-3 rounded-sm border bg-card p-3">
              <div className="min-w-0 flex-1">
                <h3 className="truncate text-base font-semibold">
                  {member.name}
                  {isSelf && <span className="ml-2 text-xs font-normal text-muted-foreground">(você)</span>}
                </h3>
                <p className="truncate text-xs text-muted-foreground">{member.email}</p>
              </div>
              {canManage ? (
                <Select value={member.role} onValueChange={(value) => void changeRole(member, value as OngRole)}>
                  <SelectTrigger aria-label={`Papel de ${member.name}`} className="w-40 cursor-pointer">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="admin">{ROLE_LABELS.admin}</SelectItem>
                    <SelectItem value="member">{ROLE_LABELS.member}</SelectItem>
                  </SelectContent>
                </Select>
              ) : (
                <span className="text-sm text-muted-foreground">{ROLE_LABELS[member.role]}</span>
              )}
              {(canManage || isSelf) && (
                <Button
                  variant="ghost"
                  size="icon-sm"
                  className="cursor-pointer text-destructive hover:text-destructive"
                  aria-label={isSelf ? "Sair da ONG" : `Remover ${member.name}`}
                  onClick={() => setToRemove(member)}
                >
                  <UserMinus />
                </Button>
              )}
            </article>
          );
        })}
      </div>

      <AddMemberDialog key={addKey} ongUuid={ongUuid} open={addOpen} onOpenChange={setAddOpen} onSaved={() => router.refresh()} />

      <DeleteDialog
        name={toRemove?.name ?? null}
        title={toRemove?.email.toLowerCase() === currentEmail.toLowerCase() ? "Sair da ONG" : "Remover membro"}
        warning="A pessoa deixa de ter acesso aos pets e posts da ONG."
        successMessage="Membro removido."
        errorMessage="Não foi possível remover o membro."
        confirmLabel="Remover"
        icon={UserMinus}
        onConfirm={async () => {
          if (toRemove) await ongPanelService.removeMember(ongUuid, toRemove.id);
        }}
        onDeleted={() => router.refresh()}
        onOpenChange={(open) => !open && setToRemove(null)}
      />
    </>
  );
}

interface AddMemberDialogProps {
  ongUuid: string;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSaved: () => void;
}

function AddMemberDialog({ ongUuid, open, onOpenChange, onSaved }: AddMemberDialogProps) {
  const [values, setValues] = useState<MemberValues>({ email: "", role: "member" });
  const [errors, setErrors] = useState<MemberErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validateMember(values);
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      await ongPanelService.addMember(ongUuid, values.email, values.role);
      toast.success("Membro adicionado.");
      onSaved();
      onOpenChange(false);
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(fieldErrorsFrom(caught, MEMBER_API_FIELDS));
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível adicionar o membro. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <RecordFormDialog
      open={open}
      onOpenChange={onOpenChange}
      title="Adicionar membro"
      description="Informe o e-mail da conta que vai entrar na ONG."
      submitLabel="Adicionar"
      isSaving={isSaving}
      formError={formError}
      onSubmit={handleSubmit}
    >
      <TextField
        id="member-email"
        label="E-mail"
        type="email"
        required
        value={values.email}
        error={errors.email}
        disabled={isSaving}
        onChange={(email) => setValues((current) => ({ ...current, email }))}
      />
      <Field>
        <FieldLabel htmlFor="member-role">Papel</FieldLabel>
        <Select value={values.role} onValueChange={(role) => setValues((current) => ({ ...current, role: role as OngRole }))} disabled={isSaving}>
          <SelectTrigger id="member-role" className="w-full cursor-pointer">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="member">{ROLE_LABELS.member}</SelectItem>
            <SelectItem value="admin">{ROLE_LABELS.admin}</SelectItem>
          </SelectContent>
        </Select>
      </Field>
    </RecordFormDialog>
  );
}
