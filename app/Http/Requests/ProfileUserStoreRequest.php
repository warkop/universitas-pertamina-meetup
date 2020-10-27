<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUserStoreRequest extends FormRequest
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
      $rule = [
         'name'        => 'required',
         'employee_id' => 'required',
         'nationality' => 'required|exists:nationality,id',
         'position'    => 'required',
      ];

      return $rule;
   }
}
