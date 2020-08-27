<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
    use SoftDeletes;

    protected $table = 'department';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

    public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    {
        $result = DB::table('department')->select(
            'department.*',
            'institution.name as institution_name'
        )
        ->leftJoin('institution', 'institution.id', '=', 'department.institution_id')
        ;

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('department.name', 'ILIKE', '%' . $search . '%');
                $where->orWhere('institution.name', 'ILIKE', '%' . $search . '%');
            });
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }

    public function institution()
    {
        return $this->hasOne(Institution::class);
    }
}
