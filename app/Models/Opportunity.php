<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Opportunity extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'opportunity';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

    protected $with = [
        'institution',
        'opportunityType',
        'institutionTarget',
        'interest',
        'files',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function interest()
    {
        return $this->belongsToMany(Member::class, 'member_opportunity', 'member_id', 'opportunity_id');
    }

    public function opportunityType()
    {
        return $this->belongsTo(OpportunityType::class);
    }

    public function files()
    {
        return $this->hasMany(OpportunityFile::class);
    }

    public function institutionTarget()
    {
        return $this->belongsToMany(Institution::class, 'opportunity_target', 'institution_id', 'opportunity_id');
    }

    public static function listData($start, $length, $search = '', $count = false, $sort, $field, $options = [])
    {
        $result = DB::table('opportunity')
        ->select(
            'opportunity.*',
            'opportunity_type.name as opportunity_type_name',
            'institution.name as institution_name'
        )
        ->join('opportunity_type', 'opportunity_type.id', '=', 'opportunity_type_id')
        ->join('institution', 'institution.id', '=', 'institution_id')
        ->whereNull('opportunity.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('name', 'ILIKE', '%' . $search . '%');
            });
        }

        if (isset($options['profile'])){
           if ($options['profile'] == 1){
             $user = auth()->user();

             $result = $result->leftJoin('member_opportunity', 'opportunity_id', 'opportunity.id')->where('member_opportunity.member_id', $user->owner_id);
           }
       }

        if ($count) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
