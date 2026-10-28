import { describe, expect, it } from "vitest";

import { campaignToValues, toCampaignPayload, validateCampaign, validateDonation } from "@/features/ong-panel/validators/panelValidators";
import { campaignHint, resolveCampaignStatus } from "@/features/ong/utils/campaignDisplay";

const BASE = { title: "Reforma", description: "", goalAmount: "1000", raisedAmount: "", endsAt: "", cancelled: false };

describe("campaign validators", () => {
  it("valida título e meta", () => {
    expect(validateCampaign(BASE)).toEqual({});
    expect(validateCampaign({ ...BASE, title: " ", goalAmount: "" })).toEqual({ title: "O título é obrigatório.", goalAmount: "Informe a meta (mínimo R$ 1,00)." });
    expect(validateCampaign({ ...BASE, goalAmount: "0,5" }).goalAmount).toBeDefined();
    expect(validateCampaign({ ...BASE, raisedAmount: "abc" }).raisedAmount).toBeDefined();
  });

  it("monta o payload com status calculado", () => {
    expect(toCampaignPayload({ ...BASE, goalAmount: "1.000,50", raisedAmount: "250,25" })).toMatchObject({
      goal_amount: 1000.5,
      raised_amount: 250.25,
      status: "active",
      description: null,
      ends_at: null,
    });
    expect(toCampaignPayload({ ...BASE, raisedAmount: "1000" }).status).toBe("completed");
    expect(toCampaignPayload({ ...BASE, raisedAmount: "1000", cancelled: true }).status).toBe("cancelled");
  });

  it("converte a caixinha da API para o formulário", () => {
    expect(campaignToValues({ title: "A", description: null, goal_amount: "1500.00", raised_amount: "250.50", ends_at: "05/12/2026", status: "cancelled" })).toEqual({
      title: "A",
      description: "",
      goalAmount: "1500",
      raisedAmount: "250,5",
      endsAt: "2026-12-05",
      cancelled: true,
    });
  });

  it("valida o valor da doação", () => {
    expect(validateDonation("150")).toBeUndefined();
    expect(validateDonation("0")).toBeDefined();
    expect(validateDonation("")).toBeDefined();
  });
});

describe("campaignDisplay", () => {
  it("resolve o status e o texto do progresso", () => {
    expect(resolveCampaignStatus(10, 100, false)).toBe("active");
    expect(resolveCampaignStatus(100, 100, false)).toBe("completed");
    expect(resolveCampaignStatus(100, 100, true)).toBe("cancelled");
    expect(campaignHint({ status: "cancelled", goal_amount: "100.00", remaining_amount: "100.00" })).toBe("Caixinha cancelada");
    expect(campaignHint({ status: "completed", goal_amount: "100.00", remaining_amount: "0.00" })).toContain("batida");
    expect(campaignHint({ status: "active", goal_amount: "100.00", remaining_amount: "40.00" })).toMatch(/^Faltam .*40,00 de .*100,00$/);
  });
});
