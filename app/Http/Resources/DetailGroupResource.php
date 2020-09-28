<?php

namespace App\Http\Resources;

use App\Models\Member;
use App\Models\ResearchGroupMember;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth()->user();
        $status_join = '';
        if ($user->type == 1) {
            $researchGroupMember = ResearchGroupMember::where(['research_group_id' => $this->id, 'member_id' => $user->owner_id])->first();
            if ($researchGroupMember) {
                $status_join = 'joined';
            }
        }


        if ($this->creator) {
            $member = Member::find($this->creator->owner_id);
        }

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'desc'          => $this->desc,
            'topic'         => $this->topic,
            'created_at'    => date('d-m-Y H:i:s', strtotime($this->created_at)),
            'creator'       => $member->name ?? null,
            'status_join'   => $status_join,
        ];
    }
}
