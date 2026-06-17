<template>
  <BaseModal :model-value="modelValue" title="Редагування інвойсу" @update:model-value="onModalToggle">
    <form class="space-y-4" @submit.prevent="onSubmit">
      <p class="text-sm text-gray-500">
        Інвойс <span class="font-mono font-semibold text-gray-900">{{ invoice.number }}</span>
        від {{ formatDate(invoice.issue_date) }}
      </p>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Без ПДВ</label>
          <Field name="net_amount" type="number" step="0.01"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
          <ErrorMessage name="net_amount" class="mt-1 block text-xs text-rose-600" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">ПДВ</label>
          <Field name="vat_amount" type="number" step="0.01"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
          <ErrorMessage name="vat_amount" class="mt-1 block text-xs text-rose-600" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Разом</label>
          <input :value="grossAmount.toFixed(2)" type="text" readonly
            class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700" />
        </div>
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Дата оплати</label>
        <Field name="due_date" type="date" :min="invoice.issue_date.slice(0, 10)"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
        <ErrorMessage name="due_date" class="mt-1 block text-xs text-rose-600" />
      </div>

      <div class="flex justify-end gap-2 border-t pt-4">
        <button type="button"
          class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="onModalToggle(false)">
          Скасувати
        </button>
        <button type="submit" :disabled="isSubmitting || !meta.valid"
          class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-gray-300">
          {{ isSubmitting ? 'Збереження...' : 'Зберегти' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup lang="ts">
import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import type { Invoice, UpdateInvoicePayload } from '~/types/invoice';

const props = defineProps<{
  modelValue: boolean;
  invoice: Invoice;
}>();

const emit = defineEmits<{
  'update:modelValue': [value: boolean];
  'updated': [invoice: Invoice];
}>();

const api = useInvoicesApi();

function buildSchema(issueDate: string) {
  function isDueAfterIssue(data: { due_date: string }) {
    return data.due_date >= issueDate;
  }

  return toTypedSchema(
    z.object({
      net_amount: z.coerce.number({ invalid_type_error: 'Введіть число' }).positive('Сума має бути більшою за 0'),
      vat_amount: z.coerce.number({ invalid_type_error: 'Введіть число' }).min(0, 'ПДВ не може бути відʼємним'),
      due_date: z.string().min(1, 'Вкажіть дату оплати'),
    }).refine(isDueAfterIssue, {
      message: 'Дата оплати не може бути раніше дати інвойсу',
      path: ['due_date'],
    }),
  );
}

function buildInitialValues(invoice: Invoice) {
  return {
    net_amount: Number(invoice.net_amount),
    vat_amount: Number(invoice.vat_amount),
    due_date: invoice.due_date.slice(0, 10),
  };
}

const { handleSubmit, values, isSubmitting, setErrors, resetForm, meta } = useForm({
  validationSchema: buildSchema(props.invoice.issue_date.slice(0, 10)),
  initialValues: buildInitialValues(props.invoice),
});

watch(
  function () { return props.invoice; },
  function (newInvoice) {
    resetForm({ values: buildInitialValues(newInvoice) });
  },
);

watch(
  function () { return props.modelValue; },
  function (open) {
    if (open) {
      resetForm({ values: buildInitialValues(props.invoice) });
    }
  },
);

function calculateGrossAmount() {
  const net = Number(values.net_amount) || 0;
  const vat = Number(values.vat_amount) || 0;
  return net + vat;
}

const grossAmount = computed(calculateGrossAmount);

const onSubmit = handleSubmit(async function (formValues) {
  try {
    const payload: UpdateInvoicePayload = {
      net_amount: formValues.net_amount,
      vat_amount: formValues.vat_amount,
      due_date: formValues.due_date,
    };
    const updated = await api.update(props.invoice.id, payload);
    emit('updated', updated);
    emit('update:modelValue', false);
  } catch (e: any) {
    if (e?.data?.errors) {
      setErrors(e.data.errors);
    }
  }
});

function onModalToggle(open: boolean) {
  emit('update:modelValue', open);
}
</script>
