<?php

// MODELO DE LA TABLA ESTADOS CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = "states";
    protected $fillable = [
        'id',
        'description'
    ];
    public $timestamps = false;
}
