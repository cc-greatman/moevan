<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\returnSelf;

class DataPlans extends Model
{
    use HasFactory;

    protected $primaryKey = 'data_id'; //Set primary Key
    public $incrementing = false; // Disable auto-incrementing

    protected $fillable = [
        'data_id', 'name', 'network_id', 'type', 'price', 'size', 'validity',
        // Add other fillable attributes as needed
    ];

    // Define relationships if any
    public function network()
    {
        return $this->belongsTo(Networks::class, 'network_id', 'network_id');
    }

    public function dataType()
    {
        return $this->belongsTo(DataType::class, 'type', 'id');
    }

    public function dataTransactions()
    {
        return $this->hasMany(DataTransactions::class, 'plan_id', 'data_id');
    }

    public function agentPrices()
{
    return $this->hasMany(AgentDataPlanPrice::class);
}
}

