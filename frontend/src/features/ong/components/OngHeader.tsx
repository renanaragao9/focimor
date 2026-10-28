import { HandHeart, Mail, MapPin, Phone } from "lucide-react";

import { OngLogo } from "@/features/ong/components/OngLogo";
import { VerifiedBadge } from "@/features/ong/components/VerifiedBadge";
import type { Ong } from "@/features/ong/types/ongTypes";
import { locationLabel, phoneHref } from "@/features/ong/utils/ongDisplay";

export function OngHeader({ ong }: { ong: Ong }) {
  const location = locationLabel(ong.city, ong.state);
  const hasDonation = Boolean(ong.pix_key || ong.donation_info);

  return (
    <section className="overflow-hidden rounded-sm border bg-card">
      <div className="flex flex-col gap-4 bg-gradient-to-br from-brand/30 via-brand-soft to-sky/30 p-6 sm:flex-row sm:items-center">
        <OngLogo name={ong.name} url={ong.logo_url} className="size-24 border-4 border-background shadow-md" />
        <div className="flex min-w-0 flex-col gap-2">
          <div className="flex flex-wrap items-center gap-2">
            <h1 className="text-2xl font-extrabold tracking-tight sm:text-3xl">{ong.name}</h1>
            {ong.is_verified && <VerifiedBadge />}
          </div>
          {ong.description && <p className="max-w-2xl text-sm text-foreground/80">{ong.description}</p>}
        </div>
      </div>

      <dl className="grid gap-4 p-6 sm:grid-cols-2">
        {location && <Info icon={MapPin} label="Local" value={[ong.address, location].filter(Boolean).join(", ")} />}
        {ong.email && <Info icon={Mail} label="E-mail" value={ong.email} href={`mailto:${ong.email}`} />}
        {ong.phone && <Info icon={Phone} label="Telefone" value={ong.phone} href={phoneHref(ong.phone)} />}
        {hasDonation && <Info icon={HandHeart} label="Como ajudar" value={[ong.pix_key && `Pix: ${ong.pix_key}`, ong.donation_info].filter(Boolean).join(" · ")} />}
      </dl>
    </section>
  );
}

interface InfoProps {
  icon: typeof MapPin;
  label: string;
  value: string;
  href?: string;
}

function Info({ icon: Icon, label, value, href }: InfoProps) {
  return (
    <div className="flex items-start gap-3">
      <span className="flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-soft">
        <Icon className="size-4 text-brand" aria-hidden="true" />
      </span>
      <div className="min-w-0">
        <dt className="text-xs text-muted-foreground">{label}</dt>
        <dd className="break-words text-sm font-medium">
          {href ? (
            <a href={href} className="hover:underline">
              {value}
            </a>
          ) : (
            value
          )}
        </dd>
      </div>
    </div>
  );
}
