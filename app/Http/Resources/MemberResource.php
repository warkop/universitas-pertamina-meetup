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
            'name'  => $this->name,
            'email' => $this->email,
            'desc' => $this->desc,
            'position' => $this->position,
            'orcid_id' => $this->orcid_id,
            'scopus_id' => $this->scopus_id,
            'web' => $this->web,
            'employee_number' => $this->employee_number,
            'education' => MemberEducationResource::collection($this->memberEducation),
            'skill' => $this->memberSkill->makeHidden(['pivot', 'created_at', 'updated_at', 'type']),
            'research_interest' => $this->memberResearchInterest->makeHidden(['pivot', 'created_at', 'updated_at', 'type']),
            'publication' => MemberPublicationResource::collection($this->publication),
            'project_interest' => ProjectInterestResource::collection($this->projectInterest),
        ];
    }
}
