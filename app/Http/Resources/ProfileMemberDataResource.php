<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileMemberDataResource extends JsonResource
{
   /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
   public function toArray($request)
   {
      $data = [
         'id'                 => $this->id,
         'name'               => $this->name,
         'desc'               => $this->desc,
         'education'          => [
            'id' => $this->memberEducation['id'],
            'degree' => [
               'id'   => $this->memberEducation['MAcDegree']['id'],
               'name' => $this->memberEducation['MAcDegree']['name'],
            ],
            'institution' => $this->memberEducation['institution_name'],
         ],
         'department'         => [
            'id'   => $this->department->id,
            'nme'  => $this->department->nme,
         ],
         'department'         => [
            'id'    => $this->department['institution']['id'],
            'name'  => $this->department['institution']['name'],
         ],
         'skill'              => [],
         'position'           => $this->position,
         'employee_id'        => $this->employee_number,
         'orcid_id'           => $this->orcid_id,
         'scopus_id'          => $this->scopus_id,
         'web'                => $this->web,
         'path_photo'         => $this->path_photo,
         'updated_at'         => date('d-m-Y', strtotime($this->updated_at)),
      ];

      foreach ($this->memberSkill as $key => $value) {
         $data['skill'][] = [
            'id'       => $value->id,
            'name'     => $value->name,
            'type'     => $value->type,
         ];
      }

      return $data;
   }
}
