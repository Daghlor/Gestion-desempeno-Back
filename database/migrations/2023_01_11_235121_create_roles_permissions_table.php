<?php

// MIGRACION PARA CREAR LA TABLA PERMISOS DE ROLES CON SUS COLUMNAS Y CON SUS LLAVES FORANEAS A OTRAS TABLAS
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
        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('rol_id');
            $table->unsignedBigInteger('permissions_id');
            $table->foreign('rol_id')->references('id')->on('roles');
            $table->foreign('permissions_id')->references('id')->on('permissions');
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
        Schema::dropIfExists('roles_permissions');
    }
};
