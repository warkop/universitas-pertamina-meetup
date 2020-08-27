<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Institution extends Model
{
    use SoftDeletes;

    protected $table = 'institution';
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
        $result = DB::table('institution');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('name', 'ILIKE', '%' . $search . '%');
            });
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
