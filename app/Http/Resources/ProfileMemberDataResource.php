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
         'title'              =>  [
            'id'   => $this->title->id??null,
            'name'  => $this->title->name??null,
         ],
         'name'               => $this->name,
         'email'              => $this->email,
         'desc'               => $this->desc,
         'education'          => [],
         'department'         => [
            'id'    => $this->department['id'],
            'name'  => $this->department['name'],
            'institution' =>[
               'id'    => $this->department['institution']['id'],
               'name'  => $this->department['institution']['name'],
            ]
         ],
         'skill'              => [],
         'research_interest'  => [],
         'position'           => $this->position,
         'nationality'         => [
            'id'    => $this->nationality ['id'],
            'name'  => $this->nationality ['name'],
         ],
         'publication'        => [],
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
            'input'     => $value->input,
         ];
      }

      foreach ($this->memberResearchInterest as $key => $value) {
         $data['research_interest'][] = [
            'id'       => $value->id,
            'name'     => $value->name,
            'type'     => $value->type,
            'total_funding'     => $value->total_funding,
            'end_date'     => $value->end_date,
            'input'     => $value->input,
         ];
      }

      foreach ($this->publication as $key => $value) {
         $data['publication'][] = [
            'id'       => $value->id,
            'title'     => $value->title,
            'name'     => $value->name,
            'year'     => $value->year,
            'author'     => $value->author,
            'publication_type' => [
               'id'    => $value->publicationType->id??null,
               'name'    => $value->publicationType->name??null,
            ]
         ];
      }

      foreach ($this->memberEducation as $key => $value) {
         $data['education'][] = [
            'id' => $value['id'],
            'degree' => [
               'id'   => $value['AcademicDegree']['id'],
               'name' => $value['AcademicDegree']['name'],
            ],
            'institution' => $value['institution_name'],
            'year_start' => $value['year_start'],
            'year_end' => $value['year_end'],
         ];
      }

      return $data;
   }
}
