<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpportunityStoreRequest extends FormRequest
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
            'name'                  => 'required',
            'opportunity_type_id'   => 'exists:opportunity_type,id',
            'total_funding'         => 'numeric',
            'target'                => 'between:0,2',
            'institutions.*'        => 'required_if:target,2',
            'start_date'            => 'nullable|date_format:d-m-Y H:i',
            'end_date'              => 'nullable|date_format:d-m-Y H:i|after:start_date',
        ];
    }

    public function attributes()
    {
        return [
            'institutions.*' => 'Institution',
            'opportunity_type_id' => 'Opportunity Type',
        ];
    }
}
