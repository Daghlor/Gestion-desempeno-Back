<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesUsers extends Model
{
    protected $table = "roles_users";
    protected $fillable = [
        'id',
        'rol_id',
        'user_id',
    ];
    public $timestamps = false;
}
