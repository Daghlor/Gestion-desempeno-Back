<?php

// MIGRACION PARA CREAR LA TABLA OBJETIVOS INDIVIDUALES CON SUS COLUMNAS Y LLAVES FORANEAS A OTRAS TABLAS
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
        Schema::create('objectives_individuals', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('unique_id', 50)->unique();
            $table->string('objetive', 1550);
            $table->integer('weight');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('strategic_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('state_id')->references('id')->on('states_objectives');
            $table->foreign('strategic_id')->references('id')->on('objectives_strategics');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objectives_individuals');
    }
};
