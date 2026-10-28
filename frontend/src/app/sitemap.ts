import type { MetadataRoute } from "next";

import type { FeedPet } from "@/features/match/types/matchTypes";
import type { Ong, OngPost } from "@/features/ong/types/ongTypes";
import { apiPublicPage } from "@/lib/api/server";
import { absoluteUrl } from "@/lib/seo";

async function uuids<T extends { uuid: string }>(path: string): Promise<string[]> {
  try {
    const page = await apiPublicPage<T>(path, { query: { page: 1, per_page: 100 } }, 3600);
    return page.data.map((item) => item.uuid);
  } catch {
    return [];
  }
}

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const [ongs, posts, pets] = await Promise.all([uuids<Ong>("ongs"), uuids<OngPost>("posts"), uuids<FeedPet>("adoptable-pets")]);

  const fixed = [
    { path: "/", priority: 1 },
    { path: "/adocao", priority: 0.9 },
    { path: "/ongs", priority: 0.8 },
    { path: "/posts", priority: 0.7 },
    { path: "/ongs/cadastro", priority: 0.5 },
    { path: "/cadastro", priority: 0.4 },
    { path: "/entrar", priority: 0.3 },
  ].map(({ path, priority }) => ({ url: absoluteUrl(path), priority }));

  return [
    ...fixed,
    ...pets.map((uuid) => ({ url: absoluteUrl(`/adocao/${uuid}`), priority: 0.8 })),
    ...ongs.map((uuid) => ({ url: absoluteUrl(`/ongs/${uuid}`), priority: 0.7 })),
    ...posts.map((uuid) => ({ url: absoluteUrl(`/posts/${uuid}`), priority: 0.6 })),
  ];
}
