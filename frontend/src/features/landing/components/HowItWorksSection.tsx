import { PetPhoto } from "@/features/landing/components/PetPhoto";
import { IMAGES, type LandingImage } from "@/features/landing/utils/landingImages";
import { cn } from "@/lib/utils";

interface Flow {
  title: string;
  image: LandingImage;
  tone: string;
  steps: Array<{ title: string; text: string }>;
}

const FLOWS: Flow[] = [
  {
    title: "Para tutores",
    image: IMAGES.boxer,
    tone: "bg-brand text-brand-foreground",
    steps: [
      { title: "Cadastre seu pet", text: "Adicione fotos, raça, porte, objetivo (amizade ou cruzamento) e a ficha de saúde." },
      { title: "Deslize e dê match", text: "Curta, passe ou super curta. Quando os dois pets se curtem, é um match." },
      { title: "Converse e combine", text: "Fale com o tutor no chat e organizem o encontro dos pets." },
    ],
  },
  {
    title: "Para quem quer adotar",
    image: IMAGES.sinkCat,
    tone: "bg-mint text-white dark:text-background",
    steps: [
      { title: "Encontre um amigo", text: "Veja pets de ONGs e tutores que procuram um lar, perto de você." },
      { title: "Demonstre interesse", text: "Ao curtir, o responsável pelo pet recebe o seu pedido de adoção." },
      { title: "Converse e adote", text: "Com o aceite, o chat abre. Combine os detalhes e confirme a adoção." },
    ],
  },
];

export function HowItWorksSection() {
  return (
    <section id="como-funciona" className="scroll-mt-20 border-t bg-gradient-to-b from-sky-soft/60 to-background px-4 py-20 sm:px-6">
      <div className="mx-auto flex w-full max-w-6xl flex-col gap-10">
        <div className="flex max-w-2xl flex-col gap-3">
          <h2 className="text-3xl font-extrabold tracking-tight sm:text-4xl">Como funciona</h2>
          <p className="text-muted-foreground">Dois caminhos simples, o mesmo cuidado com os animais.</p>
        </div>
        <div className="grid gap-6 lg:grid-cols-2">
          {FLOWS.map((flow) => (
            <div key={flow.title} className="flex flex-col gap-5 overflow-hidden rounded-sm border bg-card pb-6">
              <PetPhoto image={flow.image} sizes="(min-width: 1024px) 560px, 100vw" className="aspect-[16/7]" />
              <h3 className="px-6 text-xl font-bold">{flow.title}</h3>
              <ol className="flex flex-col gap-5 px-6">
                {flow.steps.map((step, index) => (
                  <li key={step.title} className="flex gap-4">
                    <span className={cn("flex size-9 shrink-0 items-center justify-center rounded-full text-sm font-bold", flow.tone)}>{index + 1}</span>
                    <div>
                      <h4 className="font-semibold">{step.title}</h4>
                      <p className="text-sm text-muted-foreground">{step.text}</p>
                    </div>
                  </li>
                ))}
              </ol>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
