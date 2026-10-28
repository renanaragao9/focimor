import type { MetadataRoute } from "next";

import { absoluteUrl } from "@/lib/seo";

export default function robots(): MetadataRoute.Robots {
  return {
    rules: {
      userAgent: "*",
      allow: "/",
      disallow: ["/bff/", "/boas-vindas", "/meus-pets", "/match", "/matches", "/adocoes", "/adocao/descobrir", "/minhas-ongs", "/verificar-email", "/ongs/cadastro/status"],
    },
    sitemap: absoluteUrl("/sitemap.xml"),
  };
}
