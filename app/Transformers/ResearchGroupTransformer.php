<?php

namespace App\Transformers;

use App\Models\ResearchGroup;
use App\Models\ResearchGroupMember;
use League\Fractal\TransformerAbstract;

class ResearchGroupTransformer extends TransformerAbstract
{
    /**
     * @param \App\ResearchGroup $researchGroup
     * @return array
     */
    public function transform(ResearchGroup $researchGroup)
    {
        $user = auth()->user();
        $status_join = '';
        if ($user->type == 1) {
            $researchGroupMember = ResearchGroupMember::where(['research_group_id' => $researchGroup->id, 'member_id' => $user->owner_id])->first();
            if ($researchGroupMember) {
                $status_join = 'joined';
            }
        }

        return [
            'id' => (int) $researchGroup->id,
            'name' => $researchGroup->name,
            'desc' => $researchGroup->desc,
            'topic' => $researchGroup->topic,
            'status_join'   => $status_join,
        ];
    }
}
