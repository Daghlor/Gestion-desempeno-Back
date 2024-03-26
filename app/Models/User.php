<?php

// MODELO DE LA TABLA USUARIOS CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = "users";
    protected $fillable = [
        'id',
        'unique_id',
        'photo',
        'name',
        'lastName',
        'identify',
        'phone',
        'email',
        'password',
        'address',
        'city',
        'verify',
        'codeVerify',
        'dateBirth',
        'employment_id',
        'company_id',
        'state_id'
    ];

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'roles_users', 'user_id', 'rol_id');
    }

    public function hierarchy()
    {
        // Lógica para recuperar la jerarquía del usuario
        // Por ejemplo:
        return $this->hasOne(UserHierarchy::class, 'user_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}