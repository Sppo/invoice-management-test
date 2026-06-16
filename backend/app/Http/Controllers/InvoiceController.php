<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    private InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    // створюємо інвойс
    public function store(StoreInvoiceRequest $request)
    {
        $data_invoice = $request->validated();
        return $this->invoiceService->createInvoice($data_invoice);
    }

    // отримуємо список інвойсів
    public function index()
    {
        return $this->invoiceService->getAllInvoices();
    }

    // отримуємо конкретний інвойс
    public function show(int $id)
    {
        return $this->invoiceService->getInvoiceById($id);
    }

    // оновлюємо інвойс
    public function update(int $id, UpdateInvoiceRequest $updateInvoiceRequest)
    {
        $data_invoice = $updateInvoiceRequest->validated();
        return $this->invoiceService->updateInvoice($id, $data_invoice);
    }
}
