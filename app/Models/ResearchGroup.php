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

    public function memberGroup()
    {
        return $this->belongsToMany(Member::class, 'research_group_member', 'research_group_id','member_id');
    }
}
