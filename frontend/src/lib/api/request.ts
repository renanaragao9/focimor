import type { ApiFailure, ApiPage, ApiSuccess, QueryParams } from "@/lib/api/types";
import { ApiError, toFieldErrors } from "./errors";

export interface ApiRequestInit extends Omit<RequestInit, "body"> {
  json?: unknown;
  body?: BodyInit;
  query?: QueryParams;
}

export function buildUrl(base: string, path: string, query?: QueryParams): string {
  const url = `${base.replace(/\/+$/, "")}/${path.replace(/^\/+/, "")}`;
  if (!query) return url;
  const params = new URLSearchParams();
  for (const [key, value] of Object.entries(query)) {
    if (value === undefined || value === null) continue;
    if (Array.isArray(value)) value.forEach((item) => params.append(key, String(item)));
    else params.set(key, String(value));
  }
  const qs = params.toString();
  return qs ? `${url}?${qs}` : url;
}

export function buildInit({ json, headers, ...init }: ApiRequestInit): RequestInit {
  const merged = new Headers(headers);
  merged.set("Accept", "application/json");
  if (json !== undefined) merged.set("Content-Type", "application/json");
  return {
    ...init,
    headers: merged,
    body: json !== undefined ? JSON.stringify(json) : init.body,
  };
}

export async function unwrap<T>(response: Response): Promise<T> {
  const payload = (await response.json().catch(() => null)) as ApiSuccess<T> | ApiFailure | null;

  if (!response.ok || payload?.status === "error") {
    const failure = payload as ApiFailure | null;
    throw new ApiError(failure?.message ?? "Não foi possível concluir a solicitação.", response.status, toFieldErrors(failure?.errors));
  }

  return (payload as ApiSuccess<T>).data;
}

export async function unwrapPage<T>(response: Response): Promise<ApiPage<T>> {
  const payload = (await response.json().catch(() => null)) as (ApiPage<T> & { status?: undefined }) | ApiFailure | null;

  if (!response.ok || !payload || !("meta" in payload)) {
    const failure = payload as ApiFailure | null;
    throw new ApiError(failure?.message ?? "Não foi possível concluir a solicitação.", response.status, toFieldErrors(failure?.errors));
  }

  return { data: payload.data, meta: payload.meta };
}
