import type { ApiFieldErrors } from "@/lib/api/types";

export class ApiError extends Error {
  readonly status: number;
  readonly fieldErrors: ApiFieldErrors;

  constructor(message: string, status: number, fieldErrors: ApiFieldErrors = {}) {
    super(message);
    this.name = "ApiError";
    this.status = status;
    this.fieldErrors = fieldErrors;
  }

  get isUnauthenticated(): boolean {
    return this.status === 401;
  }

  get isValidation(): boolean {
    return this.status === 422;
  }

  firstError(field: string): string | undefined {
    return this.fieldErrors[field]?.[0];
  }
}

export function toFieldErrors(errors: unknown): ApiFieldErrors {
  if (typeof errors !== "object" || errors === null || Array.isArray(errors)) return {};
  const result: ApiFieldErrors = {};
  for (const [field, value] of Object.entries(errors)) {
    if (Array.isArray(value)) result[field] = value.map(String);
    else if (typeof value === "string") result[field] = [value];
  }
  return result;
}
