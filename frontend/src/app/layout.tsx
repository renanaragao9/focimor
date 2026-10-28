import type { Metadata, Viewport } from "next";
import { Suspense } from "react";
import { NavigationProgress } from "@/components/app/NavigationProgress";
import { SiteFooter } from "@/components/app/SiteFooter";
import { ThemeProvider } from "@/components/ThemeProvider";
import { Toaster } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { SITE_DESCRIPTION, SITE_NAME, siteUrl } from "@/lib/seo";
import "./globals.css";

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl()),
  applicationName: SITE_NAME,
  title: { default: "Focimor | Encontre o par ideal para o seu pet", template: "Focimor | %s" },
  description: SITE_DESCRIPTION,
  keywords: ["adoção de pets", "adotar cachorro", "adotar gato", "ONGs de proteção animal", "match de pets", "doação para ONGs"],
  openGraph: { type: "website", locale: "pt_BR", siteName: SITE_NAME, title: "Focimor | Encontre o par ideal para o seu pet", description: SITE_DESCRIPTION },
  twitter: { card: "summary", title: "Focimor", description: SITE_DESCRIPTION },
};

export const viewport: Viewport = {
  width: "device-width",
  initialScale: 1,
  viewportFit: "cover",
};

export default function RootLayout({ children }: LayoutProps<"/">) {
  return (
    <html lang="pt-BR" className="h-full antialiased" suppressHydrationWarning>
      <body className="min-h-full flex flex-col">
        <ThemeProvider attribute="class" defaultTheme="system" enableSystem disableTransitionOnChange>
          <Suspense>
            <NavigationProgress />
          </Suspense>
          <TooltipProvider>{children}</TooltipProvider>
          <SiteFooter />
          <Toaster />
        </ThemeProvider>
      </body>
    </html>
  );
}
