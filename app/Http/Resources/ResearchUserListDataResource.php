<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Namshi\JOSE\Base64\Base64UrlSafeEncoder;

class ResearchUserListDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->status == 0) {
            $status = 'Pending';
        } else if ($this->status == 1) {
            $status = 'Active';
        } else {
            $status = 'Declined';
        }

        $link = [
            [
                'rel'   => 'detail',
                'type'  => 'GET',
                'href'  => url('api/research-user/'.$this->id)
            ],
            [
                'rel'   => 'accept',
                'type'  => 'PATCH',
                "href"  => url('api/research-user/'.$this->id)
            ],
        ];

        return [
            'id'                => $this->id,
            'email'             => $this->email,
            'name'              => $this->name,
            'institution_name'  => $this->institution_name,
            'department_name'   => $this->department_name,
            'nationality_name'  => $this->nationality_name,
            'status'            => $status,
            '_link'             => $link
        ];
    }
}
