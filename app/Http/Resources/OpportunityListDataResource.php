<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityListDataResource extends JsonResource
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
            'id'                    => $this->id,
            'name'                  => $this->name,
            'desc'                  => $this->desc,
            'contact_person'        => $this->contact_person,
            'total_funding'         => $this->total_funding,
            'opportunity_type_name' => $this->opportunityType->name,
            'institution_name'      => $this->institution->name,
            'institution_id'        => $this->institution->id,
            'institution_photo'     => $this->institution->path_photo,
            'start_date'            => $this->start_date,
            'end_date'              => $this->end_date,
            'created_at'            => date('d-m-Y H:i:s', strtotime($this->created_at)),
            'updated_at'            => date('d-m-Y H:i:s', strtotime($this->updated_at)),
        ];
    }
}
