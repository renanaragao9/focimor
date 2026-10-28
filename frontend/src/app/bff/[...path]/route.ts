import { NextResponse, type NextRequest } from "next/server";

import { apiBaseUrl } from "@/lib/api/server";
import { buildUrl } from "@/lib/api/request";
import { clearToken, getToken } from "@/lib/auth/session";

interface Context {
  params: Promise<{ path: string[] }>;
}

const FORWARDED_HEADERS = ["content-type", "accept-language"] as const;

const BLOCKED_PATHS = new Set(["auth/login"]);

async function proxy(request: NextRequest, { params }: Context): Promise<NextResponse> {
  const { path } = await params;
  if (path.some((segment) => segment === "" || segment === "." || segment === ".." || /[\\/%]/.test(segment))) {
    return NextResponse.json({ status: "error", message: "Não encontrado.", errors: {} }, { status: 404 });
  }
  if (BLOCKED_PATHS.has(path.join("/").toLowerCase())) {
    return NextResponse.json({ status: "error", message: "Não encontrado.", errors: {} }, { status: 404 });
  }

  const headers = new Headers();
  for (const name of FORWARDED_HEADERS) {
    const value = request.headers.get(name);
    if (value) headers.set(name, value);
  }
  headers.set("Accept", "application/json");
  const token = await getToken();
  if (token) headers.set("Authorization", `Bearer ${token}`);

  const hasBody = request.method !== "GET" && request.method !== "HEAD";
  const upstream = await fetch(buildUrl(apiBaseUrl(), path.join("/")) + request.nextUrl.search, {
    method: request.method,
    headers,
    body: hasBody ? await request.arrayBuffer() : undefined,
    cache: "no-store",
  });

  if (upstream.status === 401) await clearToken();

  return new NextResponse(upstream.body, {
    status: upstream.status,
    headers: {
      "content-type": upstream.headers.get("content-type") ?? "application/json",
    },
  });
}

export { proxy as GET, proxy as POST, proxy as PUT, proxy as PATCH, proxy as DELETE };
