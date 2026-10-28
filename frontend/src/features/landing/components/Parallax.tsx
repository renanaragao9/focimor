"use client";

import { useEffect, useRef, type ReactNode } from "react";

interface ParallaxProps {
  speed?: number;
  max?: number;
  className?: string;
  children: ReactNode;
}

export function Parallax({ speed = 0.08, max = 40, className, children }: ParallaxProps) {
  const track = useRef<HTMLDivElement>(null);
  const layer = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const trackEl = track.current;
    const layerEl = layer.current;
    if (!trackEl || !layerEl || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    let frame = 0;
    const update = () => {
      frame = 0;
      const rect = trackEl.getBoundingClientRect();
      if (rect.bottom < 0 || rect.top > window.innerHeight) return;
      const offset = (rect.top + rect.height / 2 - window.innerHeight / 2) * speed;
      layerEl.style.transform = `translate3d(0, ${Math.max(-max, Math.min(max, offset)).toFixed(1)}px, 0)`;
    };
    const onScroll = () => {
      if (!frame) frame = requestAnimationFrame(update);
    };

    update();
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    return () => {
      window.removeEventListener("scroll", onScroll);
      window.removeEventListener("resize", onScroll);
      if (frame) cancelAnimationFrame(frame);
    };
  }, [speed, max]);

  return (
    <div ref={track} className={className}>
      <div ref={layer} className="will-change-transform">
        {children}
      </div>
    </div>
  );
}
