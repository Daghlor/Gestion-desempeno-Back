<?php

// MODELO DE LA TABLA OBEJTVIOS ESTRATEGICOS CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectivesStrategics extends Model
{
    protected $table = "objectives_strategics";
    use SoftDeletes;
    protected $fillable = [
        'id',
        'unique_id',
        'title',
        'mission',
        'vision',
        'totalWeight',
        'company_id',
        'user_id',
        'areas_id',
        'state_id',
        'plans_id',
    ];
}
