<?php

// MODELO DE LA TABLA EMPRESA CON SUS COLUMNAS
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{


    protected $table = "companies";

    public function objectivesStrategics()
    {
        return $this->hasMany(ObjectivesStrategics::class, 'company_id', 'id');
    }

    protected $fillable = [
        'id',
        'unique_id',
        'logo',
        'nit',
        'businessName',
        'description',
        'mission',
        'vision',
        'phone',
        'email',
        'address',
        'city',
        'state_id'
    ];
}
