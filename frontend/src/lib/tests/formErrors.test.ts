import { describe, expect, it } from "vitest";

import { ApiError } from "@/lib/api/errors";
import { fieldErrorsFrom } from "@/lib/api/formErrors";

describe("fieldErrorsFrom", () => {
  it("mapeia campos da API para campos do formulário e ignora os demais", () => {
    const error = new ApiError("Dados inválidos.", 422, { applied_at: ["Data inválida."], name: ["Obrigatório."], other: ["x"] });

    expect(
      fieldErrorsFrom(error, [
        ["appliedAt", "applied_at"],
        ["name", "name"],
        ["batch", "batch"],
      ]),
    ).toEqual({ appliedAt: "Data inválida.", name: "Obrigatório." });
  });
});
