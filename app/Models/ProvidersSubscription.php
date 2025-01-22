<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvidersSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'providers',
        'name',
        'amount',
        'variation_code',
    ];

    /**
     * Define the inverse of the one-to-many relationship with TVProvider.
     */
    public function tvProvider()
    {
        return $this->belongsTo(Providers::class, 'providers');
    }
}
