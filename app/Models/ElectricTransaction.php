<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_type',
        'meter_number',
        'electric_trx',
        'user_id',
        'trx',
        'provider',
        'phone',
        'amount',
        'token',
        'units',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
