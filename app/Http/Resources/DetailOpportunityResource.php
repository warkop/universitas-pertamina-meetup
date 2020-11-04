<?php

namespace App\Http\Resources;

use App\Models\Member;
use App\Models\MemberOpportunity;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailOpportunityResource extends JsonResource
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
        $status_interest = '';
        if ($user->type == 1) {
            $member = Member::find($user->owner_id);
            $exists = $member->projectInterest()->where('opportunity_id', $this->id)->exists();
            if ($exists) {
                $status_interest = true;
            }
        }

        return [
            'id' => $this->id,
            'institution_id' => $this->institution_id,
            'name' => $this->name,
            'desc' => $this->desc,
            'total_funding' => $this->total_funding,
            'contact_person' => $this->contact_person,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'opportunity_type_id' => $this->opportunity_type_id,
            'target' => $this->target,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'keyword' => $this->keyword,
            'contact_person_email' => $this->contact_person_email,
            'deadline' => $this->deadline,
            'institution' => $this->institution,
            'opportunity_type' => $this->opportunityType,
            'institution_target' => $this->institutionTarget,
            'interest' => $this->interest,
            'files' => $this->files,
            'status_interest' => $status_interest,
        ];
    }
}
