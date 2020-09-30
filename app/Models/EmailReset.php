<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class EmailReset extends Model
{
    use SoftDeletes;
    use Userstamps;

    protected $table = 'email_reset';

    protected $primaryKey = 'id';

    protected $fillable = [
      'email', 'token', 'deleted_at', 'deleted_by', 'type', 'user_id'
   ];
    protected $hidden = [
        'created_by',
        'updated_by',
    ];
}
