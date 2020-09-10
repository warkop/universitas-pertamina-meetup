<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Member extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'member';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

    // protected $with = [
    //     'department'
    // ];

    public function memberSkill()
    {
        return $this->belongsToMany(Skill::class, 'member_skill');
    }

    public function memberEducation()
    {
        return $this->hasOne(MemberEducation::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function publication()
    {
        return $this->hasMany(MemberPublication::class);
    }

    public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    {
        $user = auth()->user();

        $result = DB::table('member')
        ->select('member.*', 'institution.name as institution_name', 'department.name as department_name')
        ->join('department', 'department.id', '=', 'department_id')
        ->join('institution', 'institution.id', '=', 'institution_id')
        ->whereNull('member.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('name', 'ILIKE', '%' . $search . '%');
            });
        }

        if ($user->type == 0 or $user->type == 1) {
            $member = Member::find($user->owner_id);
            $department = Department::find($member->department_id);
            $result = $result->where('institution_id', $department->institution_id);
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
