<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class AnnouncementComment extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'announcement_comment';
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

    public function userComment()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
