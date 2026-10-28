"use client";

import { useState } from "react";

import { PetPhotosManager } from "@/features/pet/components/PetPhotosManager";
import type { PetPhoto } from "@/features/pet/types/petTypes";

interface PetPhotosPageManagerProps {
  petUuid: string;
  initialPhotos: PetPhoto[];
}

export function PetPhotosPageManager({ petUuid, initialPhotos }: PetPhotosPageManagerProps) {
  const [photos, setPhotos] = useState(initialPhotos);

  return <PetPhotosManager petUuid={petUuid} photos={photos} onChange={setPhotos} />;
}
