<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_date'          => 'required|date_format:d-m-Y',
            'transfer_amount'       => 'required|numeric',
            'bank_account'          => 'nullable|numeric',
            'payment_attachment'    => 'nullable|image',
        ];
    }
}
