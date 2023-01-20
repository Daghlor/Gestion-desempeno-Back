<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    protected $table = "employments";
    protected $fillable = [
        'id',
        'unique_id',
        'description'
    ];
    public $timestamps = false;
}
