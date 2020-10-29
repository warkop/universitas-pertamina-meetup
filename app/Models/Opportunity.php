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
        return $this->belongsToMany(Institution::class, 'opportunity_target', 'opportunity_id', 'institution_id');
    }

    public static function listData($options = [])
    {
        $result = Opportunity::distinct()
        ->select(
            'opportunity.*',
            'opportunity_type.name as opportunity_type_name',
            'institution.name as institution_name',
            'institution.id as institution_id',
            'institution.path_photo as institution_path_photo',
        )
        ->join('opportunity_type', 'opportunity_type.id', '=', 'opportunity_type_id')
        ->join('institution', 'institution.id', '=', 'institution_id')
        ->join('opportunity_target', 'opportunity_target.opportunity_id', 'opportunity.id')
        ;

        if (isset($options['profile']) && $options['profile'] == 1){
             $user = auth()->user();

             $result = $result->leftJoin('member_opportunity', 'opportunity_id', 'opportunity.id')->where('member_opportunity.member_id', $user->owner_id);
        }

        if ($options['institution']) {
            $result = $result->where(function() use($options, $result) {
                foreach ($options['institution'] as $institution) {
                    $result = $result->orWhere('opportunity_target.institution_id', $institution);
                }
            });
        }

        return $result;
    }
}
