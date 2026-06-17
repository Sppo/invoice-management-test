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

  const create = (payload: CreateInvoicePayload) =>
    $fetch<Invoice>('/invoices', {
      baseURL,
      method: 'POST',
      body: payload,
    });

  const update = (id: number, payload: UpdateInvoicePayload) =>
    $fetch<Invoice>(`/invoices/${id}`, {
      baseURL,
      method: 'PUT',
      body: payload,
    });

  return { list, get, create, update };
};
