<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegulationStoreRequest extends FormRequest
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
        $user = auth()->user();
        if ($user->type == 2) {
            $validationInstitutionID = 'required|exists:institution,id';
        } else {
            $validationInstitutionID = 'nullable';
        }
        return [
            'name' => 'required',
            'code' => 'nullable',
            'publish_date' => 'nullable|date_format:d-m-Y',
            'target' => 'required|between:0,2',
            'institutions' => 'required_if:target,2',
            'institution_id' => $validationInstitutionID
        ];
    }

    public function messages()
    {
        return [
            'institution_id.required' => 'Institusi wajib diisi',
        ];
    }
}
