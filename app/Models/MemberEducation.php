<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class MemberEducation extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'member_education';
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

    protected $with = ['MAcDegree'];

    public function MAcDegree()
    {
        return $this->belongsTo(MAcDegree::class);
    }
    //
    // public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    // {
    //     $result = DB::table('skill')->whereNull('skill.deleted_at');
    //
    //     if (!empty($search)) {
    //         $result = $result->where(function ($where) use ($search) {
    //             $where->where('name', 'ILIKE', '%' . $search . '%');
    //         });
    //     }
    //
    //     if ($count == true) {
    //         $result = $result->count();
    //     } else {
    //         $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
    //     }
    //
    //     return $result;
    // }
}
