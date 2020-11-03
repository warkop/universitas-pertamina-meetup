<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberPublicationResource extends JsonResource
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
            'id' => $this->id,
            'publication_type_id' => $this->publicationType->id??null,
            'publication_type' => $this->publicationType->name??null,
            'title' => $this->title,
            'name' => $this->name,
            'year' => $this->year,
            'author' => $this->author,
        ];
    }
}
