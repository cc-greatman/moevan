<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Providers extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Define a one-to-many relationship with ProvidersSubscription.
     */
    public function plans()
    {
        return $this->hasMany(ProvidersSubscription::class, 'providers');
    }

    public function cableTransactions()
    {
        return $this->hasMany(CableTransactions::class, 'service_id', 'id');
    }
}
