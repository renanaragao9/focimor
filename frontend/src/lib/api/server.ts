import "server-only";

import { getToken } from "@/lib/auth/session";

import type { ApiPage } from "@/lib/api/types";

import { buildInit, buildUrl, unwrap, unwrapPage, type ApiRequestInit } from "./request";

export interface ApiServerRequestInit extends ApiRequestInit {
  token?: string;
}

export function apiBaseUrl(): string {
  const url = process.env.API_URL;
  if (!url) throw new Error("Defina API_URL (ex.: http://localhost:8000/api/v1) em .env.local.");
  return url;
}

export async function apiServer<T>(path: string, { token: explicitToken, ...init }: ApiServerRequestInit = {}): Promise<T> {
  const token = explicitToken ?? (await getToken());
  const request = buildInit(init);
  const headers = new Headers(request.headers);
  if (token) headers.set("Authorization", `Bearer ${token}`);

  const response = await fetch(buildUrl(apiBaseUrl(), path, init.query), {
    ...request,
    headers,
    cache: "no-store",
  });
  return unwrap<T>(response);
}

export async function apiServerPage<T>(path: string, init: ApiServerRequestInit = {}): Promise<ApiPage<T>> {
  const { token: explicitToken, ...rest } = init;
  const token = explicitToken ?? (await getToken());
  const request = buildInit(rest);
  const headers = new Headers(request.headers);
  if (token) headers.set("Authorization", `Bearer ${token}`);

  const response = await fetch(buildUrl(apiBaseUrl(), path, init.query), {
    ...request,
    headers,
    cache: "no-store",
  });
  return unwrapPage<T>(response);
}

export async function apiPublicPage<T>(path: string, init: ApiRequestInit = {}, revalidate = 3600): Promise<ApiPage<T>> {
  const response = await fetch(buildUrl(apiBaseUrl(), path, init.query), {
    ...buildInit(init),
    next: { revalidate },
  });
  return unwrapPage<T>(response);
}

export async function apiPublic<T>(path: string, init: ApiRequestInit = {}, revalidate = 60): Promise<T> {
  const response = await fetch(buildUrl(apiBaseUrl(), path, init.query), {
    ...buildInit(init),
    next: { revalidate },
  });
  return unwrap<T>(response);
}
