<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Networks extends Model
{
    use HasFactory;

    protected $primaryKey = 'network_id'; //Set primary Key
    public $incrementing = false; // Disable auto-incrementing

    protected $fillable = [
        'network_id',
        'name',
    ];

    // Define relationships
    public function dataPlans()
    {
        return $this->hasMany(DataPlans::class, 'network_id', 'network_id');
    }
    
    public function dataControls()
    {
        return $this->hasMany(DataControls::class, 'network', 'network_id');
    }

    public function topUpTransactions()
    {
        return $this->hasMany(AirtimeTransactions::class, 'network_id', 'network_id');
    }
}
