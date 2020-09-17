<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Department extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'department';
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

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $with = ['institution', 'member'];

    public function member()
    {
        return $this->hasMany(Member::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
