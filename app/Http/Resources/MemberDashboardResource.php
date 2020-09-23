<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberDashboardResource extends JsonResource
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
            'department'         => [
               'id'    => $this->department['id'],
               'name'  => $this->department['name'],
               'institution' =>[
                  'id'    => $this->department['institution']['id'],
                  'name'  => $this->department['institution']['name'],
               ]
            ],
            'publication_count' => $this->publication_count,
            'created_at'            => date('d-m-Y H:i:s', strtotime($this->created_at)),
            'updated_at'            => date('d-m-Y H:i:s', strtotime($this->updated_at)),
        ];
    }
}
