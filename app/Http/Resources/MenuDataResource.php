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
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'order'              => $this->order,
            'icon'               => $this->icon,
            'url'                => $this->url,
            'id_element'         => $this->id_element,
            'sub_menu'           => MenuDataResource::collection($this->subMenu),
        ];
   }
}
