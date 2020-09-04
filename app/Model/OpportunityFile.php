<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class OpportunityFile extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'opportunity_file';
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
}
