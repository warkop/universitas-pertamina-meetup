<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResearchUserListDataResource extends JsonResource
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
            'email'             => $this->email,
            'name'              => $this->name,
            'institution_name'  => $this->institution_name,
            'department_name'   => $this->department_name,
            'confirm_at'        => $this->confirm_at,
        ];
    }
}
