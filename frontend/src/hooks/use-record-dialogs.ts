"use client";

import { useState } from "react";

export function useRecordDialogs<T>() {
  const [formOpen, setFormOpen] = useState(false);
  const [formItem, setFormItem] = useState<T | null>(null);
  const [formKey, setFormKey] = useState("new-0");
  const [itemToDelete, setItemToDelete] = useState<T | null>(null);

  function openCreate(): void {
    setFormItem(null);
    setFormKey(`new-${Date.now()}`);
    setFormOpen(true);
  }

  function openEdit(item: T, key: string | number): void {
    setFormItem(item);
    setFormKey(`edit-${key}`);
    setFormOpen(true);
  }

  return { formOpen, setFormOpen, formItem, formKey, itemToDelete, setItemToDelete, openCreate, openEdit };
}
