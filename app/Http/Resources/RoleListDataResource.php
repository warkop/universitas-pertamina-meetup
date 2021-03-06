<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleListDataResource extends JsonResource
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
            'status'        => $this->status,
            'updated_at'    => date('d-m-Y', strtotime($this->updated_at)),
            // 'menu'          => $this->roleMenu,
        ];
    }
}
