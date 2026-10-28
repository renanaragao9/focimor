export interface RegisterValues {
  name: string;
  email: string;
  password: string;
  confirmPassword: string;
}

export const EMPTY_REGISTER: RegisterValues = { name: "", email: "", password: "", confirmPassword: "" };

export interface RegisterPayload {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export const VERIFICATION_CODE_LENGTH = 6;
export const RESEND_COOLDOWN_SECONDS = 60;
