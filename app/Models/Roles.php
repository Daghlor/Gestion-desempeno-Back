<?php

// MODELO DE LA TABLA ROLES CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = "roles";
    protected $fillable = [
        'id',
        'unique_id',
        'description',
    ];
    public $timestamps = false;
}
