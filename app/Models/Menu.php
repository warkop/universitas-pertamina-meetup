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

     protected $with = ['subMenu'];

    public function subMenu()
    {
      $user = auth()->user();

      return $this->hasMany(Menu::class, 'sub_menu', 'id')->Join('role_menu', 'role_menu.menu_id', 'menu.id')->where('role_menu.role_id', $user->role_id);
    }
}
