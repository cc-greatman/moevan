<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentDataPlanPrice extends Model
{
    use HasFactory;

    protected $table = 'agent_data_plan_prices';

    protected $fillable = [
        'agent_id',
        'data_id',
        'price',
    ];
}
