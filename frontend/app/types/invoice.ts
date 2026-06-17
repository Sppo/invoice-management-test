// тільки один тип
export type InvoiceStatus = 'pending' | 'approved' | 'rejected';

// точна структура даних яка прилітає з бекенду, щоб не було проблем з типами
export interface Invoice {
    id: number;
    number: string;
    supplier_name: string;
    supplier_tax_id: string;
    net_amount: string;
    vat_amount: string;
    gross_amount: string;
    currency: string;
    status: InvoiceStatus;
    issue_date: string;
    due_date: string;
    created_at: string;
    updated_at: string;
}

// дженерік для пагінації
export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
}

// payload для створення інвойсу
export interface CreateInvoicePayload {
    number: string;
    supplier_name: string;
    supplier_tax_id: string;
    net_amount: number;
    vat_amount: number;
    currency: string;
    status: InvoiceStatus;
    issue_date: string;
    due_date: string;
}

// payload для оновлення інвойсу
export interface UpdateInvoicePayload {
    net_amount: number;
    vat_amount: number;
    due_date: string;
}
