import { Suspense } from "react";

import { JsonLd } from "@/components/JsonLd";
import { SITE_DESCRIPTION, SITE_NAME, absoluteUrl, pageMetadata } from "@/lib/seo";

import { FeaturesSection } from "@/features/landing/components/FeaturesSection";
import { FinalCta } from "@/features/landing/components/FinalCta";
import { GallerySection } from "@/features/landing/components/GallerySection";
import { HeroSection } from "@/features/landing/components/HeroSection";
import { HowItWorksSection } from "@/features/landing/components/HowItWorksSection";
import { SiteHeader } from "@/components/app/SiteHeader";
import { ShowcaseSection } from "@/features/landing/components/ShowcaseSection";
import { OngSection } from "@/features/landing/components/OngSection";

export const metadata = pageMetadata({ title: "Encontre o par ideal para o seu pet", description: "Dê match entre pets, adote um novo amigo e apoie ONGs de proteção animal.", path: "/" });

export default function Home() {
  return (
    <div className="flex flex-1 scroll-smooth flex-col">
      <JsonLd
        data={{
          "@context": "https://schema.org",
          "@graph": [
            { "@type": "WebSite", name: SITE_NAME, url: absoluteUrl("/"), description: SITE_DESCRIPTION, inLanguage: "pt-BR" },
            { "@type": "Organization", name: SITE_NAME, url: absoluteUrl("/") },
          ],
        }}
      />
      <SiteHeader />
      <main className="flex flex-1 flex-col">
        <HeroSection />
        <Suspense fallback={null}>
          <ShowcaseSection />
        </Suspense>
        <GallerySection />
        <FeaturesSection />
        <HowItWorksSection />
        <OngSection />
        <FinalCta />
      </main>
    </div>
  );
}
