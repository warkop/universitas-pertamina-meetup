<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegulationListDataResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'code'              => $this->code,
            'institution_name'  => $this->institution_name,
            'regulator'         => $this->regulator,
            'created_at'        => date('d-m-Y', strtotime($this->created_at)),
            'updated_at'        => date('d-m-Y', strtotime($this->updated_at)),
        ];
    }
}
