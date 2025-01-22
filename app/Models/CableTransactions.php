<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CableTransactions extends Model
{
    use HasFactory;

    public function providers()
    {
        return $this->belongsTo(Providers::class, 'service_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
