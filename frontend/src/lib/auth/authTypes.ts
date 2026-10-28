export interface AuthUser {
  id: number;
  name: string;
  email: string;
  avatar_url: string | null;
  latitude: string | null;
  longitude: string | null;
  email_verified_at: string | null;
  created_at: string | null;
}

export interface LoginCredentials {
  email: string;
  password: string;
}
