<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $packageIdValidation = 'nullable';
        if (!request()->department_id) {
            $packageIdValidation = [
                'required',
                Rule::exists('package', 'id')->where(function ($query) {
                    $query->where('package_type', 1);
                })
            ];
        }
        return [
            'name'                  => 'required|regex:/^[a-zA-Z0-9\-\s]+$/|unique:member,name',
            'title_id'              => 'required|exists:title,id',
            'department_id'         => 'nullable|exists:department,id',
            'nationality_id'        => 'required|exists:nationality,id',
            'email'                 => 'required|email|unique:user',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required|present',
            'package_id'            => $packageIdValidation
        ];
    }
}
