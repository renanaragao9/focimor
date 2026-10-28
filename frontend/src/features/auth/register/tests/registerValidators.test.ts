import { describe, expect, it } from "vitest";

import { EMPTY_REGISTER } from "@/features/auth/register/types/registerTypes";
import { isStrongPassword, isValidVerificationCode, maskEmail, passwordRules, toRegisterPayload, validateRegister } from "@/features/auth/register/validators/registerValidators";

const valid = { name: "Maria Tutora", email: "maria@example.com", password: "Senha123", confirmPassword: "Senha123" };

describe("passwordRules", () => {
  it("espelha as regras do backend (8 caracteres, maiúscula, minúscula e número)", () => {
    expect(passwordRules("Senha123").every((rule) => rule.met)).toBe(true);
    expect(isStrongPassword("senha123")).toBe(false);
    expect(isStrongPassword("SENHA123")).toBe(false);
    expect(isStrongPassword("SenhaSemNumero")).toBe(false);
    expect(isStrongPassword("Ab1")).toBe(false);
  });

  it("não exige símbolo", () => {
    expect(passwordRules("Senha123").map((rule) => rule.key)).toEqual(["length", "upper", "lower", "number"]);
  });
});

describe("validateRegister", () => {
  it("aceita dados válidos", () => {
    expect(validateRegister(valid)).toEqual({});
  });

  it("aponta todos os campos inválidos", () => {
    expect(Object.keys(validateRegister(EMPTY_REGISTER)).sort()).toEqual(["email", "name", "password"]);
  });

  it("exige confirmação igual à senha", () => {
    expect(validateRegister({ ...valid, confirmPassword: "Outra123" }).confirmPassword).toBe("As senhas não coincidem.");
  });

  it("recusa e-mail mal formado e nome curto", () => {
    expect(validateRegister({ ...valid, email: "sem-arroba" }).email).toBe("Informe um e-mail válido.");
    expect(validateRegister({ ...valid, name: " a " }).name).toBe("Informe seu nome.");
  });
});

describe("toRegisterPayload", () => {
  it("normaliza e usa os nomes de campo da API", () => {
    expect(toRegisterPayload({ ...valid, name: "  Maria Tutora ", email: " MARIA@Example.com " })).toEqual({
      name: "Maria Tutora",
      email: "maria@example.com",
      password: "Senha123",
      password_confirmation: "Senha123",
    });
  });
});

describe("verificação de e-mail", () => {
  it("mascara o e-mail", () => {
    expect(maskEmail("maria@example.com")).toBe("m***a@example.com");
    expect(maskEmail("ab@example.com")).toBe("ab@example.com");
    expect(maskEmail("sem-arroba")).toBe("sem-arroba");
  });

  it("aceita só códigos de 6 dígitos", () => {
    expect(isValidVerificationCode("123456")).toBe(true);
    expect(isValidVerificationCode("12345")).toBe(false);
    expect(isValidVerificationCode("12345a")).toBe(false);
    expect(isValidVerificationCode("1234567")).toBe(false);
  });
});
