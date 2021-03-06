<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Member;

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
      $user = auth()->user();

      $data = Member::find($user->owner_id);

      if ($data->is_independent == false) {
          $validationDepartment = 'exists:department,id';
      } else {
          $validationDepartment = 'nullable';
      }
      return [
         // 'photo' => 'image',
         'name' => '',
         // 'email' => 'required',
         'desc' => '',
         'education.*.degree_id' => 'exists:academic_degree,id|required_with:education.*.institution',
         'education.*.institution' => 'required_with:education.*.degree_id',
         'education.*.year_start' => 'required_with:education.*.degree_id',
         'education.*.year_end' => 'required_with:education.*.degree_id',
         'publication.*.id' => '',
         'publication.*.title' => 'required_with:publication.*.publication_type_id, publication.*.author',
         'publication.*.publication_type_id' => 'required_with:publication.*.tittle, publication.*.author',
         'publication.*.name' => 'required_with:publication.*.tittle, publication.*.author',
         'publication.*.year' => 'required_with:publication.*.tittle, publication.*.author',
         'publication.*.author' => 'required_with:publication.*.tittle, publication.*.publication_type_id',
         // 'skill.*' => 'exists:skill,id',
         // 'interest.*' => 'exists:skill,id',
         'department' => $validationDepartment,
         'nationality' => 'exists:nationality,id',
         'position' => '',
         'employee_id' => '',
         'orcid_id' => '',
         'scopus_id' => '',
         'website' => '',
      ];
   }
}
