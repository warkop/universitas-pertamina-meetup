<?php

namespace Database\Seeders;

use App\Models\RoleMenu;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class RoleMenuSeeder extends Seeder
{
   /**
   * Run the database seeds.
   *
   * @return void
   */
   public function run()
   {
      $roleData = [
         [
            'role_id'  => 4,
            'menu_id'  => 2,
            'action'   => 'R,U',
         ],
         [
            'role_id'  => 5,
            'menu_id'  => 2,
            'action'   => 'R,U',
         ],
         [
            'role_id'  => 6,
            'menu_id'  => 2,
            'action'   => 'R,U',
         ],
         [
            'role_id'  => 7,
            'menu_id'  => 2,
            'action'   => 'R,U',
         ],
         [
            'role_id'  => 8,
            'menu_id'  => 2,
            'action'   => 'R,U',
         ],
         [
            'role_id'  => 9,
            'menu_id'  => 2,
            'action'   => 'R,U',
         ],
         [
            'role_id'  => 10,
            'menu_id'  => 2,
            'action'   => 'R,U',
         ],
         [
            'role_id'  => 11,
            'menu_id'  => 2,
            'action'   => 'R,U',
         ],
         [
            'role_id'  => 4,
            'menu_id'  => 5,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 5,
            'menu_id'  => 5,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 6,
            'menu_id'  => 5,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 7,
            'menu_id'  => 5,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 8,
            'menu_id'  => 5,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 9,
            'menu_id'  => 5,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 10,
            'menu_id'  => 5,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 11,
            'menu_id'  => 5,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 4,
            'menu_id'  => 6,
            'action'   => 'R,U,A',
         ],
         [
            'role_id'  => 5,
            'menu_id'  => 6,
            'action'   => 'R,U,A,DE,AS',
         ],
         [
            'role_id'  => 6,
            'menu_id'  => 6,
            'action'   => 'R,U,A,DE,AS',
         ],
         [
            'role_id'  => 9,
            'menu_id'  => 6,
            'action'   => 'R',
         ],
         [
            'role_id'  => 10,
            'menu_id'  => 6,
            'action'   => 'R,DE,AS',
         ],
         [
            'role_id'  => 11,
            'menu_id'  => 6,
            'action'   => 'R,DE,AS',
         ],
         [
            'role_id'  => 4,
            'menu_id'  => 3,
            'action'   => 'R',
         ],
         [
            'role_id'  => 5,
            'menu_id'  => 3,
            'action'   => 'C,R,U,D,DE,AS',
         ],
         [
            'role_id'  => 5,
            'menu_id'  => 1,
            'action'   => 'R',
         ],
         [
            'role_id'  => 6,
            'menu_id'  => 3,
            'action'   => 'C,R,U,D,DE,AS',
         ],
         [
            'role_id'  => 7,
            'menu_id'  => 3,
            'action'   => 'R',
         ],
         [
            'role_id'  => 8,
            'menu_id'  => 3,
            'action'   => 'C,R,U,D,DE,AS',
         ],
         [
            'role_id'  => 9,
            'menu_id'  => 3,
            'action'   => 'R',
         ],
         [
            'role_id'  => 10,
            'menu_id'  => 1,
            'action'   => 'R',
         ],
         [
            'role_id'  => 10,
            'menu_id'  => 3,
            'action'   => 'R,DE,AS',
         ],
         [
            'role_id'  => 11,
            'menu_id'  => 3,
            'action'   => 'R,DE,AS',
         ],
         [
            'role_id'  => 5,
            'menu_id'  => 4,
            'action'   => 'C,R,U,D,DE',
         ],
         [
            'role_id'  => 6,
            'menu_id'  => 4,
            'action'   => 'C,R,U,D,DE',
         ],
         [
            'role_id'  => 8,
            'menu_id'  => 4,
            'action'   => 'C,R,U,D,DE',
         ],
         [
            'role_id'  => 10,
            'menu_id'  => 4,
            'action'   => 'R,DE',
         ],
         [
            'role_id'  => 11,
            'menu_id'  => 4,
            'action'   => 'R,DE',
         ],
      ];


      $menu = Menu::select()->get();

      foreach ($menu as $key => $value) {
         $roleData[] = [
            'role_id'  => 1,
            'menu_id'  => $value->id,
            'action'   => $value->action,
         ];
      }

      RoleMenu::truncate();
      RoleMenu::insert($roleData);
      // RoleMenu::Create([
      //    'role_id'    => $key['role_id'],
      //    'menu_id'  => $key['menu_id'],
      //    'action'  => $key['action']
      // ]);
   }
}
