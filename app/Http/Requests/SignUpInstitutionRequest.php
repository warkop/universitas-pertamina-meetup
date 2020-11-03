<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignUpInstitutionRequest extends FormRequest
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
            'name'                  => 'required|regex:/^[a-zA-Z0-9\-\s]+$/|unique:institution,name',
            'email'                 => 'required|email|unique:user',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required|present',
            'package_id'            => [
                'required',
                Rule::exists('package', 'id')->where(function ($query) {
                    $query->where('package_type', 0);
                })
            ],
            'nationality_id'        => 'required|exists:nationality,id'
        ];
    }

    public function messages()
    {
        return [
            'nationality_id.required' => 'Negara wajib diisi',
        ];
    }
}
