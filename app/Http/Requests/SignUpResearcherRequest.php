<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpResearcherRequest extends FormRequest
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
            'name'                  => 'required|regex:/^[a-zA-Z0-9\-\s]+$/|unique:member,name',
            'title_id'              => 'required|exists:title,id',
            'department_id'         => 'required|exists:department,id',
            'nationality_id'        => 'required|exists:nationality,id',
            'email'                 => 'required|email|unique:user',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required|present',
        ];
    }
}
