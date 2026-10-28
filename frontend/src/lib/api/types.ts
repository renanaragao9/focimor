export interface ApiSuccess<T> {
  status: "success";
  message: string;
  data: T;
}

export type ApiFieldErrors = Record<string, string[]>;

export interface ApiFailure {
  status?: "error";
  message: string;
  errors?: ApiFieldErrors | unknown[];
}

export type QueryScalar = string | number | boolean;
export type QueryValue = QueryScalar | readonly QueryScalar[] | null | undefined;
export type QueryParams = Record<string, QueryValue>;

export interface PageMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

export interface ApiPage<T> {
  data: T[];
  meta: PageMeta;
}
