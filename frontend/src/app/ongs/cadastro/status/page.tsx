import type { Metadata } from "next";

import { RegisterStatusView } from "@/features/ong-register/views/RegisterStatusView";

export const metadata: Metadata = { title: "Acompanhe seu pré-cadastro", robots: { index: false, follow: false } };

export default function OngRegisterStatusPage() {
  return <RegisterStatusView />;
}
