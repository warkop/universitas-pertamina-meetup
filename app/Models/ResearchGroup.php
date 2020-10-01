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

    public function listOfMember($researchGroupId)
    {
        return ResearchGroupMember::join('member', 'member.id', '=', 'member_id')
        ->join('research_group', 'research_group.id', '=', 'research_group_id')
        ->where('research_group_id', $researchGroupId)
        ->get(['member.id','member.name', 'member.email','is_admin']);
    }
}
