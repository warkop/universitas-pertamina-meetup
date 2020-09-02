<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class RegulationFile extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'regulation_file';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];
}
