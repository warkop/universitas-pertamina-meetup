<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileAdminDataResource extends JsonResource
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
            'name'               => strstr($this->email, '@', true),
            'email'              => $this->email,
            'type'              => $this->type,
        ];

        return $data;
    }
}
