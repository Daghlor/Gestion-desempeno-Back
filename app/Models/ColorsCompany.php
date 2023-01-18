<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorsCompany extends Model
{
    protected $table = "colors_companies";
    protected $fillable = [
        'unique_id',
        'label',
        'rgb',
        'hexadecimal',
        'principal',
        'location',
        'company_id',
    ];
    public $timestamps = false;
}
