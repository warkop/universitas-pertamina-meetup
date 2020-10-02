<?php

namespace App\Transformers;

use App\Models\Opportunity;
use League\Fractal\TransformerAbstract;

class OpportunityTransformer extends TransformerAbstract
{
    /**
     * @param \App\Opportunity $opportunity
     * @return array
     */
    public function transform(Opportunity $opportunity)
    {
        return [
            'id'                    => $opportunity->id,
            'name'                  => $opportunity->name,
            'desc'                  => $opportunity->desc,
            'contact_person'        => $opportunity->contact_person,
            'total_funding'         => $opportunity->total_funding,
            'opportunity_type_name' => $opportunity->opportunity_type_name??null,
            'institution_name'      => $opportunity->institution_name??null,
            'institution_id'        => $opportunity->institution_id??null,
            'institution_photo'     => $opportunity->institution_path_photo??null,
            'start_date'            => $opportunity->start_date,
            'end_date'              => $opportunity->end_date,
            'created_at'            => date('d-m-Y H:i:s', strtotime($opportunity->created_at)),
            'updated_at'            => date('d-m-Y H:i:s', strtotime($opportunity->updated_at)),
        ];
    }
}
