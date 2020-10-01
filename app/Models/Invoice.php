<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoice';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $dates = [
        'payment_date',
        'valid_until',
        'created_at',
        'updated_at',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
