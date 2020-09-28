<?php

namespace App\Http\Resources;

use App\Models\Member;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionResource extends JsonResource
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

        if ($this->creator) {
            $member = Member::find($this->creator->owner_id);
        }

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'desc'              => $this->desc,
            'creator'           => $member->name??null,
            'created_at'        => date('d-m-Y H:i:s', strtotime($this->created_at)),
            'updated_at'        => date('d-m-Y H:i:s', strtotime($this->updated_at)),
            'translate_time'    => $this->updated_at->diffForHumans(),
            'comment'           => CommentResource::collection($this->comment()->latest('id')->paginate($comment_limit)->reverse()),
            'total_comment'     => count($this->comment),
            'path_file'         => $this->path_file,
        ];
    }
}
