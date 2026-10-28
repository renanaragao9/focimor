import { describe, expect, it } from "vitest";

import { EMPTY_PRE_REGISTER, type PreRegisterValues } from "@/features/ong-register/types/ongRegisterTypes";
import { statusInfo } from "@/features/ong-register/utils/statusInfo";
import { toContactValue, toPreRegisterPayload, validateContact, validatePreRegister } from "@/features/ong-register/validators/ongRegisterValidators";
import { isValidCnpj, maskCnpj, maskPhone } from "@/lib/masks";

const VALID: PreRegisterValues = { ...EMPTY_PRE_REGISTER, name: "Patas do Bem", email: "contato@patas.org", phone: "85 99999-0000", city: "Fortaleza", state: "CE", lgpd: true };

describe("masks", () => {
  it("formata telefone e CNPJ", () => {
    expect(maskPhone("85999990000")).toBe("85 99999-0000");
    expect(maskPhone("8533330000")).toBe("85 3333-0000");
    expect(maskCnpj("11222333000181")).toBe("11.222.333/0001-81");
    expect(maskCnpj("112")).toBe("11.2");
  });

  it("valida os dígitos do CNPJ", () => {
    expect(isValidCnpj("11.222.333/0001-81")).toBe(true);
    expect(isValidCnpj("11.222.333/0001-82")).toBe(false);
    expect(isValidCnpj("11.111.111/1111-11")).toBe(false);
  });
});

describe("validatePreRegister", () => {
  it("aceita um formulário completo e o CNPJ é opcional", () => {
    expect(validatePreRegister(VALID)).toEqual({});
    expect(validatePreRegister({ ...VALID, cnpj: "11.222.333/0001-81" })).toEqual({});
  });

  it("aponta os campos obrigatórios e inválidos", () => {
    const errors = validatePreRegister({ ...EMPTY_PRE_REGISTER });
    expect(Object.keys(errors).sort()).toEqual(["city", "email", "lgpd", "name", "phone", "state"]);
    expect(validatePreRegister({ ...VALID, email: "x", phone: "123", state: "C", cnpj: "123" })).toMatchObject({
      email: expect.any(String),
      phone: expect.any(String),
      state: expect.any(String),
      cnpj: expect.any(String),
    });
  });

  it("monta o payload só com dígitos e vazios como null", () => {
    expect(toPreRegisterPayload({ ...VALID, email: " A@B.CO ", state: "ce", cnpj: "", description: " " })).toEqual({
      name: "Patas do Bem",
      email: "a@b.co",
      phone: "85999990000",
      cnpj: null,
      city: "Fortaleza",
      state: "CE",
      description: null,
    });
  });
});

describe("consulta de status", () => {
  it("valida e normaliza o contato", () => {
    expect(validateContact("email", "")).toBeDefined();
    expect(validateContact("email", "a@b.co")).toBeUndefined();
    expect(validateContact("phone", "85 99999-0000")).toBeUndefined();
    expect(validateContact("phone", "123")).toBeDefined();
    expect(toContactValue("email", " A@B.CO ")).toBe("a@b.co");
    expect(toContactValue("phone", "(85) 99999-0000")).toBe("85999990000");
  });

  it("descreve cada situação e tem fallback", () => {
    expect(statusInfo("pending").label).toBe("Recebido");
    expect(statusInfo("approved").label).toBe("Aprovado");
    expect(statusInfo("qualquer").label).toBe("Em análise");
  });
});
