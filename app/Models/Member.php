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

    protected $with = ['nationality'];

    public function memberSkill()
    {
        return $this->belongsToMany(Skill::class, 'member_skill')->where('skill.type', 1);
    }

    public function memberResearchInterest()
    {
        return $this->belongsToMany(Skill::class, 'member_skill')->where('skill.type', 0);
    }

    public function memberEducation()
    {
        return $this->hasMany(MemberEducation::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
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

    public function projectInterest()
    {
        return $this->belongsToMany(Opportunity::class, 'member_opportunity');
    }

    public static function listData()
    {
        $user = auth()->user();

        $result = Member::distinct()->select(
            'member.*',
            'institution.name as institution_name',
            'department.name as department_name',
            'nationality.name as nationality_name',
            'user.status'
        )
        ->leftJoin('department', 'department.id', '=', 'department_id')
        ->leftJoin('institution', 'institution.id', '=', 'institution_id')
        ->leftJoin('nationality', 'nationality.id', '=', 'nationality_id')
        ->join('user', 'user.owner_id', '=', 'member.id')
        ->where('user.type', 1)
        ;

        if ($user->type == 1) {
            $member = Member::find($user->owner_id);
            $department = Department::find($member->department_id);
            $result = $result->where('institution_id', $department->institution_id);
        } elseif ($user->type == 0) {
           $result = $result->where('institution_id', $user->owner_id);
        }

        $result = $result->where('is_sysadmin', '=', 'false');

        return $result->get();
    }
}
