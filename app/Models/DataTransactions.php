<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTransactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'trx', 'data_trx', 'plan_id', 'phone', 'user_id'
    ];

    public function dataPlan()
    {
        return $this->belongsTo(DataPlans::class, 'plan_id', 'data_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
