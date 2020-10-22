<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Role extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'role';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $with = ['roleMenu'];

    public function roleMenu()
    {
        return $this->belongsToMany(Menu::class, 'role_menu')->select('menu.*','role_menu.action as action_role')->orderBy('order', 'asc');
    }

    public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    {
        $result = Role::whereNull('role.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('name', 'ILIKE', '%' . $search . '%');
            });
        }

        if (isset($options['active_only'])){
           if ($options['active_only'] == 1){
             $result = $result->where('status', 1);
          }
        }

        if ($count) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
