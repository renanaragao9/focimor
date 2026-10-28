"use client";

import { Loader2, Save } from "lucide-react";
import { useCallback, useState, type FormEvent } from "react";
import { toast } from "sonner";

import { FormErrorAlert } from "@/components/FormErrorAlert";
import { SearchSelect, type SearchSelectOption } from "@/components/SearchSelect";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Switch } from "@/components/ui/switch";
import { Textarea } from "@/components/ui/textarea";
import { ongPanelService } from "@/features/ong-panel/services/ongPanelService";
import { petService } from "@/features/pet/services/petService";
import { EMPTY_PET_FORM, GENDER_OPTIONS, type Pet, type PetCatalogs, type PetFormValues } from "@/features/pet/types/petTypes";
import { todayIso } from "@/lib/dates";
import { PET_API_FIELDS, petToFormValues, toPetPayload, validatePetForm, type PetFormErrors } from "@/features/pet/validators/petValidators";
import { ApiError } from "@/lib/api/errors";

interface PetFormDialogProps {
  pet: Pet | null;
  open: boolean;
  catalogs: PetCatalogs;
  onOpenChange: (open: boolean) => void;
  onSaved: () => void;
  ongUuid?: string;
}

const NONE = "none";

export function PetFormDialog({ pet, open, catalogs, onOpenChange, onSaved, ongUuid }: PetFormDialogProps) {
  const isEditing = pet !== null;
  const [values, setValues] = useState<PetFormValues>(
    pet ? petToFormValues(pet) : { ...EMPTY_PET_FORM, goalId: ongUuid ? String(catalogs.goals.find((goal) => goal.slug === "adoption")?.id ?? "") : EMPTY_PET_FORM.goalId },
  );
  const [errors, setErrors] = useState<PetFormErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);
  const [breedName, setBreedName] = useState(pet?.breed?.name ?? "");

  const searchBreeds = useCallback(
    async (query: string): Promise<SearchSelectOption[]> => (await petService.breeds(Number(values.speciesId), query)).map((item) => ({ value: String(item.id), label: item.name })),
    [values.speciesId],
  );

  function setValue<K extends keyof PetFormValues>(key: K, value: PetFormValues[K]): void {
    setValues((current) => ({ ...current, [key]: value }));
  }

  function changeSpecies(speciesId: string): void {
    setValues((current) => ({ ...current, speciesId, breedId: "" }));
    setBreedName("");
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>): Promise<void> {
    event.preventDefault();
    setFormError(null);

    const found = validatePetForm(values, todayIso(new Date()));
    setErrors(found);
    if (Object.keys(found).length > 0) return;

    setIsSaving(true);
    try {
      const payload = toPetPayload(values);
      if (pet) {
        await petService.update(pet.uuid, payload);
      } else {
        await (ongUuid ? ongPanelService.createPet(ongUuid, payload) : petService.create(payload));
      }
      toast.success(pet ? "Pet atualizado." : "Pet criado.");
      onSaved();
    } catch (caught) {
      if (caught instanceof ApiError) {
        setErrors(Object.fromEntries(PET_API_FIELDS.map(([key, apiKey]) => [key, caught.firstError(apiKey)]).filter(([, message]) => message)) as PetFormErrors);
        setFormError(caught.message);
      } else {
        setFormError("Não foi possível salvar o pet. Tente novamente.");
      }
    } finally {
      setIsSaving(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={(next) => !isSaving && onOpenChange(next)}>
      <DialogContent className="max-h-[var(--vvh,100dvh)] overflow-y-auto sm:max-h-[92dvh] sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>{isEditing ? `Editar ${pet.name}` : "Novo pet"}</DialogTitle>
          <DialogDescription>{isEditing ? "Atualize os dados do seu pet." : "Conte um pouco sobre o seu pet. Você poderá adicionar fotos depois de salvar."}</DialogDescription>
        </DialogHeader>

        <form onSubmit={handleSubmit} noValidate>
          <FieldGroup className="gap-5">
            {formError && <FormErrorAlert message={formError} />}

            <div className="grid gap-5 sm:grid-cols-2">
              <Field data-invalid={Boolean(errors.name)}>
                <FieldLabel htmlFor="pet-name">Nome *</FieldLabel>
                <Input id="pet-name" placeholder="Ex.: Thor" value={values.name} onChange={(event) => setValue("name", event.target.value)} aria-invalid={Boolean(errors.name)} disabled={isSaving} />
                {errors.name && <FieldError>{errors.name}</FieldError>}
              </Field>

              <Field data-invalid={Boolean(errors.gender)}>
                <FieldLabel htmlFor="pet-gender">Sexo *</FieldLabel>
                <Select value={values.gender} onValueChange={(value) => setValue("gender", value as PetFormValues["gender"])} disabled={isSaving}>
                  <SelectTrigger id="pet-gender" className="w-full cursor-pointer" aria-invalid={Boolean(errors.gender)}>
                    <SelectValue placeholder="Selecione" />
                  </SelectTrigger>
                  <SelectContent>
                    {GENDER_OPTIONS.map((option) => (
                      <SelectItem key={option.value} value={option.value}>
                        {option.label}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.gender && <FieldError>{errors.gender}</FieldError>}
              </Field>

              <Field data-invalid={Boolean(errors.speciesId)}>
                <FieldLabel htmlFor="pet-species">Espécie *</FieldLabel>
                <Select value={values.speciesId} onValueChange={changeSpecies} disabled={isSaving}>
                  <SelectTrigger id="pet-species" className="w-full cursor-pointer" aria-invalid={Boolean(errors.speciesId)}>
                    <SelectValue placeholder="Selecione" />
                  </SelectTrigger>
                  <SelectContent>
                    {catalogs.species.map((item) => (
                      <SelectItem key={item.id} value={String(item.id)}>
                        {item.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.speciesId && <FieldError>{errors.speciesId}</FieldError>}
              </Field>

              <Field data-invalid={Boolean(errors.breedId)}>
                <FieldLabel htmlFor="pet-breed">Raça</FieldLabel>
                <SearchSelect
                  key={values.speciesId}
                  id="pet-breed"
                  value={values.breedId}
                  selectedLabel={breedName}
                  placeholder={values.speciesId ? "Buscar raça" : "Escolha a espécie primeiro"}
                  searchPlaceholder="Digite o nome da raça"
                  emptyLabel="Nenhuma raça encontrada."
                  clearLabel="Não informar"
                  invalid={Boolean(errors.breedId)}
                  disabled={isSaving || !values.speciesId}
                  search={searchBreeds}
                  onChange={(option) => {
                    setValue("breedId", option?.value ?? "");
                    setBreedName(option?.label ?? "");
                  }}
                />
                {errors.breedId && <FieldError>{errors.breedId}</FieldError>}
              </Field>

              <Field data-invalid={Boolean(errors.sizeId)}>
                <FieldLabel htmlFor="pet-size">Porte</FieldLabel>
                <Select value={values.sizeId || NONE} onValueChange={(value) => setValue("sizeId", value === NONE ? "" : value)} disabled={isSaving}>
                  <SelectTrigger id="pet-size" className="w-full cursor-pointer">
                    <SelectValue placeholder="Selecione" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value={NONE}>Não informar</SelectItem>
                    {catalogs.sizes.map((item) => (
                      <SelectItem key={item.id} value={String(item.id)}>
                        {item.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.sizeId && <FieldError>{errors.sizeId}</FieldError>}
              </Field>

              {!ongUuid && (
                <Field data-invalid={Boolean(errors.goalId)}>
                  <FieldLabel htmlFor="pet-goal">Objetivo *</FieldLabel>
                  <Select value={values.goalId} onValueChange={(value) => setValue("goalId", value)} disabled={isSaving}>
                    <SelectTrigger id="pet-goal" className="w-full cursor-pointer" aria-invalid={Boolean(errors.goalId)}>
                      <SelectValue placeholder="Selecione" />
                    </SelectTrigger>
                    <SelectContent>
                      {catalogs.goals.map((item) => (
                        <SelectItem key={item.id} value={String(item.id)}>
                          {item.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {errors.goalId && <FieldError>{errors.goalId}</FieldError>}
                </Field>
              )}

              <Field data-invalid={Boolean(errors.birthDate)} className="sm:col-span-2">
                <FieldLabel htmlFor="pet-birth">Data de nascimento</FieldLabel>
                <Input
                  id="pet-birth"
                  type="date"
                  className="sm:max-w-xs"
                  max={todayIso(new Date())}
                  value={values.birthDate}
                  onChange={(event) => setValue("birthDate", event.target.value)}
                  aria-invalid={Boolean(errors.birthDate)}
                  disabled={isSaving}
                />
                {errors.birthDate && <FieldError>{errors.birthDate}</FieldError>}
              </Field>
            </div>

            <Field data-invalid={Boolean(errors.color)}>
              <FieldLabel htmlFor="pet-color">Cor</FieldLabel>
              <Input
                id="pet-color"
                placeholder="Ex.: caramelo, preto e branco"
                maxLength={60}
                value={values.color}
                onChange={(event) => setValue("color", event.target.value)}
                aria-invalid={Boolean(errors.color)}
                disabled={isSaving}
              />
              {errors.color && <FieldError>{errors.color}</FieldError>}
            </Field>

            <Field data-invalid={Boolean(errors.bio)}>
              <FieldLabel htmlFor="pet-bio">Sobre o pet</FieldLabel>
              <Textarea
                id="pet-bio"
                rows={3}
                placeholder="Personalidade, manias, o que ele gosta..."
                value={values.bio}
                onChange={(event) => setValue("bio", event.target.value)}
                aria-invalid={Boolean(errors.bio)}
                disabled={isSaving}
              />
              {errors.bio && <FieldError>{errors.bio}</FieldError>}
            </Field>

            <div className="grid gap-3 sm:grid-cols-3">
              {(
                [
                  ["isVaccinated", "Vacinado"],
                  ["isNeutered", "Castrado"],
                  ["isActive", "Ativo"],
                ] as const
              ).map(([key, label]) => (
                <div key={key} className="flex items-center justify-between gap-3 rounded-sm border px-3 py-2">
                  <Label htmlFor={`pet-${key}`} className="cursor-pointer">
                    {label}
                  </Label>
                  <Switch id={`pet-${key}`} checked={values[key]} onCheckedChange={(checked) => setValue(key, checked)} disabled={isSaving} />
                </div>
              ))}
            </div>

            <Button type="submit" className="w-full cursor-pointer sm:ml-auto sm:w-auto" disabled={isSaving}>
              {isSaving ? <Loader2 className="animate-spin" /> : <Save />}
              {isSaving ? "Salvando..." : isEditing ? "Salvar alterações" : "Criar pet"}
            </Button>
          </FieldGroup>
        </form>
      </DialogContent>
    </Dialog>
  );
}
