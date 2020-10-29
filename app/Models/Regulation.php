<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Regulation extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'regulation';
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

    protected $with = ['regulationFile', 'institution'];

    public function regulationFile()
    {
        return $this->hasMany(RegulationFile::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function institutionRegulation()
    {
        return $this->belongsToMany(Institution::class);
    }

    public static function listData($options)
    {
        $result = Regulation::select(
            'regulation.*',
            'institution.name as institution_name'
        )
        ->join('institution','institution.id', '=', 'institution_id')
        ->join('institution_regulation', 'institution_regulation.regulation_id', '=', 'regulation.id')
        ;

        if ($options['institution']) {
            $result = $result->where(function() use($options, $result) {
                foreach ($options['institution'] as $institution) {
                    $result = $result->orWhere('institution_regulation.institution_id', $institution);
                }
            });
        }

        return $result;
    }
}
