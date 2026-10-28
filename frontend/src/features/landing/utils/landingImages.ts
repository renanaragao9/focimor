export interface LandingImage {
  src: string;
  alt: string;
}

export const IMAGES = {
  boxer: { src: "https://images.dog.ceo/breeds/boxer/n02108089_4989.jpg", alt: "Cachorro boxer com a língua de fora" },
  terrier: { src: "https://images.dog.ceo/breeds/terrier-tibetan/n02097474_523.jpg", alt: "Cachorro peludo preto e branco com bandana laranja" },
  silky: { src: "https://images.dog.ceo/breeds/terrier-silky/n02097658_980.jpg", alt: "Cachorro pequeno de pelo dourado com coleira vermelha" },
  malamute: { src: "https://images.dog.ceo/breeds/malamute/n02110063_18782.jpg", alt: "Cachorro husky sentado na grama" },
  siamese: { src: "https://cdn2.thecatapi.com/images/MTY5NjU4NQ.jpg", alt: "Gato de olhos azuis olhando para cima" },
  whiteCat: { src: "https://cdn2.thecatapi.com/images/MTY2OTA1Ng.jpg", alt: "Gato branco de olhos arregalados" },
  laptopCat: { src: "https://cdn2.thecatapi.com/images/1tj.jpg", alt: "Gato laranja deitado sobre um notebook" },
  sinkCat: { src: "https://cdn2.thecatapi.com/images/8mh.jpg", alt: "Gato deitado de barriga para cima em uma pia" },
  goldenFace: { src: "https://images.dog.ceo/breeds/retriever-golden/n02099601_7916.jpg", alt: "Cachorro golden retriever deitado olhando para a câmera" },
  corgiBlueEyes: { src: "https://images.dog.ceo/breeds/corgi-cardigan/n02113186_694.jpg", alt: "Cachorro corgi de olhos azuis olhando para cima" },
  samoyedPuppies: { src: "https://images.dog.ceo/breeds/samoyed/n02111889_662.jpg", alt: "Dois filhotes brancos de samoieda brincando" },
  pomeranian: { src: "https://images.dog.ceo/breeds/pomeranian/n02112018_5697.jpg", alt: "Cachorro spitz creme sentado no chão de madeira" },
  beagleSnow: { src: "https://images.dog.ceo/breeds/beagle/n02088364_5123.jpg", alt: "Beagle sentado na escada coberta de neve" },
  shibaRunning: { src: "https://images.dog.ceo/breeds/shiba/kurosuke01.jpg", alt: "Cachorro shiba preto correndo entre folhas secas" },
} satisfies Record<string, LandingImage>;
