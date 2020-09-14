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
         'action'             => ($this->action != null)? $this->action_list($this->action) : null,
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
            // 'sub_menu'           => ($value->subMenuSidebar != null)? $this->loop($value->subMenu) : null,
            'action'             => ($value->action != null)? $this->action_list($value->action) : null,
         ];
      }

      return $data;
   }

   public function action_list($data_action)
   {
      $array_raw = ['C','R','U','D','I','A','SA'];
      $array_detail = ['create', 'read', 'update', 'delete', 'invite', 'approve', 'select_admin'];
      $data = [];
      $arrayAction = explode(",", $data_action);
      foreach ($arrayAction as $key => $value) {
         $key = array_search($value, $array_raw);

         $data[$array_detail[$key]] = True;
      }

      return $data;
   }
}
