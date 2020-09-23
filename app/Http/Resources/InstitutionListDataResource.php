<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionListDataResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'address'       => $this->address,
            'phone'         => $this->phone,
            'est'           => $this->est,
            'status'        => $this->status,
            'updated_at'    => date('d-m-Y', strtotime($this->updated_at)),
        ];
    }
}
