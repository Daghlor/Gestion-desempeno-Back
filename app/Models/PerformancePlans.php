<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformancePlans extends Model
{
    protected $table = "performance_plans";
    protected $fillable = [
        'id',
        'unique_id',
        'name',
        'term',
        'dateInit',
        'dateEnd',
        'state',
        'company_id'
    ];
}
