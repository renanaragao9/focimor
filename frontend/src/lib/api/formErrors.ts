import type { ApiError } from "@/lib/api/errors";

export function fieldErrorsFrom<K extends string>(error: ApiError, fields: ReadonlyArray<readonly [K, string]>): Partial<Record<K, string>> {
  const errors: Partial<Record<K, string>> = {};

  for (const [key, apiKey] of fields) {
    const message = error.firstError(apiKey);
    if (message) errors[key] = message;
  }

  return errors;
}
