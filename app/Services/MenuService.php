<?php
namespace App\Services;

use App\Http\Resources\SidebarMenuDataResource;
use App\Models\Menu;
use App\Models\Member;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\Institution;
use App\Models\Department;

use DB;

class MenuService
{
   public function checkMenu($user, $modelLogin, $url = false, $no_resource = false)
   {
      $dateNow = date("Y-m-d");
      if ($user->type == 0) {
         $modelInvoice = Invoice::select('package_id', 'invoice.valid_until')
                                ->where('invoice.user_id', $user->id)
                                ->where('invoice.valid_until', '>', $dateNow)
                                ->orderBy('invoice.id', 'DESC')->first();

         if (empty($modelInvoice)) {
            $idPackage = null;
         } else {
            $idPackage = $modelInvoice->package_id;

            $get_role = Role::where('type', 0)->where('package_id', $idPackage)->where('status', 1)->firstOrFail();

            $data_by_role = Menu::Select('menu.*', 'role_menu.action as action_role')
            // ->whereRaw('sub_menu is null')
            ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
            ->where('role_menu.role_id', $get_role->id);
         }
      } else if ($user->type == 1) {
         $idBuyPackage = $user->id;
         if (!$modelLogin->is_independent) {
            $dataInstitution = Department::select('user.id')
                                         ->leftJoin('institution', 'institution.id', 'department.institution_id')
                                         ->leftJoin('user', 'institution.id', 'user.owner_id')
                                         ->where('department.id', $modelLogin->department_id)
                                         ->where('user.type', 0)
                                         ->first();

            $idBuyPackage = $dataInstitution->id;
         }

         $modelInvoice = Invoice::select('package_id')
                                ->where('invoice.user_id', $idBuyPackage)
                                ->where('invoice.valid_until', '>', $dateNow)
                                ->orderBy('invoice.id', 'DESC')->first();

         if (empty($modelInvoice)) {
            $idPackage = null;
         } else {
            $idPackage = $modelInvoice->package_id;
            $type = 3;
            if ($modelLogin->is_independent == true) {
               $type = 1;
            }

            $get_role = Role::where('type', $type)->where('package_id', $idPackage)->where('status', 1)->first();

            $data_by_role = Menu::Select('menu.*', 'role_menu.action as action_role')
            // ->whereRaw('sub_menu is null')
            ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
            ->where('role_menu.role_id', $get_role->id);
         }

      } else {
         $data_by_role = Menu::Select('menu.*', 'role_menu.action as action_role')
         // ->whereRaw('sub_menu is null')
         ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
         ->where('role_menu.role_id', $user->role_id);

         $idPackage = true;
      }

      $dataReturn = [];
      if ($idPackage != null){
         $data_by_user = Menu::select('menu.*', 'role_menu_addition.action as action_role')
         // ->whereRaw('sub_menu is null')
         ->Join('role_menu_addition', 'role_menu_addition.menu_id', 'menu.id')
         ->where('role_menu_addition.user_id', $user->id);

         if ($url){
            $data_by_role = $data_by_role->where('menu.url', $url);
            $data_by_user = $data_by_user->where('menu.url', $url);
         }

         $data  = $data_by_role->union($data_by_user)->groupBy('menu.id', 'role_menu.action')->orderBy('order', 'asc')->get();

         if ($url || $no_resource){
            $dataReturn['menu'] = $this->ResourceCheckMenuRole($data);
            $dataReturn['packageEnd'] = false;
         } else {
            $dataReturn['menu'] = $this->ResourceMenuRole($data);
            $dataReturn['packageEnd'] = false;
         }
      } else {
         $dataReturn['menu'] = [];
         $dataReturn['packageEnd'] = true;
      }
      return $dataReturn;
   }

   public function ResourceMenuRole($data)
   {
      $fixData = [];
      $arrayMenuID = [];

      foreach ($data as $key => $value) {
         if ($value->sub_menu == null){
            $key = array_search($value->id, array_column($fixData, 'id'));

            if ($key === false){
               $fixData[] = [
                  'id'                 => $value->id,
                  'name'               => $value->name,
                  'order'              => $value->order,
                  'icon'               => $value->icon,
                  'url'                => $value->url,
                  'id_element'         => $value->id_element,
                  'sub_menu'           => [],
                  'action'             => explode(",", $value->action),
                  'action_role'        => explode(",", $value->action_role),
               ];
            } else {
               $actionRole = implode(",", $fixData[$key]['action_role']);
               $actionRole = $actionRole.','.$value->action_role;
               $fixData[$key]['action_role'] = explode(",", $actionRole);
            }

         } else {
            $key = array_search($value->sub_menu, array_column($fixData, 'id'));
            $keySub = array_search($value->id, array_column($fixData[$key]['sub_menu'], 'id'));

            if ($keySub === false){
               $fixSubMenu = [
                  'id'                 => $value->id,
                  'name'               => $value->name,
                  'order'              => $value->order,
                  'icon'               => $value->icon,
                  'url'                => $value->url,
                  'id_element'         => $value->id_element,
                  'sub_menu'           => [],
                  'action'             => explode(",", $value->action),
                  'action_role'        => explode(",", $value->action_role),
               ];

               $fixData[$key]['sub_menu'][] = $fixSubMenu;
            } else {
               $actionRole = implode(",", $fixData[$key]['sub_menu'][$keySub]['action_role']);
               $actionRole = $actionRole.','.$value->action_role;
               $fixData[$key]['sub_menu'][$keySub]['action_role'] = explode(",", $actionRole);
            }
         }
         $arrayMenuID[] = $value->id;
      }

      return $fixData;
   }

   public function ResourceCheckMenuRole($data)
   {
      $fixData = [];
      foreach ($data as $key => $value) {
         $key = array_search($value->id, array_column($fixData, 'id'));

         if ($key === false){
            $fixData[] = [
               'id'                 => $value->id,
               'name'               => $value->name,
               'order'              => $value->order,
               'icon'               => $value->icon,
               'url'                => $value->url,
               'id_element'         => $value->id_element,
               'sub_menu'           => [],
               'action'             => explode(",", $value->action),
               'action_role'        => explode(",", $value->action_role),
            ];
         } else {
            $actionRole = implode(",", $fixData[$key]['action_role']);
            $actionRole = $actionRole.','.$value->action_role;
            $fixData[$key]['action_role'] = explode(",", $actionRole);
         }
      }

      return $fixData;
   }

   public function getMenuRole($user, $modelLogin, $url = false, $no_resource = false)
   {
      $idBuyPackage = $user->id;
      $dateNow = date("Y-m-d");
      if (!$modelLogin->is_independent) {
         $dataInstitution = Department::select('user.id')
         ->leftJoin('institution', 'institution.id', 'department.institution_id')
         ->leftJoin('user', 'institution.id', 'user.owner_id')
         ->where('department.id', $modelLogin->department_id)
         ->where('user.type', 0)
         ->first();

         $idBuyPackage = $dataInstitution->id;
      }

      $modelInvoice = Invoice::select('package_id')
      ->where('invoice.user_id', $idBuyPackage)
      ->where('invoice.valid_until', '>', $dateNow)
      ->orderBy('invoice.id', 'DESC')->first();

      if (empty($modelInvoice)) {
         $idPackage = 1;
      } else {
         $idPackage = $modelInvoice->package_id;
      }

      $get_role = Role::where('type', 3)->where('package_id', $idPackage)->where('status', 1)->first();

      $data = Menu::Select('menu.*', 'role_menu.action as action_role')
      // ->whereRaw('sub_menu is null')
      ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
      ->where('role_menu.role_id', $get_role->id)
      ->orderBy('order', 'asc')->get();

      if ($url || $no_resource){
         return $this->ResourceCheckMenuRole($data);
      } else {
         return $this->ResourceMenuRole($data);
      }
   }
}
