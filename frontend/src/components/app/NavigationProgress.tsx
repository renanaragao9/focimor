"use client";

import { usePathname, useSearchParams } from "next/navigation";
import { useEffect, useRef, useState } from "react";

export function NavigationProgress() {
  const pathname = usePathname();
  const search = useSearchParams().toString();
  const [width, setWidth] = useState(0);
  const [visible, setVisible] = useState(false);
  const timer = useRef<ReturnType<typeof setInterval>>(undefined);

  useEffect(() => {
    function start(event: MouseEvent) {
      if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      const anchor = (event.target as Element).closest("a");
      if (!anchor || (anchor.target && anchor.target !== "_self") || anchor.hasAttribute("download")) return;
      const url = new URL(anchor.href, location.href);
      if (url.origin !== location.origin || url.pathname + url.search === location.pathname + location.search) return;

      clearInterval(timer.current);
      setVisible(true);
      setWidth(10);
      timer.current = setInterval(() => setWidth((w) => w + (90 - w) * 0.1), 200);
    }

    document.addEventListener("click", start);
    return () => {
      document.removeEventListener("click", start);
      clearInterval(timer.current);
    };
  }, []);

  useEffect(() => {
    clearInterval(timer.current);
    const finish = setTimeout(() => setWidth(100));
    const hide = setTimeout(() => {
      setVisible(false);
      setWidth(0);
    }, 250);
    return () => {
      clearTimeout(finish);
      clearTimeout(hide);
    };
  }, [pathname, search]);

  return (
    <div role="progressbar" aria-hidden={!visible} className="pointer-events-none fixed inset-x-0 top-0 z-[100] h-0.5 transition-opacity duration-200" style={{ opacity: visible ? 1 : 0 }}>
      <div className="h-full bg-gradient-to-r from-brand to-sky transition-[width] duration-200 ease-out" style={{ width: `${width}%` }} />
    </div>
  );
}
