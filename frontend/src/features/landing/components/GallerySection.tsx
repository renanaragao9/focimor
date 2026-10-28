import { Parallax } from "@/features/landing/components/Parallax";
import { PetPhoto } from "@/features/landing/components/PetPhoto";
import { IMAGES } from "@/features/landing/utils/landingImages";

const TILES = [
  { image: IMAGES.malamute, className: "aspect-[4/5]", speed: 0.1 },
  { image: IMAGES.siamese, className: "aspect-square", speed: -0.08 },
  { image: IMAGES.silky, className: "aspect-[4/5]", speed: 0.14 },
  { image: IMAGES.laptopCat, className: "aspect-square", speed: -0.1 },
  { image: IMAGES.terrier, className: "aspect-[4/5]", speed: 0.08 },
] as const;

export function GallerySection() {
  return (
    <section className="overflow-hidden border-t bg-background px-4 py-20 sm:px-6">
      <div className="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <div className="flex flex-col gap-2 text-center">
          <h2 className="text-2xl font-extrabold tracking-tight sm:text-3xl">Cães e gatos de todos os jeitos</h2>
          <p className="text-muted-foreground">Seja qual for a raça ou o tamanho, sempre tem um par esperando.</p>
        </div>
        <div className="grid grid-cols-2 items-end gap-3 sm:grid-cols-3 lg:grid-cols-5">
          {TILES.map(({ image, className, speed }) => (
            <Parallax key={image.src} speed={speed}>
              <PetPhoto image={image} sizes="(min-width: 1024px) 220px, 50vw" className={`rounded-2xl shadow-md ${className}`} />
            </Parallax>
          ))}
        </div>
        <p className="text-center text-xs text-muted-foreground">Imagens ilustrativas.</p>
      </div>
    </section>
  );
}
