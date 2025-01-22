<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataControls extends Model
{
    use HasFactory;

    public $table = 'data_controls'; 

    // Define relationships
    public function network()
    {
        return $this->belongsTo(Networks::class, 'network', 'network_id');
    }

    public function dataType()
    {
        return $this->belongsTo(DataType::class, 'data_type', 'id');
    }
}
