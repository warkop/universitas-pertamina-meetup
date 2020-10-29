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
}
