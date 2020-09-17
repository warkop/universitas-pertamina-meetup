<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Department extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'department';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $with = ['institution', 'member'];

    public function member()
    {
        return $this->hasMany(Member::class);
    }

    public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    {
        $result = DB::table('department')->select(
            'department.*',
            'institution.name as institution_name'
        )
        ->leftJoin('institution', 'institution.id', '=', 'department.institution_id')
        ->whereNull('department.deleted_at')
        ;

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('department.name', 'ILIKE', '%' . $search . '%');
                $where->orWhere('institution.name', 'ILIKE', '%' . $search . '%');
            });
        }

        if (isset($options['institution_id'])){
           $result = $result->where('institution_id', $options['institution_id']);
        }

        if ($count) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
