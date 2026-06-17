<template>
  <BaseModal :model-value="modelValue" title="Новий інвойс" @update:model-value="onModalToggle">
    <form class="space-y-4" @submit.prevent="onSubmit">
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Номер</label>
        <Field
          name="number"
          type="text"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
        />
        <ErrorMessage name="number" class="mt-1 block text-xs text-rose-600" />
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Постачальник</label>
          <Field
            name="supplier_name"
            type="text"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
          />
          <ErrorMessage name="supplier_name" class="mt-1 block text-xs text-rose-600" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Податковий №</label>
          <Field
            name="supplier_tax_id"
            type="text"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
          />
          <ErrorMessage name="supplier_tax_id" class="mt-1 block text-xs text-rose-600" />
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Без ПДВ</label>
          <Field
            name="net_amount"
            type="number"
            step="0.01"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
          />
          <ErrorMessage name="net_amount" class="mt-1 block text-xs text-rose-600" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">ПДВ</label>
          <Field
            name="vat_amount"
            type="number"
            step="0.01"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
          />
          <ErrorMessage name="vat_amount" class="mt-1 block text-xs text-rose-600" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Разом</label>
          <input
            :value="grossAmount.toFixed(2)"
            type="text"
            readonly
            class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
          />
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Валюта</label>
          <Field
            name="currency"
            as="select"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
          >
            <option value="UAH">UAH</option>
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
          </Field>
          <ErrorMessage name="currency" class="mt-1 block text-xs text-rose-600" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Дата інвойсу</label>
          <Field
            name="issue_date"
            type="date"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
          />
          <ErrorMessage name="issue_date" class="mt-1 block text-xs text-rose-600" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Дата оплати</label>
          <Field
            name="due_date"
            type="date"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
          />
          <ErrorMessage name="due_date" class="mt-1 block text-xs text-rose-600" />
        </div>
      </div>

      <div class="flex justify-end gap-2 border-t pt-4">
        <button
          type="button"
          class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="onModalToggle(false)"
        >
          Скасувати
        </button>
        <button
          type="submit"
          :disabled="isSubmitting || !meta.valid"
          class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-gray-300"
        >
          {{ isSubmitting ? 'Збереження...' : 'Створити' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup lang="ts">
import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import type { CreateInvoicePayload } from '~/types/invoice';

defineProps<{ modelValue: boolean }>();

const emit = defineEmits<{
  'update:modelValue': [value: boolean];
  'created': [];
}>();

const api = useInvoicesApi();

function isDueAfterIssue(data: { issue_date: string; due_date: string }) {
  return data.due_date >= data.issue_date;
}

const schema = toTypedSchema(
  z.object({
    number: z.string().min(1, 'Вкажіть номер').max(255),
    supplier_name: z.string().min(1, 'Вкажіть назву постачальника').max(255),
    supplier_tax_id: z.string().min(1, 'Вкажіть податковий номер').max(255),
    net_amount: z.coerce.number({ invalid_type_error: 'Введіть число' }).positive('Сума має бути більшою за 0'),
    vat_amount: z.coerce.number({ invalid_type_error: 'Введіть число' }).min(0, 'ПДВ не може бути відʼємним'),
    currency: z.enum(['UAH', 'USD', 'EUR']),
    issue_date: z.string().min(1, 'Вкажіть дату інвойсу'),
    due_date: z.string().min(1, 'Вкажіть дату оплати'),
  }).refine(isDueAfterIssue, {
    message: 'Дата оплати не може бути раніше дати інвойсу',
    path: ['due_date'],
  }),
);

const { handleSubmit, values, isSubmitting, setErrors, resetForm, meta } = useForm({
  validationSchema: schema,
  initialValues: {
    number: '',
    supplier_name: '',
    supplier_tax_id: '',
    net_amount: 0,
    vat_amount: 0,
    currency: 'UAH',
    issue_date: '',
    due_date: '',
  },
});

function calculateGrossAmount() {
  const net = Number(values.net_amount) || 0;
  const vat = Number(values.vat_amount) || 0;
  return net + vat;
}

const grossAmount = computed(calculateGrossAmount);

const onSubmit = handleSubmit(async function (formValues) {
  try {
    const payload: CreateInvoicePayload = {
      ...formValues,
      status: 'pending',
    };
    await api.create(payload);
    emit('created');
    emit('update:modelValue', false);
    resetForm();
  } catch (e: any) {
    if (e?.data?.errors) {
      setErrors(e.data.errors);
    }
  }
});

function onModalToggle(open: boolean) {
  emit('update:modelValue', open);
  if (!open) resetForm();
}
</script>
