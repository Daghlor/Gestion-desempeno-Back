<?php

// MODELO DE LA TABLA PERMISOS CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $table = "permissions";
    protected $fillable = [
        'id',
        'unique_id',
        'description',
        'code'
    ];
    public $timestamps = false;
}
