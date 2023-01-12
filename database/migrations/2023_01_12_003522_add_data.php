<?php

use App\Models\State;
use App\Models\StatesObjectives;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        State::create([
            'id' => 1,
            'description' => 'Activo',
        ]);

        State::create([
            'id' => 2,
            'description' => 'Inactivo',
        ]);

        StatesObjectives::create([
            'id' => 1,
            'description' => 'Inactivo',
        ]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
