<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileMemberStoreRequest extends FormRequest
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
         'photo' => 'image',
         'name' => '',
         // 'email' => 'required',
         'desc' => '',
         'education.id' => 'exists:member_education,id',
         'education.degree_id' => 'exists:m_ac_degree,id|required_with:education.institution',
         'education.institution' => 'required_with:education.degree',
         'skill.*' => 'exists:skill,id',
         'department' => 'exists:department,id',
         'position' => 'required_with:department',
         'employee_id' => 'required_with:department',
         'orcid_id' => '',
         'scopus_id' => '',
         'website' => '',
      ];
   }
}
