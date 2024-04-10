<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingActions extends Model
{
    protected $table = "training_actions";
    protected $fillable = [
        'id',
        'unique_id',
        'title',
        'user_id',
        'start_date',
        'end_date',
        'state_id',
    ];
}