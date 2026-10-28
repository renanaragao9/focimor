import { NextResponse } from "next/server";

import { ApiError } from "@/lib/api/errors";
import { apiServer } from "@/lib/api/server";
import type { AuthUser, LoginCredentials } from "@/lib/auth/authTypes";
import { setToken } from "@/lib/auth/session";

interface LoginPayload {
  token: string;
  me: AuthUser;
}

function failure(message: string, status: number, errors: unknown = {}): NextResponse {
  return NextResponse.json({ status: "error", message, errors }, { status });
}

export async function POST(request: Request): Promise<NextResponse> {
  const credentials = (await request.json().catch(() => null)) as LoginCredentials | null;
  if (!credentials) return failure("Informe seu e-mail e sua senha.", 422);

  try {
    const { token, me } = await apiServer<LoginPayload>("auth/login", {
      method: "POST",
      json: credentials,
    });

    await setToken(token);
    return NextResponse.json({ status: "success", message: "Login realizado.", data: me });
  } catch (error) {
    if (error instanceof ApiError) return failure(error.message, error.status, error.fieldErrors);
    return failure("Não foi possível conectar ao servidor. Verifique se a API está em execução e tente novamente.", 503);
  }
}
