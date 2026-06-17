<template>
  <div class="mx-auto max-w-6xl p-6">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Інвойси</h1>
      <button type="button"
        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        @click="createOpen = true">
        + Створити
      </button>
    </div>

    <div v-if="pending" class="py-12 text-center text-gray-500">
      Завантаження...
    </div>

    <div v-else-if="error" class="rounded-md border border-rose-200 bg-rose-50 p-4 text-rose-700">
      <p class="mb-2">Не вдалося завантажити список інвойсів.</p>
      <button type="button" class="text-sm font-medium underline hover:no-underline" @click="refresh()">
        Спробувати ще
      </button>
    </div>

    <div v-else-if="!data?.data?.length" class="py-12 text-center text-gray-500">
      Інвойсів поки немає.
    </div>

    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <NuxtLink v-for="invoice in data.data" :key="invoice.id" :to="`/invoices/${invoice.id}`"
        class="block rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-indigo-300 hover:shadow-md">
        <div class="mb-2 flex items-start justify-between">
          <span class="font-mono text-sm font-semibold text-gray-900">
            {{ invoice.number }}
          </span>
          <InvoiceStatusBadge :status="invoice.status" />
        </div>

        <p class="mb-3 text-sm text-gray-700">{{ invoice.supplier_name }}</p>

        <div class="flex items-end justify-between text-sm">
          <span class="text-lg font-semibold text-gray-900">
            {{ formatMoney(invoice.gross_amount, invoice.currency) }}
          </span>
          <span class="text-gray-500">
            до {{ formatDate(invoice.due_date) }}
          </span>
        </div>
      </NuxtLink>
    </div>

    <div v-if="data && data.last_page > 1" class="mt-6 flex items-center justify-center gap-3 text-sm">
      <button
        type="button"
        class="rounded-md border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
        :disabled="data.current_page <= 1"
        @click="goToPage(data.current_page - 1)"
      >
        Попередня
      </button>
      <span class="text-gray-600">
        Сторінка {{ data.current_page }} з {{ data.last_page }}
      </span>
      <button
        type="button"
        class="rounded-md border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
        :disabled="data.current_page >= data.last_page"
        @click="goToPage(data.current_page + 1)"
      >
        Наступна
      </button>
    </div>

    <InvoiceCreateModal v-model="createOpen" @created="onInvoiceCreated" />
  </div>
</template>

<script setup lang="ts">
const api = useInvoicesApi();
const createOpen = ref(false);
const page = ref(1);

const { data, pending, error, refresh } = await useAsyncData(
  'invoices-list',
  function () {
    return api.list(page.value);
  },
  { watch: [page] },
);

async function onInvoiceCreated() {
  page.value = 1;
  await refresh();
}

function goToPage(target: number) {
  if (!data.value) return;
  const last = data.value.last_page;
  if (target < 1 || target > last) return;
  page.value = target;
}


</script>
