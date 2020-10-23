<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementDashboardResource extends JsonResource
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
            'name'              => $this->announcement,
            'created_at'        => date('d-m-Y H:i:s', strtotime($this->created_at)),
            'updated_at'        => date('d-m-Y H:i:s', strtotime($this->updated_at)),
            'translate_time'    => $this->updated_at->diffForHumans(),
            'path_file'         => $this->path_file,
        ];
    }
}
