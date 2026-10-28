export type OngRole = "admin" | "member";
export type OngStatus = "pending" | "verified" | "rejected" | "suspended";
export type PanelTab = "dados" | "pets" | "caixinhas" | "posts" | "membros";

export interface MyOng {
  uuid: string;
  name: string;
  slug: string;
  cnpj: string | null;
  description: string | null;
  logo_url: string | null;
  email: string | null;
  phone: string | null;
  address: string | null;
  city: string | null;
  state: string | null;
  latitude: string | null;
  longitude: string | null;
  pix_key: string | null;
  donation_info: string | null;
  status: string;
  verified_at: string | null;
  my_role: OngRole | null;
}

export interface OngMember {
  id: number;
  name: string;
  email: string;
  role: OngRole;
  joined_at: string | null;
}

export interface OngProfileValues {
  description: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  state: string;
  latitude: string;
  longitude: string;
  pixKey: string;
  donationInfo: string;
}

export interface OngProfilePayload {
  description: string | null;
  email: string | null;
  phone: string | null;
  address: string | null;
  city: string | null;
  state: string | null;
  latitude: number | null;
  longitude: number | null;
  pix_key: string | null;
  donation_info: string | null;
}

export interface PostValues {
  title: string;
  body: string;
  publishedAt: string;
}

export interface PostPayload {
  title: string;
  body: string;
  published_at: string | null;
}

export interface CampaignValues {
  title: string;
  description: string;
  goalAmount: string;
  raisedAmount: string;
  endsAt: string;
  cancelled: boolean;
}

export interface CampaignPayload {
  title: string;
  description: string | null;
  goal_amount: number;
  raised_amount: number;
  ends_at: string | null;
  status: "active" | "completed" | "cancelled";
}

export interface MemberValues {
  email: string;
  role: OngRole;
}

export const PANEL_PER_PAGE = 12;
export const MAX_LOGO_BYTES = 2 * 1024 * 1024;
export const MAX_COVER_BYTES = 5 * 1024 * 1024;
export const IMAGE_TYPES = ["image/jpeg", "image/png", "image/webp"] as const;
export const ROLE_LABELS: Record<OngRole, string> = { admin: "Administrador", member: "Membro" };
export const STATUS_LABELS: Record<string, string> = { pending: "Em análise", verified: "Verificada", rejected: "Recusada", suspended: "Suspensa" };
