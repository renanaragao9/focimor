import type { FeedPet, SwipeAction } from "@/features/match/types/matchTypes";
import { formatDecimal } from "@/lib/numbers";

export function distanceLabel(distanceKm: number | null): string | null {
  if (distanceKm === null) return null;
  if (distanceKm < 1) return "a menos de 1 km";
  return `a ${formatDecimal(distanceKm, 1)} km`;
}

export function actionLabel(action: SwipeAction): string {
  if (action === "like") return "Curtir";
  if (action === "dislike") return "Passar";
  return "Super curtir";
}

export function cardTags(pet: FeedPet): string[] {
  return [pet.size?.name, pet.is_vaccinated ? "Vacinado" : null, pet.is_neutered ? "Castrado" : null, pet.goal.name].filter((tag): tag is string => Boolean(tag));
}
