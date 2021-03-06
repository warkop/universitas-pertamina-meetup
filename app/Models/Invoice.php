<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Userstamps;

    protected $table = 'invoice';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
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

    public function getLastInvoice(User $user)
    {
        return Invoice::where(['user_id' => $user->id])->latest()->first();
    }

    public function getLastPaidedInvoice(User $user)
    {
        return Invoice::where(['user_id' => $user->id])->whereNotNull('valid_until')->latest()->first();
    }

    public function getUnpaid(User $user)
    {
        return Invoice::where('user_id', $user->id)
        ->whereNull('payment_date')
        ->whereNull('valid_until')
        ->latest()
        ->first();
    }
}
