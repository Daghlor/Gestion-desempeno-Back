<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatesObjectives extends Model
{
    protected $table = "states_objectives";
    protected $fillable = [
        'id',
        'description'
    ];
    public $timestamps = false;
}
