import { AlertCircle } from "lucide-react";

import { Alert, AlertDescription } from "@/components/ui/alert";

interface FormErrorAlertProps {
  message: string;
}

export function FormErrorAlert({ message }: FormErrorAlertProps) {
  return (
    <Alert variant="destructive" role="alert" className="flex flex-col items-center text-center">
      <AlertCircle />
      <AlertDescription className="w-full text-center">{message}</AlertDescription>
    </Alert>
  );
}
