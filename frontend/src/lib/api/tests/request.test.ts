import { describe, expect, it } from "vitest";

import { ApiError, toFieldErrors } from "@/lib/api/errors";
import { buildInit, buildUrl, unwrap, unwrapPage } from "@/lib/api/request";

function jsonResponse(body: unknown, status = 200): Response {
  return new Response(JSON.stringify(body), { status, headers: { "content-type": "application/json" } });
}

describe("buildUrl", () => {
  it("junta base e caminho sem barras duplicadas", () => {
    expect(buildUrl("/bff/", "/auth/logout")).toBe("/bff/auth/logout");
    expect(buildUrl("http://localhost:8000/api/v1", "me")).toBe("http://localhost:8000/api/v1/me");
  });

  it("monta a query ignorando vazios e repetindo arrays", () => {
    expect(buildUrl("/bff", "pets", { search: "rex", page: 2, empty: null, none: undefined, ids: [1, 2] })).toBe("/bff/pets?search=rex&page=2&ids=1&ids=2");
  });
});

describe("buildInit", () => {
  it("sempre pede JSON e serializa o corpo", () => {
    const init = buildInit({ method: "POST", json: { a: 1 } });
    const headers = init.headers as Headers;

    expect(headers.get("Accept")).toBe("application/json");
    expect(headers.get("Content-Type")).toBe("application/json");
    expect(init.body).toBe('{"a":1}');
  });

  it("não força Content-Type quando não há JSON (multipart)", () => {
    const body = new FormData();
    const init = buildInit({ method: "POST", body });

    expect((init.headers as Headers).get("Content-Type")).toBeNull();
    expect(init.body).toBe(body);
  });
});

describe("unwrap", () => {
  it("devolve o data do envelope de sucesso", async () => {
    expect(await unwrap<{ id: number }>(jsonResponse({ status: "success", message: "ok", data: { id: 7 } }))).toEqual({ id: 7 });
  });

  it("lança ApiError com mensagem, status e erros por campo", async () => {
    const response = jsonResponse({ status: "error", message: "E-mail inválido.", errors: { email: ["Informe um e-mail válido."] } }, 422);

    const error = await unwrap(response).catch((caught: unknown) => caught);

    expect(error).toBeInstanceOf(ApiError);
    expect((error as ApiError).status).toBe(422);
    expect((error as ApiError).isValidation).toBe(true);
    expect((error as ApiError).firstError("email")).toBe("Informe um e-mail válido.");
  });

  it("reconhece 401 e usa mensagem padrão quando o corpo não é JSON", async () => {
    const error = (await unwrap(new Response("<html>", { status: 401 })).catch((caught: unknown) => caught)) as ApiError;

    expect(error.isUnauthenticated).toBe(true);
    expect(error.message).toBe("Não foi possível concluir a solicitação.");
  });

  it("trata status error mesmo com HTTP 200", async () => {
    await expect(unwrap(jsonResponse({ status: "error", message: "Falhou." }))).rejects.toThrow("Falhou.");
  });
});

describe("unwrapPage", () => {
  it("devolve data e meta da lista paginada", async () => {
    const meta = { current_page: 1, last_page: 2, per_page: 15, total: 20, from: 1, to: 15 };

    expect(await unwrapPage<number>(jsonResponse({ data: [1, 2], meta }))).toEqual({ data: [1, 2], meta });
  });

  it("falha quando a resposta não é uma página", async () => {
    await expect(unwrapPage(jsonResponse({ message: "Erro." }, 500))).rejects.toThrow("Erro.");
  });
});

describe("toFieldErrors", () => {
  it("normaliza arrays e strings e ignora o resto", () => {
    expect(toFieldErrors({ a: ["x", "y"], b: "z", c: 3 })).toEqual({ a: ["x", "y"], b: ["z"] });
    expect(toFieldErrors([])).toEqual({});
    expect(toFieldErrors(null)).toEqual({});
  });
});
