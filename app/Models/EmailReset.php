<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class EmailReset extends Model
{
    use SoftDeletes;
    use Userstamps;

    public $incrementing = false;
    protected $table = 'email_reset';

    protected $primaryKey = 'email';

    protected $fillable = [
      'email', 'token', 'deleted_at', 'deleted_by'
   ];
    protected $hidden = [
        'created_by',
        'updated_by',
    ];
}
