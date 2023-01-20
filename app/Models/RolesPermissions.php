<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesPermissions extends Model
{
    protected $table = "roles_permissions";
    protected $fillable = [
        'id',
        'rol_id',
        'permissions_id',
    ];
}
