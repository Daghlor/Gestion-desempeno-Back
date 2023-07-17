<?php

// MODELO DE LA TABLA OBJETIVOS INDIVIDUALES CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectivesIndividual extends Model
{
    protected $table = "objectives_individuals";
    use SoftDeletes;
    protected $fillable = [
        'id',
        'unique_id',
        'title',
        'objetive',
        'weight',
        'user_id',
        'state_id',
        'strategic_id',
        'plans_id',
    ];
}
