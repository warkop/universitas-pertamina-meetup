<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileChangeMailStoreRequest extends FormRequest
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
         'email' => 'email|required|unique:member,email|unique:institution,email',
      ];
   }
}
