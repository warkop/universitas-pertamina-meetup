<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstitutionStoreRequest extends FormRequest
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
      $request = app('request');

      if (!($request->filled(['status']))) {
         return [
            'name'  => 'required',
            'email' => 'email|nullable',
         ];
      } else {
         return [
            'status'              => 'required|numeric|between:0,1',
         ];
      }
   }
}
