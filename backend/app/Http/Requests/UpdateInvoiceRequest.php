<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Отримуємо інвойс, щоб перевірити дату оплати
        $invoice = Invoice::findOrFail($this->route('id'));

        return [
            'net_amount' => ['required', 'numeric', 'gt:0'],
            'vat_amount' => ['required', 'numeric', 'gte:0'],
            'due_date' => ['required', 'date', 'after_or_equal:' . $invoice->issue_date->format('Y-m-d')],
        ];
    }

    public function messages(): array
    {
        return [
            'net_amount.required' => 'Вкажіть суму без ПДВ.',
            'net_amount.numeric' => 'Сума без ПДВ має бути числом.',
            'net_amount.gt' => 'Сума без ПДВ має бути більшою за 0.',

            'vat_amount.required' => 'Вкажіть суму ПДВ.',
            'vat_amount.numeric' => 'Сума ПДВ має бути числом.',
            'vat_amount.gte' => 'Сума ПДВ не може бути відʼємною.',

            'due_date.required' => 'Вкажіть дату оплати.',
            'due_date.date' => 'Дата оплати має бути коректною датою.',
            'due_date.after_or_equal' => 'Дата оплати не може бути раніше дати інвойсу.',
        ];
    }
}
