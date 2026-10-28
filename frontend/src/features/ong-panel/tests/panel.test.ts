import { describe, expect, it } from "vitest";

import { isAdmin, isVerified, publishState } from "@/features/ong-panel/utils/panelDisplay";
import { panelHref, parsePanelParams } from "@/features/ong-panel/utils/panelParams";
import { parseCoordinate, toPostPayload, toProfilePayload, validateMember, validatePost, validateProfile } from "@/features/ong-panel/validators/panelValidators";

const EMPTY_PROFILE = { description: "", email: "", phone: "", address: "", city: "", state: "", latitude: "", longitude: "", pixKey: "", donationInfo: "" };

describe("panelParams", () => {
  it("lê aba e página", () => {
    expect(parsePanelParams({})).toEqual({ tab: "dados", page: 1 });
    expect(parsePanelParams({ aba: "membros", page: "2" })).toEqual({ tab: "membros", page: 2 });
    expect(parsePanelParams({ aba: "x" }).tab).toBe("dados");
  });

  it("monta href", () => {
    expect(panelHref("u1")).toBe("/minhas-ongs/u1");
    expect(panelHref("u1", { tab: "posts", page: 2 })).toBe("/minhas-ongs/u1?aba=posts&page=2");
  });
});

describe("panelDisplay", () => {
  it("papel, verificação e situação do post", () => {
    expect(isAdmin({ my_role: "admin" })).toBe(true);
    expect(isAdmin({ my_role: "member" })).toBe(false);
    expect(isVerified({ status: "verified" })).toBe(true);
    expect(publishState({ published_at: "01/01/2026 00:00:00", is_published: true })).toBe("published");
    expect(publishState({ published_at: "01/01/2099 00:00:00", is_published: false })).toBe("scheduled");
    expect(publishState({ published_at: null, is_published: false })).toBe("draft");
  });
});

describe("panelValidators", () => {
  it("aceita coordenadas negativas", () => {
    expect(parseCoordinate("-3,7319")).toBe(-3.7319);
    expect(parseCoordinate("abc")).toBeNull();
  });

  it("valida o perfil", () => {
    expect(validateProfile(EMPTY_PROFILE)).toEqual({});
    expect(validateProfile({ ...EMPTY_PROFILE, email: "x", state: "CEA", latitude: "99" })).toEqual({
      email: "Informe um e-mail válido.",
      state: "A UF deve ter 2 letras.",
      latitude: "Latitude inválida.",
    });
  });

  it("monta o payload do perfil com null nos vazios", () => {
    expect(toProfilePayload({ ...EMPTY_PROFILE, state: "ce", latitude: "-3.7" })).toMatchObject({ description: null, state: "CE", latitude: -3.7, longitude: null });
  });

  it("valida post e membro", () => {
    expect(validatePost({ title: " ", body: "", publishedAt: "" })).toEqual({ title: "O título é obrigatório.", body: "O conteúdo é obrigatório." });
    expect(toPostPayload({ title: " a ", body: "b", publishedAt: "" })).toEqual({ title: "a", body: "b", published_at: null });
    expect(validateMember({ email: "", role: "member" }).email).toBeDefined();
    expect(validateMember({ email: "a@b.co", role: "admin" })).toEqual({});
  });
});
