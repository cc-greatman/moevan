<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = ['referrer_id', 'user_id'];

    public function referrer() {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
