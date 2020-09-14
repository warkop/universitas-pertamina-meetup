<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class ResearchGroup extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'research_group';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    {
        $result = DB::table('member')
        ->select(
            'member.*',
            'institution.name as institution_name',
            'department.name as department_name',
            'nationality.name as nationality_name',
            'user.confirm_at',
        )
        ->join('research_group_member','member.id', '=', 'research_group_member.member_id')
        ->join('research_group','research_group.id', '=', 'research_group_member.research_group_id')
        ->leftJoin('department','department.id', '=', 'member.department_id')
        ->leftJoin('institution','institution.id', '=', 'department.institution_id')
        ->leftJoin('nationality','nationality.id', '=', 'member.nationality_id')
        ->leftJoin('user','user.owner_id', '=', 'member.id')
        ->whereNull('member.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('member.name', 'ILIKE', '%' . $search . '%');
                $where->orWhere('member.email', 'ILIKE', '%' . $search . '%');
                $where->orWhere('institution.name', 'ILIKE', '%' . $search . '%');
                $where->orWhere('department.name', 'ILIKE', '%' . $search . '%');
                $where->orWhere('nationality.name', 'ILIKE', '%' . $search . '%');
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
