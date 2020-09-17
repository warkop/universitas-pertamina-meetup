<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Wildside\Userstamps\Userstamps;

class Announcement extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'announcement';
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

    public function comment()
    {
        return $this->hasMany(AnnouncementComment::class);
    }
}
