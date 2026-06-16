<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    const PAGINATE_COUNT = 5;

    // Створюємо інвойс
    public function createInvoice(array $invoiceData): Invoice
    {
        // Рахуємо загальну суму інвойсу
        $invoiceData["gross_amount"] = $this->calculateGrossAmount($invoiceData);

        // Створюємо інвойс у базі даних
        return Invoice::create($invoiceData);
    }

    // Отримуємо список інвойсів
    public function getAllInvoices()
    {
        return Invoice::latest()->paginate(self::PAGINATE_COUNT);
    }

    // Отримуємо конкретний інвойс по локальному id
    public function getInvoiceById(int $id): Invoice
    {
        return Invoice::findOrFail($id);
    }

    // Оновлюємо інвойс
    public function updateInvoice(int $id, array $invoiceData): Invoice
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status !== Invoice::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Оновлювати можна тільки інвойси зі статусом pending.',
            ]);
        }

        $invoiceData["gross_amount"] = $this->calculateGrossAmount($invoiceData);
        $invoice->update($invoiceData);

        return $invoice;
    }

    // рахуємо загальну суму інвойсу
    private function calculateGrossAmount(array $invoiceData)
    {
        return $invoiceData['net_amount'] + $invoiceData['vat_amount'];
    }
}
