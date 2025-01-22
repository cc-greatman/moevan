<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    //Define relationships as needed
    public function dataPlans()
    {
        return $this->hasMany(DataPlans::class, 'type', 'id');
    }
    
    public function dataControls()
    {
        return $this->hasMany(DataControls::class, 'data_type', 'id');
    }
}
