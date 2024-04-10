<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackActions extends Model
{
    protected $table = "feeback_actions";
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