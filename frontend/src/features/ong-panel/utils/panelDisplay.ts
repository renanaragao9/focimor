import type { MyOng } from "@/features/ong-panel/types/ongPanelTypes";

export function isAdmin(ong: Pick<MyOng, "my_role">): boolean {
  return ong.my_role === "admin";
}

export function isVerified(ong: Pick<MyOng, "status">): boolean {
  return ong.status === "verified";
}

export function publishState(post: { published_at: string | null; is_published: boolean }): "published" | "scheduled" | "draft" {
  if (post.is_published) return "published";
  return post.published_at ? "scheduled" : "draft";
}
