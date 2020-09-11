<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectInterestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'type'  => $this->opportunityType->name??null,
            'total_funding'     => $this->total_funding,
            'contact_person'    => $this->contact_person,
            'keyword'           => $this->keyword,
            'desc'              => $this->desc,
            'start_date'        => date('d-m-Y', strtotime($this->start_date)),
            'end_date'          => date('d-m-Y', strtotime($this->end_date)),
            'duration'          => $start->diffInDays($end).' Hari',
        ];
    }
}
