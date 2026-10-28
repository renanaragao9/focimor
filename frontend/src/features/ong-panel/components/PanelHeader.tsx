import { Eye } from "lucide-react";
import Link from "next/link";

import { Button } from "@/components/ui/button";
import { LogoUploadButton } from "@/features/ong-panel/components/LogoUploadButton";
import { OngStatusBadge } from "@/features/ong-panel/components/OngStatusBadge";
import { isAdmin } from "@/features/ong-panel/utils/panelDisplay";
import { ROLE_LABELS, type MyOng } from "@/features/ong-panel/types/ongPanelTypes";
import { OngLogo } from "@/features/ong/components/OngLogo";
import { ongHref } from "@/features/ong/utils/ongParams";

export function PanelHeader({ ong }: { ong: MyOng }) {
  return (
    <section className="flex flex-col gap-4 rounded-sm border bg-gradient-to-br from-brand-soft via-background to-sky-soft p-6 sm:flex-row sm:items-center">
      <OngLogo name={ong.name} url={ong.logo_url} className="size-20 border-4 border-background shadow-md" />
      <div className="flex min-w-0 flex-1 flex-col gap-1">
        <div className="flex flex-wrap items-center gap-2">
          <h1 className="text-2xl font-extrabold tracking-tight">{ong.name}</h1>
          <OngStatusBadge status={ong.status} />
        </div>
        <p className="text-sm text-foreground/80">{ong.my_role ? ROLE_LABELS[ong.my_role] : "Membro"}</p>
      </div>
      <div className="grid w-full grid-cols-1 gap-2 sm:flex sm:w-auto">
        {isAdmin(ong) && <LogoUploadButton ongUuid={ong.uuid} />}
        <Button variant="outline" className="w-full bg-background sm:w-auto" asChild>
          <Link href={ongHref(ong.uuid)}>
            <Eye /> Ver página pública
          </Link>
        </Button>
      </div>
    </section>
  );
}
