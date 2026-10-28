"use client";

import { useEffect, useImperativeHandle, useRef, useState, type PointerEvent, type Ref } from "react";

import { PetCardFace } from "@/features/match/components/PetCardFace";
import type { FeedPet, SwipeAction } from "@/features/match/types/matchTypes";
import { exitOffset, isTap, overlayOpacities, resolveSwipe, rotationFor, stepPhoto, tapZone } from "@/features/match/utils/swipeGesture";

export interface SwipeCardHandle {
  swipe: (action: SwipeAction) => void;
}

interface SwipeCardProps {
  pet: FeedPet;
  onSwiped: (action: SwipeAction) => void;
  ref?: Ref<SwipeCardHandle>;
}

interface Offset {
  x: number;
  y: number;
}

const EXIT_MS = 280;
const REST: Offset = { x: 0, y: 0 };

export function SwipeCard({ pet, onSwiped, ref }: SwipeCardProps) {
  const [offset, setOffset] = useState<Offset>(REST);
  const [isDragging, setIsDragging] = useState(false);
  const [exit, setExit] = useState<SwipeAction | null>(null);
  const [photoIndex, setPhotoIndex] = useState(0);
  const origin = useRef<Offset | null>(null);
  const onSwipedRef = useRef(onSwiped);

  useEffect(() => {
    onSwipedRef.current = onSwiped;
  });

  useEffect(() => {
    if (!exit) return;

    const timer = window.setTimeout(() => onSwipedRef.current(exit), EXIT_MS);
    return () => window.clearTimeout(timer);
  }, [exit]);

  useImperativeHandle(ref, () => ({
    swipe: (action) => setExit((current) => current ?? action),
  }));

  function handlePointerDown(event: PointerEvent<HTMLDivElement>): void {
    if (exit) return;

    event.currentTarget.setPointerCapture(event.pointerId);
    origin.current = { x: event.clientX, y: event.clientY };
    setIsDragging(true);
  }

  function handlePointerMove(event: PointerEvent<HTMLDivElement>): void {
    if (!origin.current) return;

    setOffset({ x: event.clientX - origin.current.x, y: event.clientY - origin.current.y });
  }

  function handlePointerUp(event: PointerEvent<HTMLDivElement>): void {
    if (!origin.current) return;

    const dx = event.clientX - origin.current.x;
    const dy = event.clientY - origin.current.y;
    origin.current = null;
    setIsDragging(false);

    const action = resolveSwipe(dx, dy);
    if (action) {
      setExit(action);
      return;
    }

    setOffset(REST);
    if (isTap(dx, dy)) {
      const bounds = event.currentTarget.getBoundingClientRect();
      setPhotoIndex((current) => stepPhoto(current, pet.photos.length, tapZone((event.clientX - bounds.left) / bounds.width)));
    }
  }

  function handlePointerCancel(): void {
    origin.current = null;
    setIsDragging(false);
    setOffset(REST);
  }

  const target = exit ? exitOffset(exit) : offset;
  const overlays = overlayOpacities(target.x, target.y);

  return (
    <div
      role="group"
      aria-label={`Cartão de ${pet.name}`}
      className="absolute inset-0 cursor-grab touch-none active:cursor-grabbing"
      style={{
        transform: `translate(${target.x}px, ${target.y}px) rotate(${rotationFor(target.x)}deg)`,
        transition: isDragging ? "none" : `transform ${EXIT_MS}ms ease-out`,
      }}
      onPointerDown={handlePointerDown}
      onPointerMove={handlePointerMove}
      onPointerUp={handlePointerUp}
      onPointerCancel={handlePointerCancel}
    >
      <PetCardFace pet={pet} photoIndex={photoIndex} overlays={overlays} priority />
    </div>
  );
}
