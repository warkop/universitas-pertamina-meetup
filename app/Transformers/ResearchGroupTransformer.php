<?php

namespace App\Transformers;

use App\Models\ResearchGroup;
use League\Fractal\TransformerAbstract;

class ResearchGroupTransformer extends TransformerAbstract
{
    /**
     * @param \App\ResearchGroup $researchGroup
     * @return array
     */
    public function transform(ResearchGroup $researchGroup)
    {
        return [
            'id' => (int) $researchGroup->id,
            'name' => $researchGroup->name,
            'desc' => $researchGroup->desc,
            'topic' => $researchGroup->topic,
        ];
    }
}
