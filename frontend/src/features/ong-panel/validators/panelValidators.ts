import { resolveCampaignStatus } from "@/features/ong/utils/campaignDisplay";
import type { CampaignPayload, CampaignValues, MemberValues, MyOng, OngProfilePayload, OngProfileValues, PostPayload, PostValues } from "@/features/ong-panel/types/ongPanelTypes";
import { brToIso } from "@/lib/dates";
import { parseDecimal, toDecimalInput } from "@/lib/numbers";

export type ProfileErrors = Partial<Record<keyof OngProfileValues, string>>;
export type PostErrors = Partial<Record<keyof PostValues, string>>;
export type MemberErrors = Partial<Record<keyof MemberValues, string>>;

export function parseCoordinate(value: string): number | null {
  const normalized = value.trim().replace(",", ".");
  if (!/^-?\d+(\.\d+)?$/.test(normalized)) return null;

  return Number(normalized);
}

const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export const PROFILE_API_FIELDS = [
  ["description", "description"],
  ["email", "email"],
  ["phone", "phone"],
  ["address", "address"],
  ["city", "city"],
  ["state", "state"],
  ["latitude", "latitude"],
  ["longitude", "longitude"],
  ["pixKey", "pix_key"],
  ["donationInfo", "donation_info"],
] as const;

export const POST_API_FIELDS = [
  ["title", "title"],
  ["body", "body"],
  ["publishedAt", "published_at"],
] as const;

export const MEMBER_API_FIELDS = [
  ["email", "email"],
  ["role", "role"],
] as const;

export function profileToValues(ong: MyOng): OngProfileValues {
  return {
    description: ong.description ?? "",
    email: ong.email ?? "",
    phone: ong.phone ?? "",
    address: ong.address ?? "",
    city: ong.city ?? "",
    state: ong.state ?? "",
    latitude: ong.latitude ?? "",
    longitude: ong.longitude ?? "",
    pixKey: ong.pix_key ?? "",
    donationInfo: ong.donation_info ?? "",
  };
}

function coordinateError(value: string, limit: number, label: string): string | undefined {
  if (value.trim() === "") return undefined;
  const parsed = parseCoordinate(value);
  if (parsed === null || Math.abs(parsed) > limit) return `${label} inválida.`;
  return undefined;
}

export function validateProfile(values: OngProfileValues): ProfileErrors {
  const errors: ProfileErrors = {};
  if (values.email.trim() && !EMAIL.test(values.email.trim())) errors.email = "Informe um e-mail válido.";
  if (values.state.trim() && values.state.trim().length !== 2) errors.state = "A UF deve ter 2 letras.";
  if (values.description.length > 5000) errors.description = "A descrição não pode ter mais de 5000 caracteres.";
  if (values.donationInfo.length > 5000) errors.donationInfo = "O texto não pode ter mais de 5000 caracteres.";
  const latitude = coordinateError(values.latitude, 90, "Latitude");
  const longitude = coordinateError(values.longitude, 180, "Longitude");
  if (latitude) errors.latitude = latitude;
  if (longitude) errors.longitude = longitude;
  return errors;
}

function orNull(value: string): string | null {
  const trimmed = value.trim();
  return trimmed === "" ? null : trimmed;
}

export function toProfilePayload(values: OngProfileValues): OngProfilePayload {
  return {
    description: orNull(values.description),
    email: orNull(values.email),
    phone: orNull(values.phone),
    address: orNull(values.address),
    city: orNull(values.city),
    state: values.state.trim() === "" ? null : values.state.trim().toUpperCase(),
    latitude: values.latitude.trim() === "" ? null : parseCoordinate(values.latitude),
    longitude: values.longitude.trim() === "" ? null : parseCoordinate(values.longitude),
    pix_key: orNull(values.pixKey),
    donation_info: orNull(values.donationInfo),
  };
}

export function postToValues(post: { title: string; body: string; published_at: string | null }): PostValues {
  return { title: post.title, body: post.body, publishedAt: brToIso(post.published_at?.slice(0, 10) ?? null) ?? "" };
}

export function validatePost(values: PostValues): PostErrors {
  const errors: PostErrors = {};
  if (!values.title.trim()) errors.title = "O título é obrigatório.";
  else if (values.title.trim().length > 255) errors.title = "O título não pode ter mais de 255 caracteres.";
  if (!values.body.trim()) errors.body = "O conteúdo é obrigatório.";
  else if (values.body.length > 20000) errors.body = "O conteúdo não pode ter mais de 20000 caracteres.";
  return errors;
}

export function toPostPayload(values: PostValues): PostPayload {
  return { title: values.title.trim(), body: values.body.trim(), published_at: values.publishedAt || null };
}

export function validateMember(values: MemberValues): MemberErrors {
  const errors: MemberErrors = {};
  if (!values.email.trim()) errors.email = "Informe o e-mail do membro.";
  else if (!EMAIL.test(values.email.trim())) errors.email = "Informe um e-mail válido.";
  return errors;
}

export type CampaignErrors = Partial<Record<keyof CampaignValues, string>>;

export const CAMPAIGN_API_FIELDS = [
  ["title", "title"],
  ["description", "description"],
  ["goalAmount", "goal_amount"],
  ["raisedAmount", "raised_amount"],
  ["endsAt", "ends_at"],
] as const;

export const EMPTY_CAMPAIGN_FORM: CampaignValues = { title: "", description: "", goalAmount: "", raisedAmount: "", endsAt: "", cancelled: false };

export function campaignToValues(campaign: { title: string; description: string | null; goal_amount: string; raised_amount: string; ends_at: string | null; status: string }): CampaignValues {
  return {
    title: campaign.title,
    description: campaign.description ?? "",
    goalAmount: toDecimalInput(campaign.goal_amount),
    raisedAmount: toDecimalInput(campaign.raised_amount),
    endsAt: brToIso(campaign.ends_at),
    cancelled: campaign.status === "cancelled",
  };
}

export function validateCampaign(values: CampaignValues): CampaignErrors {
  const errors: CampaignErrors = {};
  if (!values.title.trim()) errors.title = "O título é obrigatório.";
  else if (values.title.trim().length > 255) errors.title = "O título não pode ter mais de 255 caracteres.";
  if (values.description.length > 5000) errors.description = "A descrição não pode ter mais de 5000 caracteres.";
  const goal = parseDecimal(values.goalAmount);
  if (goal === null || goal < 1) errors.goalAmount = "Informe a meta (mínimo R$ 1,00).";
  if (values.raisedAmount.trim() !== "" && parseDecimal(values.raisedAmount) === null) errors.raisedAmount = "Informe um valor válido.";
  return errors;
}

export function toCampaignPayload(values: CampaignValues): CampaignPayload {
  const goal = parseDecimal(values.goalAmount) ?? 0;
  const raised = values.raisedAmount.trim() === "" ? 0 : (parseDecimal(values.raisedAmount) ?? 0);

  return {
    title: values.title.trim(),
    description: orNull(values.description),
    goal_amount: goal,
    raised_amount: raised,
    ends_at: values.endsAt || null,
    status: resolveCampaignStatus(raised, goal, values.cancelled),
  };
}

export function validateDonation(value: string): string | undefined {
  const parsed = parseDecimal(value);
  return parsed === null || parsed <= 0 ? "Informe um valor maior que zero." : undefined;
}
