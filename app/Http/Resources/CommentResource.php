<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'announcement_id'   => $this->announcement_id,
            'comment'           => $this->comment,
            'creator'           => $this->user->member->name,
            'updated_at'        => date('d-m-Y H:i:s', strtotime($this->updated_at)),
            'translate_time'    => $this->updated_at->diffForHumans(),
        ];
    }
}
