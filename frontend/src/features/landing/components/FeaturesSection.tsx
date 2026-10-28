import { Parallax } from "@/features/landing/components/Parallax";
import { PetPhoto } from "@/features/landing/components/PetPhoto";
import { IMAGES, type LandingImage } from "@/features/landing/utils/landingImages";

interface Feature {
  image: LandingImage;
  title: string;
  text: string;
}

const PARALLAX_SPEEDS = [0.06, -0.06, 0.1] as const;

const FEATURES: Feature[] = [
  {
    image: IMAGES.corgiBlueEyes,
    title: "Match entre pets",
    text: "Deslize pets de outros tutores, filtre por espécie, porte, sexo e distância e dê match quando os dois se curtirem. Perfeito para amizade e cruzamento responsável.",
  },
  {
    image: IMAGES.goldenFace,
    title: "Chat entre tutores",
    text: "Combinou o match? Converse direto com o tutor do outro pet, com mensagens em tempo quase real e aviso de não lidas.",
  },
  {
    image: IMAGES.samoyedPuppies,
    title: "Adoção responsável",
    text: "Demonstre interesse em pets para adoção. Quem cuida do pet aceita ou recusa, e só depois do aceite a conversa é liberada.",
  },
  {
    image: IMAGES.beagleSnow,
    title: "ONGs verificadas",
    text: "Conheça ONGs aprovadas pela equipe, veja os pets disponíveis, acompanhe as novidades e descubra como ajudar com Pix e doações.",
  },
  {
    image: IMAGES.pomeranian,
    title: "Ficha de saúde do pet",
    text: "Guarde vacinas com a próxima dose, histórico de saúde, peso e medidas, microchip e alergias de cada pet em um só lugar.",
  },
  {
    image: IMAGES.shibaRunning,
    title: "Painel para ONGs",
    text: "Administradores e membros cadastram pets para adoção, publicam posts com capa, mantêm os dados da ONG e gerenciam a equipe.",
  },
];

export function FeaturesSection() {
  return (
    <section id="recursos" className="scroll-mt-20 overflow-hidden border-t bg-background px-4 py-24 sm:px-6">
      <div className="mx-auto flex w-full max-w-6xl flex-col gap-10">
        <div className="flex max-w-2xl flex-col gap-3">
          <h2 className="text-3xl font-extrabold tracking-tight sm:text-4xl">Tudo o que o seu pet precisa para se conectar</h2>
          <p className="text-muted-foreground">Da primeira curtida à adoção confirmada, o Focimor cuida de cada etapa.</p>
        </div>
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {FEATURES.map(({ image, title, text }, index) => (
            <Parallax key={title} speed={PARALLAX_SPEEDS[index % 3]}>
              <article className="flex flex-col overflow-hidden rounded-sm border bg-card transition-shadow hover:shadow-md">
                <PetPhoto image={image} sizes="(min-width: 1024px) 384px, (min-width: 640px) 50vw, 100vw" className="aspect-4/3" />
                <div className="flex flex-col gap-2 p-5">
                  <h3 className="text-lg font-semibold">{title}</h3>
                  <p className="text-sm text-muted-foreground">{text}</p>
                </div>
              </article>
            </Parallax>
          ))}
        </div>
      </div>
    </section>
  );
}
