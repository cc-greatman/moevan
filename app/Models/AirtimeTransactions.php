<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirtimeTransactions extends Model
{
    use HasFactory;

    public $table = "top_up_transactions";

    protected $fillable = [
        'amount', 'trx', 'airtime_trx', 'network_id', 'phone', 'type', 'user_id'
    ];

    public function network()
    {
        return $this->belongsTo(Networks::class, 'network_id', 'network_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
