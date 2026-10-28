import type { ApiPage } from "@/lib/api/types";

import { ApiError } from "./errors";
import { buildInit, buildUrl, unwrap, unwrapPage, type ApiRequestInit } from "./request";

export async function api<T>(path: string, init: ApiRequestInit = {}): Promise<T> {
  const response = await fetch(buildUrl("/bff", path, init.query), {
    ...buildInit(init),
    credentials: "same-origin",
  });
  return unwrap<T>(response);
}

export async function apiPage<T>(path: string, init: ApiRequestInit = {}): Promise<ApiPage<T>> {
  const response = await fetch(buildUrl("/bff", path, init.query), {
    ...buildInit(init),
    credentials: "same-origin",
  });
  return unwrapPage<T>(response);
}

export async function downloadFile(path: string, fallbackName: string): Promise<void> {
  const response = await fetch(buildUrl("/bff", path), { credentials: "same-origin" });
  if (!response.ok) {
    const failure = (await response.json().catch(() => null)) as { message?: string } | null;
    throw new ApiError(failure?.message ?? "Não foi possível baixar o arquivo.", response.status);
  }

  const disposition = response.headers.get("content-disposition") ?? "";
  const fileName = /filename="?([^";]+)"?/i.exec(disposition)?.[1] ?? fallbackName;
  const url = URL.createObjectURL(await response.blob());
  const link = document.createElement("a");
  link.href = url;
  link.download = fileName;
  document.body.appendChild(link);
  link.click();
  link.remove();
  URL.revokeObjectURL(url);
}
