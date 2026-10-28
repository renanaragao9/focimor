"use client"

import { useTheme } from "next-themes"
import { Toaster as Sonner, type ToasterProps } from "sonner"
import { CircleCheckIcon, InfoIcon, TriangleAlertIcon, OctagonXIcon, Loader2Icon } from "lucide-react"

const Toaster = ({ ...props }: ToasterProps) => {
  const { theme = "system" } = useTheme()

  return (
    <Sonner
      theme={theme as ToasterProps["theme"]}
      className="toaster group"
      icons={{
        success: (
          <CircleCheckIcon className="size-6 text-success" />
        ),
        info: (
          <InfoIcon className="size-6 text-info" />
        ),
        warning: (
          <TriangleAlertIcon className="size-6 text-warning" />
        ),
        error: (
          <OctagonXIcon className="size-6 text-destructive" />
        ),
        loading: (
          <Loader2Icon className="size-6 animate-spin" />
        ),
      }}
      style={
        {
          "--normal-bg": "var(--popover)",
          "--normal-text": "var(--popover-foreground)",
          "--normal-border": "var(--border)",
          "--border-radius": "var(--radius-sm)",
          "--width": "min(26rem, calc(100vw - 2rem))",
        } as React.CSSProperties
      }
      toastOptions={{
        classNames: {
          toast: "cn-toast !gap-4 !border-l-4 !p-5 !text-base !font-semibold !shadow-2xl",
          success: "!border-l-success",
          error: "!border-l-destructive",
          warning: "!border-l-warning",
          info: "!border-l-info",
        },
      }}
      {...props}
    />
  )
}

export { Toaster }
