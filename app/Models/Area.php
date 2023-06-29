<?php

// MODELO DE LA TABLA AREA CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = "areas";
    protected $fillable = [
        'id',
        'unique_id',
        'description',
        'company_id',
    ];
}
