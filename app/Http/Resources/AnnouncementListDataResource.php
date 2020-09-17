<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementListDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $comment_limit = \request()->get('comment_limit');
        return [
            'id'                => $this->id,
            'name'              => $this->announcement,
            'updated_at'        => date('d-m-Y H:i:s', strtotime($this->updated_at)),
            'translate_time'    => $this->updated_at->diffForHumans(),
            'comment'           => CommentResource::collection($this->comment()->latest('id')->paginate($comment_limit)->reverse()),
            'total_comment'     => count($this->comment),
            'path_file'         => $this->path_file,
        ];
    }
}
