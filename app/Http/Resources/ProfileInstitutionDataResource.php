<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileInstitutionDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id'                 => $this->id,
            'name'               => $this->name,
            'email'              => $this->email,
            'address'            => $this->address,
            'phone'              => $this->phone,
            'est'                => $this->est,
            'country'            => [
               'id'  => $this->country['id'],
               'name'=> $this->country['name'],
            ],
            'city'               => $this->city,
            'postal_code'        => $this->postal_code,
            'total_department'   => count($this->department),
            'total_member'       => 0,
            'path_photo'         => $this->path_photo,
            'updated_at'         => date('d-m-Y', strtotime($this->updated_at)),
            'department'         => [],
        ];

        foreach ($this->department as $key => $value) {
           $member = count($value->member);
           $data['department'][] = [
             'id'       => $value->id,
             'name'     => $value->name,
             'member'   => $member,
          ];

           $data['total_member'] =  $data['total_member'] + $member;
        }

        return $data;
    }
}
