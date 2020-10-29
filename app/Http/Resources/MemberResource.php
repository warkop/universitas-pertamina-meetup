<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'title'              =>  [
                'id'   => $this->title->id??null,
                'name'  => $this->title->name??null,
             ],
            'name'  => $this->name,
            'email' => $this->email,
            'desc' => $this->desc,
            'position' => $this->position,
            'orcid_id' => $this->orcid_id,
            'scopus_id' => $this->scopus_id,
            'path_photo'         => $this->path_photo,
            'updated_at'         => date('d-m-Y', strtotime($this->updated_at)),
            'web' => $this->web,
            'employee_number' => $this->employee_number,
            'department'         => [
                'id'    => $this->department->id??null,
                'name'  => $this->department->name??null,
                'institution' => [
                   'id'    => $this->department->institution->id??null,
                   'name'  => $this->department->institution->name??null,
                ]
             ],
            'nationality'         => $this->nationality->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at'])??null,
            'education' => MemberEducationResource::collection($this->memberEducation),
            'skill' => $this->memberSkill->makeHidden(['pivot', 'created_at', 'updated_at', 'type']),
            'research_interest' => $this->memberResearchInterest->makeHidden(['pivot', 'created_at', 'updated_at', 'type']),
            'publication' => MemberPublicationResource::collection($this->publication),
            'project_interest' => ProjectInterestResource::collection($this->projectInterest),
        ];
    }
}
