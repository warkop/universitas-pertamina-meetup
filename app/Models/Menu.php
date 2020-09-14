<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Menu extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'menu';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

     // protected $with = ['subMenu'];

    public function subMenuSidebar()
    {
      $user = auth()->user();

      $data_by_role = $this->hasMany(Menu::class, 'sub_menu', 'id')
                           // ->with('subMenu')
                           ->Select('menu.*', 'role_menu.action as action_role')
                           ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
                           ->where('role_menu.role_id', $user->role_id);

      $data_by_user = $this->hasMany(Menu::class, 'sub_menu', 'id')
                           // ->with('subMenu')
                           ->select('menu.*', 'role_menu_addition.action as action_role')
                           ->Join('role_menu_addition', 'role_menu_addition.menu_id', 'menu.id')
                           ->where('role_menu_addition.user_id', $user->id);

      return $data_by_role->union($data_by_user)->groupBy('menu.id', 'role_menu.action')->orderBy('order', 'asc');
    }

    public function subMenu()
    {
      return $this->hasMany(Menu::class, 'sub_menu', 'id')->orderBy('order', 'asc');
    }
}
