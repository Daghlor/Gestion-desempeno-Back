<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHistorial extends Model
{
    protected $table = "user_historials";
    protected $fillable = [
        'id',
        'unique_id',
        'token',
        'user_id',
    ];
}
