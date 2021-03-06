<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendDataPaymentRequest extends FormRequest
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
            'token' => 'required|exists:payment_tokens,token',
            'payment_date' => 'required|date_format:d-m-Y',
            'bank_id' => 'required|exists:banks,id',
            'buyer' => 'required',
            'bank_account' => 'required',
            'transfer_amount' => 'required|numeric',
        ];
    }
}
