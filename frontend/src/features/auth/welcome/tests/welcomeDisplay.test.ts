import { describe, expect, it } from "vitest";

import { firstName, initials } from "@/features/auth/welcome/utils/welcomeDisplay";

describe("welcomeDisplay", () => {
  it("usa o primeiro nome na saudação", () => {
    expect(firstName("Maria da Silva")).toBe("Maria");
    expect(firstName("  João  ")).toBe("João");
  });

  it("gera iniciais do primeiro e do último nome", () => {
    expect(initials("Maria da Silva")).toBe("MS");
    expect(initials("joão")).toBe("J");
    expect(initials("  ana  maria  souza ")).toBe("AS");
    expect(initials("")).toBe("");
  });
});
