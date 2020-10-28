<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityFileResource extends JsonResource
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
            'opportunity_id' => $this->opportunity_id,
            'name' => $this->name,
            'path' => $this->path,
            'size' => $this->size,
            'ext' => $this->ext,
            'is_image' => $this->is_image,
            'url' => url('api/opportunity/files/'. $this->id),
        ];
    }
}
