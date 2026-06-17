import type {
  Invoice,
  Paginated,
  CreateInvoicePayload,
  UpdateInvoicePayload,
} from '~/types/invoice';

export const useInvoicesApi = () => {
  const config = useRuntimeConfig();
  const baseURL = import.meta.server
    ? (config.apiBase as string)
    : config.public.apiBase;

  const list = (page = 1) =>
    $fetch<Paginated<Invoice>>('/invoices', {
      baseURL,
      query: { page },
    });

  const get = (id: number) =>
    $fetch<Invoice>(`/invoices/${id}`, { baseURL });

  const create = async (payload: CreateInvoicePayload) => {
    const res = await $fetch<{ message: string; data: Invoice }>('/invoices', {
      baseURL,
      method: 'POST',
      body: payload,
    });
    return res.data;
  };

  const update = async (id: number, payload: UpdateInvoicePayload) => {
    const res = await $fetch<{ message: string; data: Invoice }>(`/invoices/${id}`, {
      baseURL,
      method: 'PUT',
      body: payload,
    });
    return res.data;
  };

  return { list, get, create, update };
};
