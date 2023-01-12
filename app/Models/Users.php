<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
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
        'dateBirth',
        'employment_id',
        'state_id'
    ];
}
