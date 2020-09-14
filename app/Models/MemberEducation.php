<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    protected $with = ['AcademicDegree'];

    public function AcademicDegree()
    {
        return $this->belongsTo(AcademicDegree::class);
    }
}
