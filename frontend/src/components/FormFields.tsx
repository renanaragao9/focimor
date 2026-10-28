import type { ComponentProps, ReactNode } from "react";

import { Field, FieldError, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";

interface BaseFieldProps {
  id: string;
  label: string;
  error?: string;
  required?: boolean;
  className?: string;
  hint?: ReactNode;
}

type TextFieldProps = BaseFieldProps & Omit<ComponentProps<typeof Input>, "id" | "className" | "onChange" | "value"> & { value: string; onChange: (value: string) => void };

export function TextField({ id, label, error, required, className, hint, value, onChange, ...props }: TextFieldProps) {
  return (
    <Field data-invalid={Boolean(error)} className={className}>
      <FieldLabel htmlFor={id}>
        {label}
        {required && " *"}
      </FieldLabel>
      <Input id={id} value={value} onChange={(event) => onChange(event.target.value)} aria-invalid={Boolean(error)} {...props} />
      {hint && !error && <p className="text-xs text-muted-foreground">{hint}</p>}
      {error && <FieldError>{error}</FieldError>}
    </Field>
  );
}

type TextAreaFieldProps = BaseFieldProps & Omit<ComponentProps<typeof Textarea>, "id" | "className" | "onChange" | "value"> & { value: string; onChange: (value: string) => void };

export function TextAreaField({ id, label, error, required, className, hint, value, onChange, ...props }: TextAreaFieldProps) {
  return (
    <Field data-invalid={Boolean(error)} className={className}>
      <FieldLabel htmlFor={id}>
        {label}
        {required && " *"}
      </FieldLabel>
      <Textarea id={id} rows={3} value={value} onChange={(event) => onChange(event.target.value)} aria-invalid={Boolean(error)} {...props} />
      {hint && !error && <p className="text-xs text-muted-foreground">{hint}</p>}
      {error && <FieldError>{error}</FieldError>}
    </Field>
  );
}

type DateFieldProps = Omit<TextFieldProps, "type">;

export function DateField(props: DateFieldProps) {
  return <TextField type="date" {...props} />;
}
