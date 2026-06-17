<template>
  <div class="mx-auto max-w-4xl p-6">
    <NuxtLink to="/invoices" class="mb-4 inline-block text-sm text-indigo-600 hover:underline">
      ← Назад до списку
    </NuxtLink>

    <div v-if="pending" class="py-12 text-center text-gray-500">
      Завантаження...
    </div>

    <div v-else-if="error" class="rounded-md border border-rose-200 bg-rose-50 p-4 text-rose-700">
      <p v-if="error.statusCode === 404">Інвойс не знайдено.</p>
      <p v-else>Не вдалося завантажити інвойс.</p>
    </div>

    <div v-else-if="invoice" class="space-y-6">
      <div class="flex items-start justify-between rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <div>
          <div class="mb-1 flex items-center gap-3">
            <h1 class="font-mono text-2xl font-semibold">{{ invoice.number }}</h1>
            <InvoiceStatusBadge :status="invoice.status" />
          </div>
          <p class="text-gray-600">{{ invoice.supplier_name }}</p>
        </div>

        <button type="button"
          class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 cursor-pointer disabled:cursor-not-allowed disabled:bg-gray-300"
          :disabled="invoice.status !== 'pending'"
          :title="invoice.status !== 'pending' ? 'Редагувати можна тільки інвойси зі статусом «В очікуванні»' : ''"
          @click="editOpen = true">
          Редагувати
        </button>
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
          <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Постачальник</h2>
          <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
              <dt class="text-gray-500">Назва:</dt>
              <dd class="font-medium text-gray-900">{{ invoice.supplier_name }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-gray-500">Податковий №:</dt>
              <dd class="font-mono text-gray-900">{{ invoice.supplier_tax_id }}</dd>
            </div>
          </dl>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
          <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Дати</h2>
          <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
              <dt class="text-gray-500">Дата інвойсу:</dt>
              <dd class="font-medium text-gray-900">{{ formatDate(invoice.issue_date) }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-gray-500">Дата оплати:</dt>
              <dd class="font-medium text-gray-900">{{ formatDate(invoice.due_date) }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Суми</h2>
        <dl class="space-y-2 text-sm">
          <div class="flex justify-between">
            <dt class="text-gray-500">Без ПДВ:</dt>
            <dd class="font-medium text-gray-900">{{ formatMoney(invoice.net_amount, invoice.currency) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-500">ПДВ:</dt>
            <dd class="font-medium text-gray-900">{{ formatMoney(invoice.vat_amount, invoice.currency) }}</dd>
          </div>
          <div class="flex justify-between border-t border-gray-200 pt-2 text-base">
            <dt class="font-semibold text-gray-900">Разом:</dt>
            <dd class="font-semibold text-gray-900">{{ formatMoney(invoice.gross_amount, invoice.currency) }}</dd>
          </div>
        </dl>
      </div>

      <p class="text-right text-xs text-gray-500">
        Останнє оновлення: {{ formatDateTime(invoice.updated_at) }}
      </p>

      <InvoiceEditModal v-model="editOpen" :invoice="invoice" @updated="onInvoiceUpdated" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Invoice } from '~/types/invoice';

const route = useRoute();
const api = useInvoicesApi();
const id = Number(route.params.id);

const { data: invoice, pending, error } = await useAsyncData(`invoice-${id}`, function () {
  return api.get(id);
});

const editOpen = ref(false);

function onInvoiceUpdated(updated: Invoice) {
  invoice.value = updated;
}
</script>
