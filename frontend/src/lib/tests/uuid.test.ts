import { describe, expect, it } from "vitest";

import { isUuid } from "@/lib/uuid";

describe("isUuid", () => {
  it("aceita só UUID completo", () => {
    expect(isUuid("3f2b8c1e-4d5a-4e6f-8a9b-0c1d2e3f4a5b")).toBe(true);
    expect(isUuid("3F2B8C1E-4D5A-4E6F-8A9B-0C1D2E3F4A5B")).toBe(true);
    expect(isUuid("3f2b8c1e-4d5a-4e6f-8a9b-0c1d2e3f4a5")).toBe(false);
    expect(isUuid("3f2b8c1e-4d5a-4e6f-8a9b-0c1d2e3f4a5b/../x")).toBe(false);
    expect(isUuid("")).toBe(false);
  });
});
