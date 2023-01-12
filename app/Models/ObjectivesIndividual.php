<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectivesIndividual extends Model
{
    protected $table = "objectives_individuals";
    protected $fillable = [
        'id',
        'unique_id',
        'objetive',
        'weight',
        'user_id',
        'state_id',
        'strategic_id',
    ];
}
