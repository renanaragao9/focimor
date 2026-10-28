import type { SwipeAction } from "@/features/match/types/matchTypes";

export const SWIPE_X_THRESHOLD = 100;
export const SWIPE_Y_THRESHOLD = 120;
export const TAP_MOVEMENT_LIMIT = 6;
export const MAX_ROTATION_DEG = 15;
export const ROTATION_SPAN_PX = 300;

function clamp(value: number, min: number, max: number): number {
  return Math.min(Math.max(value, min), max);
}

export function resolveSwipe(dx: number, dy: number): SwipeAction | null {
  if (dy <= -SWIPE_Y_THRESHOLD && Math.abs(dy) > Math.abs(dx)) return "superlike";
  if (dx >= SWIPE_X_THRESHOLD) return "like";
  if (dx <= -SWIPE_X_THRESHOLD) return "dislike";
  return null;
}

export function rotationFor(dx: number): number {
  return clamp((dx / ROTATION_SPAN_PX) * MAX_ROTATION_DEG, -MAX_ROTATION_DEG, MAX_ROTATION_DEG);
}

export interface OverlayOpacities {
  like: number;
  dislike: number;
  superlike: number;
}

export function overlayOpacities(dx: number, dy: number): OverlayOpacities {
  const verticalWins = dy < 0 && Math.abs(dy) > Math.abs(dx);

  return {
    like: verticalWins ? 0 : clamp(dx / SWIPE_X_THRESHOLD, 0, 1),
    dislike: verticalWins ? 0 : clamp(-dx / SWIPE_X_THRESHOLD, 0, 1),
    superlike: verticalWins ? clamp(-dy / SWIPE_Y_THRESHOLD, 0, 1) : 0,
  };
}

export function isTap(dx: number, dy: number): boolean {
  return Math.abs(dx) + Math.abs(dy) <= TAP_MOVEMENT_LIMIT;
}

export function tapZone(relativeX: number): "previous" | "next" | "none" {
  if (relativeX < 0.35) return "previous";
  if (relativeX > 0.65) return "next";
  return "none";
}

export function stepPhoto(index: number, total: number, zone: "previous" | "next" | "none"): number {
  if (total <= 1 || zone === "none") return index;
  return clamp(zone === "next" ? index + 1 : index - 1, 0, total - 1);
}

export function exitOffset(action: SwipeAction): { x: number; y: number } {
  if (action === "like") return { x: 1000, y: 0 };
  if (action === "dislike") return { x: -1000, y: 0 };
  return { x: 0, y: -1000 };
}
