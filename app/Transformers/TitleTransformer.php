<?php

namespace App\Transformers;

use App\Models\Title;
use League\Fractal\TransformerAbstract;

class TitleTransformer extends TransformerAbstract
{
    /**
     * @param \App\TitleTransformer $titleTransformer
     * @return array
     */
    public function transform(Title $titleTransformer)
    {
        return [
            'id' => $titleTransformer->id,
            'name' => $titleTransformer->name,
            'updated_at' => date('d-m-Y', strtotime($titleTransformer->updated_at)),
        ];
    }
}
