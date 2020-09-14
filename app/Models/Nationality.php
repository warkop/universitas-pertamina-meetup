<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Nationality extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'nationality';
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
        $result = DB::table('nationality')->whereNull('nationality.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('name', 'ILIKE', '%' . $search . '%');
            });
        }

        if ($count) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
