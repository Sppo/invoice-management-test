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
        $invoice = $this->invoiceService->createInvoice($data_invoice);

        return response()->json([
            'message' => 'Інвойс успішно створено.',
            'data' => $invoice,
        ], 201);
    }

    // отримуємо список інвойсів
    public function index()
    {
        return response()->json($this->invoiceService->getAllInvoices());
    }

    // отримуємо конкретний інвойс
    public function show(int $id)
    {
        return response()->json($this->invoiceService->getInvoiceById($id));
    }

    // оновлюємо інвойс
    public function update(int $id, UpdateInvoiceRequest $updateInvoiceRequest)
    {
        $data_invoice = $updateInvoiceRequest->validated();
        $invoice = $this->invoiceService->updateInvoice($id, $data_invoice);

        return response()->json([
            'message' => 'Інвойс успішно оновлено.',
            'data' => $invoice,
        ]);
    }
}
