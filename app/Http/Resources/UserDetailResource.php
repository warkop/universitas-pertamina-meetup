<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
         'name'            => $this->name,
         'path_photo'      => $this->path_photo,
         'nationality'     => $this->nationality,
         'position'        => $this->position,
         'employee_number' => $this->employee_number,
         'desc'            => $this->desc,
         'updated_at'      => date('d-m-Y', strtotime($this->updated_at)),
      ];
      
      return $data;
   }
}
