<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectivesStrategics extends Model
{
    protected $table = "objectives_strategics";
    protected $fillable = [
        'id',
        'unique_id',
        'mission',
        'vision',
        'totalWeight',
        'company_id',
        'user_id',
        'areas_id',
        'state_id'
    ];
}
