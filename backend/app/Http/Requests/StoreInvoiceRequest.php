<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:255', 'unique:invoices,number'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_tax_id' => ['required', 'string', 'max:255'],
            'net_amount' => ['required', 'numeric', 'gt:0'],
            'vat_amount' => ['required', 'numeric', 'gte:0'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['required', 'string', Rule::in([
                Invoice::STATUS_PENDING,
                Invoice::STATUS_APPROVED,
                Invoice::STATUS_REJECTED,
            ])],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Вкажіть номер інвойсу.',
            'number.string' => 'Номер інвойсу має бути текстом.',
            'number.max' => 'Номер інвойсу не може бути довшим за 255 символів.',
            'number.unique' => 'Інвойс з таким номером вже існує.',

            'supplier_name.required' => 'Вкажіть назву постачальника.',
            'supplier_name.string' => 'Назва постачальника має бути текстом.',
            'supplier_name.max' => 'Назва постачальника не може бути довшою за 255 символів.',

            'supplier_tax_id.required' => 'Вкажіть податковий номер постачальника.',
            'supplier_tax_id.string' => 'Податковий номер постачальника має бути текстом.',
            'supplier_tax_id.max' => 'Податковий номер постачальника не може бути довшим за 255 символів.',

            'net_amount.required' => 'Вкажіть суму без ПДВ.',
            'net_amount.numeric' => 'Сума без ПДВ має бути числом.',
            'net_amount.gt' => 'Сума без ПДВ має бути більшою за 0.',

            'vat_amount.required' => 'Вкажіть суму ПДВ.',
            'vat_amount.numeric' => 'Сума ПДВ має бути числом.',
            'vat_amount.gte' => 'Сума ПДВ не може бути відʼємною.',

            'currency.required' => 'Вкажіть валюту.',
            'currency.string' => 'Валюта має бути текстом.',
            'currency.size' => 'Валюта має складатися з 3 символів.',

            'status.required' => 'Вкажіть статус інвойсу.',
            'status.string' => 'Статус інвойсу має бути текстом.',
            'status.in' => 'Оберіть коректний статус інвойсу.',

            'issue_date.required' => 'Вкажіть дату інвойсу.',
            'issue_date.date' => 'Дата інвойсу має бути коректною датою.',

            'due_date.required' => 'Вкажіть дату оплати.',
            'due_date.date' => 'Дата оплати має бути коректною датою.',
            'due_date.after_or_equal' => 'Дата оплати не може бути раніше дати інвойсу.',
        ];
    }
}
