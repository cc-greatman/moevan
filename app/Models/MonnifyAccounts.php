<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonnifyAccounts extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_reference',
        'account_name',
        'account_number',
        'bank_code'
    ];

    public function user() {

       return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
