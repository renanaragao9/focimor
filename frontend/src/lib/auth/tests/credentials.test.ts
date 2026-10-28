import { describe, expect, it } from "vitest";

import { resolveRedirect, validateCredentials } from "@/lib/auth/credentials";

describe("validateCredentials", () => {
  it("aceita e-mail válido e senha preenchida", () => {
    expect(validateCredentials({ email: " voce@email.com ", password: "x" })).toEqual({});
  });

  it("exige e-mail e senha", () => {
    expect(validateCredentials({ email: "", password: "" })).toEqual({ email: "Informe seu e-mail.", password: "Informe sua senha." });
    expect(validateCredentials({ email: "   ", password: "x" }).email).toBe("Informe seu e-mail.");
  });

  it("recusa e-mail mal formado", () => {
    expect(validateCredentials({ email: "sem-arroba", password: "x" }).email).toBe("Informe um e-mail válido.");
    expect(validateCredentials({ email: "a@b", password: "x" }).email).toBe("Informe um e-mail válido.");
  });
});

describe("resolveRedirect", () => {
  const fallback = "/boas-vindas";

  it("aceita caminhos da própria área", () => {
    expect(resolveRedirect("/boas-vindas", fallback, ["/boas-vindas"])).toBe("/boas-vindas");
    expect(resolveRedirect("/boas-vindas?aba=pets", fallback, ["/boas-vindas"])).toBe("/boas-vindas?aba=pets");
    expect(resolveRedirect("/pets/10", fallback, ["/boas-vindas", "/pets"])).toBe("/pets/10");
  });

  it("não deixa o redirect levar a outra área nem a outro site", () => {
    expect(resolveRedirect("/admin", fallback, ["/boas-vindas"])).toBe(fallback);
    expect(resolveRedirect("/boas-vindas-fake", fallback, ["/boas-vindas"])).toBe(fallback);
    expect(resolveRedirect("https://mal.com", fallback, ["/boas-vindas"])).toBe(fallback);
    expect(resolveRedirect("//mal.com/boas-vindas", fallback, ["/boas-vindas"])).toBe(fallback);
  });

  it("usa o destino padrão quando não há redirect", () => {
    expect(resolveRedirect(null, fallback, "/boas-vindas")).toBe(fallback);
    expect(resolveRedirect(undefined, fallback, "/boas-vindas")).toBe(fallback);
    expect(resolveRedirect("", fallback, "/boas-vindas")).toBe(fallback);
  });
});
