import { Building2, Clock, MapPin, Newspaper, PawPrint } from "lucide-react";

import { ListingCard } from "@/features/landing/components/ListingCard";
import { PetDetailsLine } from "@/features/pet/components/PetDetailsLine";
import { adoptablePetHref } from "@/features/adoption/utils/adoptablePetParams";
import { ShowcaseRow } from "@/features/landing/components/ShowcaseRow";
import type { FeedPet } from "@/features/match/types/matchTypes";
import type { Ong, OngPost } from "@/features/ong/types/ongTypes";
import { excerpt, locationLabel, postDate } from "@/features/ong/utils/ongDisplay";
import { ongHref, postHref } from "@/features/ong/utils/ongParams";
import { genderLabel, petSubtitle, primaryPhoto } from "@/features/pet/utils/petDisplay";
import { apiPublicPage } from "@/lib/api/server";

const ROW_SIZE = 10;
const REVALIDATE_SECONDS = 300;

async function load<T>(path: string): Promise<T[]> {
  try {
    const page = await apiPublicPage<T>(path, { query: { per_page: ROW_SIZE } }, REVALIDATE_SECONDS);
    return page.data;
  } catch {
    return [];
  }
}

function Line({ icon: Icon, children }: { icon: typeof MapPin; children: React.ReactNode }) {
  return (
    <span className="flex items-start gap-1.5">
      <Icon className="mt-0.5 size-3.5 shrink-0" aria-hidden="true" />
      <span className="line-clamp-2 min-w-0 flex-1">{children}</span>
    </span>
  );
}

function OngCards({ ongs }: { ongs: Ong[] }) {
  return ongs.map((ong) => {
    const count = ong.adoptable_pets_count ?? 0;

    return (
      <ListingCard
        key={ong.uuid}
        title={ong.name}
        href={ongHref(ong.uuid)}
        image={ong.logo_url}
        imageAlt={`Logo de ${ong.name}`}
        fit="contain"
        placeholder={<Building2 className="size-10 text-muted-foreground" aria-hidden="true" />}
        tag={ong.is_verified ? "Verificada" : undefined}
        lines={[
          locationLabel(ong.city, ong.state) ? (
            <Line key="l" icon={MapPin}>
              {locationLabel(ong.city, ong.state)}
            </Line>
          ) : null,
          ong.description ? excerpt(ong.description, 90) : null,
        ].filter(Boolean)}
        footer={
          <p>
            <strong>{count}</strong> <span className="text-muted-foreground">{count === 1 ? "pet para adoção" : "pets para adoção"}</span>
          </p>
        }
      />
    );
  });
}

function PetCards({ pets }: { pets: FeedPet[] }) {
  return pets.map((pet) => {
    const photo = primaryPhoto(pet.photos);

    return (
      <ListingCard
        key={pet.uuid}
        title={pet.name}
        href={adoptablePetHref(pet.uuid)}
        image={photo?.url ?? null}
        imageAlt={`Foto de ${pet.name}`}
        placeholder={<PawPrint className="size-10 text-brand/70" aria-hidden="true" />}
        tag="Adoção"
        lines={[
          <Line key="s" icon={PawPrint}>
            {petSubtitle(pet)}
          </Line>,
          <PetDetailsLine key="d" parts={[genderLabel(pet.gender), pet.size?.name]} birthDate={pet.birth_date} />,
        ]}
        footer={
          pet.ong ? (
            <p className="truncate text-sm">
              {pet.ong.name}
              {locationLabel(pet.ong.city, pet.ong.state) && <span className="text-muted-foreground"> · {locationLabel(pet.ong.city, pet.ong.state)}</span>}
            </p>
          ) : undefined
        }
      />
    );
  });
}

function PostCards({ posts }: { posts: OngPost[] }) {
  return posts.map((post) => (
    <ListingCard
      key={post.uuid}
      title={post.title}
      href={postHref(post.uuid)}
      image={post.cover_url}
      imageAlt={post.title}
      placeholder={<Newspaper className="size-10 text-sky/60" aria-hidden="true" />}
      tag={post.ong?.name ?? "Focimor"}
      lines={[excerpt(post.body, 110)]}
      footer={postDate(post.published_at) ? <Line icon={Clock}>{postDate(post.published_at)}</Line> : undefined}
    />
  ));
}

export async function ShowcaseSection() {
  const [ongs, pets, posts] = await Promise.all([load<Ong>("ongs"), load<FeedPet>("adoptable-pets"), load<OngPost>("posts")]);

  if (ongs.length + pets.length + posts.length === 0) return null;

  return (
    <section className="border-t bg-background px-4 pb-4 pt-12 sm:px-6">
      <div className="mx-auto w-full max-w-6xl">
        {pets.length > 0 && (
          <ShowcaseRow title="Adoção" href="/adocao">
            <PetCards pets={pets} />
          </ShowcaseRow>
        )}
        {ongs.length > 0 && (
          <ShowcaseRow title="ONGs" href="/ongs">
            <OngCards ongs={ongs} />
          </ShowcaseRow>
        )}
        {posts.length > 0 && (
          <ShowcaseRow title="Novidades" href="/posts">
            <PostCards posts={posts} />
          </ShowcaseRow>
        )}
      </div>
    </section>
  );
}
