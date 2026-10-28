import type { FeedPet } from "@/features/match/types/matchTypes";

export interface Ong {
  uuid: string;
  name: string;
  slug: string;
  description: string | null;
  logo_url: string | null;
  email: string | null;
  phone: string | null;
  address: string | null;
  city: string | null;
  state: string | null;
  pix_key: string | null;
  donation_info: string | null;
  is_verified: boolean;
  adoptable_pets_count?: number;
}

export interface OngPost {
  uuid: string;
  title: string;
  body: string;
  cover_url: string | null;
  published_at: string | null;
  is_published: boolean;
  created_at: string | null;
  ong: { uuid: string; name: string; logo_url: string | null } | null;
  pet: { uuid: string; name: string } | null;
}

export type CampaignStatus = "active" | "completed" | "cancelled";

export interface OngCampaign {
  uuid: string;
  title: string;
  description: string | null;
  goal_amount: string;
  raised_amount: string;
  remaining_amount: string;
  progress_percent: number;
  status: CampaignStatus;
  status_label: string;
  ends_at: string | null;
  updated_at: string | null;
}

export type AdoptablePet = FeedPet;

export type OngTab = "pets" | "caixinhas" | "posts";

export const ONGS_PER_PAGE = 12;
export const ONG_PETS_PER_PAGE = 12;
export const POSTS_PER_PAGE = 9;
export const CAMPAIGNS_PER_PAGE = 6;
