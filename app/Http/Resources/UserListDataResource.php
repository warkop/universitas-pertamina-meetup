<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserListDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'email'         => $this->email,
            'status'        => $this->status,
            'member'        => $this->detail($this->member),
            'updated_at'    => date('d-m-Y', strtotime($this->updated_at)),
            // 'menu'          => $this->roleMenu,
        ];
    }

    public function detail($data_detail)
    {
      return [
         'name'            => $data_detail->name,
         'path_photo'      => $data_detail->path_photo,
         'nationality'         => [
            'id'    => $data_detail->nationality['id'],
            'name'  => $data_detail->nationality['name'],
         ],
         'position'        => $data_detail->position,
         'employee_number' => $data_detail->employee_number,
         'desc'            => $data_detail->desc,
         'updated_at'      => date('d-m-Y', strtotime($data_detail->updated_at)),
      ];
   }
}
