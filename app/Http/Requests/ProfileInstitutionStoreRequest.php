<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileInstitutionStoreRequest extends FormRequest
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
           // 'photo' => 'image',
           'name' => 'required',
           // 'email' => 'required',
           'country' => 'exists:nationality,id',
           'city' => '',
           'address' => 'required',
           'postal_code' => 'required_with:address',
           'phone' => 'required',
           'est' => 'required',
           'department.*.id' => '',
           'department.*.name' => 'required',
        ];
    }
}
