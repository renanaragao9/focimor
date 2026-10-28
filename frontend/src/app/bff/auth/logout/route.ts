import { NextResponse } from "next/server";

import { apiServer } from "@/lib/api/server";
import { clearToken } from "@/lib/auth/session";

export async function POST(): Promise<NextResponse> {
  try {
    await apiServer<null>("auth/logout", { method: "POST" });
  } catch {}
  await clearToken();
  return NextResponse.json({
    status: "success",
    message: "Logout realizado.",
    data: null,
  });
}
