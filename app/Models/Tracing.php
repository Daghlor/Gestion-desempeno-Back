<?php

// MODELO DE LA TABLA SEGUIMIENTOS CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracing extends Model
{
    protected $table = "tracings";
    protected $fillable = [
        'id',
        'unique_id',
        'comment',
        'user_id',
        'individual_id',
    ];
}
