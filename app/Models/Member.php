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

    public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    {
        $result = DB::table('member')->whereNull('member.deleted_at');

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
