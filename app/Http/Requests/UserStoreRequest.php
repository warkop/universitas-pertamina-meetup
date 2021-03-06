<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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

      $url = explode('/',url()->full());


      if (!($request->filled(['status']))) {
         $rule = [
            'name'        => 'required',
            'employee_id' => 'required',
            'nationality' => 'required|exists:nationality,id',
            'position'    => 'required',
            'email'       => 'required|email|unique:user,email,'.end($url),
         ];
         if (is_numeric(end($url))) {
            $rule['email'] = 'required|email|unique:user,email,'.end($url);
         } else {
            $rule['email'] = 'required|email|unique:user,email';
         }
      } else {
         $rule = [
            'status'              => 'required|numeric|between:0,1',
         ];
      }

      return $rule;
   }
}
