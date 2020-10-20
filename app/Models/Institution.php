<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Institution extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'institution';
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

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function department()
    {
        return $this->hasMany(Department::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'country', 'id');
    }

    public function opportunity()
    {
        return $this->hasMany(Opportunity::class)->latest();
    }

    public function memberInstitution()
    {
        return $this->hasManyThrough(Member::class, Department::class);
    }
}
