import type { Metadata } from "next";

export const SITE_NAME = "Focimor";
export const SITE_DESCRIPTION = "Dê match entre pets, adote um novo amigo e apoie ONGs de proteção animal.";

export function siteUrl(): string {
  return (process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000").replace(/\/$/, "");
}

export function absoluteUrl(path: string): string {
  return `${siteUrl()}${path}`;
}

export function clip(text: string | null | undefined, max = 160): string | undefined {
  const clean = (text ?? "").replace(/\s+/g, " ").trim();
  if (!clean) return undefined;
  return clean.length <= max ? clean : `${clean.slice(0, max - 1).trimEnd()}…`;
}

interface PageSeo {
  title: string;
  description?: string;
  path: string;
  image?: string | null;
  type?: "website" | "article";
  index?: boolean;
}

export function pageMetadata({ title, description, path, image, type = "website", index = true }: PageSeo): Metadata {
  const images = image ? [{ url: image, alt: title }] : undefined;

  return {
    title,
    description,
    alternates: { canonical: path },
    robots: index ? { index: true, follow: true } : { index: false, follow: false },
    openGraph: { type, locale: "pt_BR", siteName: SITE_NAME, title, description, url: path, images },
    twitter: { card: image ? "summary_large_image" : "summary", title, description, images: image ? [image] : undefined },
  };
}
