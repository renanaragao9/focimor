"use client"

import * as React from "react"
import { cn } from "cn"
import { Dialog as DialogPrimitive } from "radix-ui"

import { Button } from "@/components/ui/button"
import { XIcon } from "lucide-react"

function Dialog({
  ...props
}: React.ComponentProps<typeof DialogPrimitive.Root>) {
  return <DialogPrimitive.Root data-slot="dialog" {...props} />
}

function DialogTrigger({
  ...props
}: React.ComponentProps<typeof DialogPrimitive.Trigger>) {
  return <DialogPrimitive.Trigger data-slot="dialog-trigger" {...props} />
}

function DialogPortal({
  ...props
}: React.ComponentProps<typeof DialogPrimitive.Portal>) {
  return <DialogPrimitive.Portal data-slot="dialog-portal" {...props} />
}

function DialogClose({
  ...props
}: React.ComponentProps<typeof DialogPrimitive.Close>) {
  return <DialogPrimitive.Close data-slot="dialog-close" {...props} />
}

function DialogOverlay({
  className,
  ...props
}: React.ComponentProps<typeof DialogPrimitive.Overlay>) {
  return (
    <DialogPrimitive.Overlay
      data-slot="dialog-overlay"
      className={cn(
        "fixed inset-0 isolate z-50 bg-black/10 duration-100 data-open:animate-in data-open:fade-in-0 data-closed:animate-out data-closed:fade-out-0",
        className
      )}
      {...props}
    />
  )
}

function useVisualViewportFit(ref: React.RefObject<HTMLElement | null>) {
  React.useEffect(() => {
    const element = ref.current
    const viewport = window.visualViewport
    if (!element || !viewport) return

    const sync = () => {
      if (window.matchMedia("(min-width: 640px)").matches) {
        element.style.removeProperty("--vvh")
        element.style.removeProperty("--vvt")
        return
      }
      element.style.setProperty("--vvh", `${viewport.height}px`)
      element.style.setProperty("--vvt", `${viewport.offsetTop}px`)
    }

    sync()
    viewport.addEventListener("resize", sync)
    viewport.addEventListener("scroll", sync)
    return () => {
      viewport.removeEventListener("resize", sync)
      viewport.removeEventListener("scroll", sync)
    }
  }, [ref])
}

function DialogContent({
  className,
  children,
  showCloseButton = true,
  ...props
}: React.ComponentProps<typeof DialogPrimitive.Content> & {
  showCloseButton?: boolean
}) {
  const contentRef = React.useRef<HTMLDivElement>(null)
  useVisualViewportFit(contentRef)

  return (
    <DialogPortal>
      <DialogOverlay />
      <DialogPrimitive.Content
        ref={contentRef}
        data-slot="dialog-content"
        onInteractOutside={(event) => event.preventDefault()}
        className={cn(
          "fixed inset-x-0 top-(--vvt,0px) z-50 flex h-(--vvh,100dvh) w-full max-w-none flex-col gap-6 overflow-y-auto [--dc-gap:1.5rem] [--dc-pad:1rem] sm:[--dc-pad:1.5rem] rounded-none bg-popover p-4 text-sm text-popover-foreground ring-1 ring-foreground/10 duration-100 outline-none sm:inset-x-auto sm:top-1/2 sm:left-1/2 sm:h-auto sm:max-h-[90dvh] sm:min-h-72 sm:max-w-xl sm:-translate-1/2 sm:rounded-sm sm:p-6 data-open:animate-in data-open:fade-in-0 data-open:zoom-in-95 data-closed:animate-out data-closed:fade-out-0 data-closed:zoom-out-95",
          className
        )}
        {...props}
      >
        {children}
        {showCloseButton && (
          <div className="pointer-events-none sticky top-[calc(8px-var(--dc-pad))] z-30 order-first mt-[calc(8px-var(--dc-pad))] mb-[calc(var(--dc-pad)-8px-var(--dc-gap))] h-0">
            <DialogPrimitive.Close data-slot="dialog-close" asChild>
              <Button
                variant="ghost"
                className="pointer-events-auto absolute top-0 right-[calc(8px-var(--dc-pad))]"
                size="icon-sm"
              >
                <XIcon
                />
                <span className="sr-only">Close</span>
              </Button>
            </DialogPrimitive.Close>
          </div>
        )}
      </DialogPrimitive.Content>
    </DialogPortal>
  )
}

function DialogHeader({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="dialog-header"
      className={cn("sticky -top-(--dc-pad) z-20 -mx-4 -mt-4 flex flex-col gap-1.5 border-b bg-popover p-4  pr-14 sm:-mx-6 sm:-mt-6 sm:px-6", className)}
      {...props}
    />
  )
}

function DialogFooter({
  className,
  showCloseButton = false,
  children,
  ...props
}: React.ComponentProps<"div"> & {
  showCloseButton?: boolean
}) {
  return (
    <div
      data-slot="dialog-footer"
      className={cn(
        "sticky -bottom-(--dc-pad) z-20 mt-auto -mx-4 -mb-4 flex flex-col-reverse gap-2 rounded-none border-t bg-popover p-4 sm:-mx-6 sm:-mb-6 sm:rounded-b-sm sm:p-6 sm:py-4 sm:flex-row sm:justify-end",
        className
      )}
      {...props}
    >
      {children}
      {showCloseButton && (
        <DialogPrimitive.Close asChild>
          <Button variant="outline">Close</Button>
        </DialogPrimitive.Close>
      )}
    </div>
  )
}

function DialogTitle({
  className,
  ...props
}: React.ComponentProps<typeof DialogPrimitive.Title>) {
  return (
    <DialogPrimitive.Title
      data-slot="dialog-title"
      className={cn(
        "font-heading text-xl/tight  font-bold tracking-tight",
        className
      )}
      {...props}
    />
  )
}

function DialogDescription({
  className,
  ...props
}: React.ComponentProps<typeof DialogPrimitive.Description>) {
  return (
    <DialogPrimitive.Description
      data-slot="dialog-description"
      className={cn(
        "text-sm text-muted-foreground *:[a]:underline *:[a]:underline-offset-3 *:[a]:hover:text-foreground",
        className
      )}
      {...props}
    />
  )
}

export {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogOverlay,
  DialogPortal,
  DialogTitle,
  DialogTrigger,
}
