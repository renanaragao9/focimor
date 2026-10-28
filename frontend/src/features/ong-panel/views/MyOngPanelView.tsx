import { notFound, redirect } from "next/navigation";

import { AppShell } from "@/components/app/AppShell";
import { CampaignsPanel } from "@/features/ong-panel/components/CampaignsPanel";
import { MembersPanel } from "@/features/ong-panel/components/MembersPanel";
import { PanelHeader } from "@/features/ong-panel/components/PanelHeader";
import { PanelTabs } from "@/features/ong-panel/components/PanelTabs";
import { PetsPanel } from "@/features/ong-panel/components/PetsPanel";
import { PostsPanel } from "@/features/ong-panel/components/PostsPanel";
import { ProfilePanel } from "@/features/ong-panel/components/ProfilePanel";
import { fetchMyOng, fetchOngMembers, fetchOngPanelCampaigns, fetchOngPanelPets, fetchOngPanelPosts } from "@/features/ong-panel/server/ongPanelServer";
import type { MyOng } from "@/features/ong-panel/types/ongPanelTypes";
import { isAdmin, isVerified } from "@/features/ong-panel/utils/panelDisplay";
import { panelHref, type PanelParams } from "@/features/ong-panel/utils/panelParams";
import { fetchPetCatalogs } from "@/features/pet/server/petServer";
import { ApiError } from "@/lib/api/errors";
import type { AuthUser } from "@/lib/auth/authTypes";

interface MyOngPanelViewProps {
  user: AuthUser;
  uuid: string;
  params: PanelParams;
}

async function loadOng(uuid: string): Promise<MyOng> {
  try {
    return await fetchMyOng(uuid);
  } catch (error) {
    if (error instanceof ApiError && (error.status === 404 || error.status === 403)) notFound();
    throw error;
  }
}

async function PanelContent({ ong, user, params }: { ong: MyOng; user: AuthUser; params: PanelParams }) {
  const canPublish = isVerified(ong);

  if (params.tab === "pets") {
    const [page, catalogs] = await Promise.all([fetchOngPanelPets(ong.uuid, params.page), fetchPetCatalogs()]);
    if (page.data.length === 0 && params.page > 1) redirect(panelHref(ong.uuid, { ...params, page: Math.max(page.meta.last_page, 1) }));
    return <PetsPanel ongUuid={ong.uuid} page={page} catalogs={catalogs} canPublish={canPublish} />;
  }

  if (params.tab === "caixinhas") {
    const page = await fetchOngPanelCampaigns(ong.uuid, params.page);
    if (page.data.length === 0 && params.page > 1) redirect(panelHref(ong.uuid, { ...params, page: Math.max(page.meta.last_page, 1) }));
    return <CampaignsPanel ongUuid={ong.uuid} page={page} canPublish={canPublish} />;
  }

  if (params.tab === "posts") {
    const page = await fetchOngPanelPosts(ong.uuid, params.page);
    if (page.data.length === 0 && params.page > 1) redirect(panelHref(ong.uuid, { ...params, page: Math.max(page.meta.last_page, 1) }));
    return <PostsPanel ongUuid={ong.uuid} page={page} canPublish={canPublish} />;
  }

  if (params.tab === "membros") {
    const members = await fetchOngMembers(ong.uuid);
    return <MembersPanel ongUuid={ong.uuid} members={members} canManage={isAdmin(ong)} currentEmail={user.email} />;
  }

  return <ProfilePanel key={JSON.stringify(ong)} ong={ong} />;
}

export async function MyOngPanelView({ user, uuid, params }: MyOngPanelViewProps) {
  const ong = await loadOng(uuid);

  return (
    <AppShell user={user}>
      <PanelHeader ong={ong} />
      <PanelTabs uuid={ong.uuid} active={params.tab} />
      <PanelContent ong={ong} user={user} params={params} />
    </AppShell>
  );
}
