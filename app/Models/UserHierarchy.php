<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHierarchy extends Model
{
    protected $table = 'user_hierarchy';

    protected $fillable = ['user_id', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id'); // Actualizado para reflejar el cambio en la clave foránea
    }

    public function children()
    {
        return $this->hasMany(UserHierarchy::class, 'parent_id');
    }
}