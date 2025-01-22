<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerify extends Model
{
    use HasFactory;

    public $table = "user_verify";

    /**
     * Gets Users ID and generates a token to verify
     * email addresses
     *
     * @return void
     */
    protected $fillable = [
        'user_id',
        'token',
    ];

    /**
     * Linking the token to the user
     *
     * @return Void
     */
    public function user() {

        return $this->belongsTo(User::class);
    }
}
