<?php

// MODELO DE LA TABLA SEGUIMIENTOS CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tracing extends Model
{
    protected $table = "tracings";
    use SoftDeletes;
    protected $fillable = [
        'id',
        'unique_id',
        'comment',
        'user_id',
        'individual_id',
        'plans_id',
        'weight',
        'comment_employee',
        'user_id_jefe',
        'user_id_empleado',
    ];
}
