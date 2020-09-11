<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuDataResource extends JsonResource
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
         'order'              => $this->order,
         'icon'               => $this->icon,
         'url'                => $this->url,
         'id_element'         => $this->id_element,
         'sub_menu'           => $this->loop($this->subMenu),
      ];

      return $data;
   }

   public function loop($data_sub_menu)
   {
      $data = [];
      foreach ($data_sub_menu as $key => $value) {
         $data[] = [
            'id'                 => $value->id,
            'name'               => $value->name,
            'order'              => $value->order,
            'icon'               => $value->icon,
            'url'                => $value->url,
            'id_element'         => $value->id_element,
            'sub_menu'           => $this->loop($value->subMenu),
         ];
      }

      return $data;
   }
}
