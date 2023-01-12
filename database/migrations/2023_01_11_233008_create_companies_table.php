<?php

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
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('unique_id', 50)->unique();
            $table->string('logo', 150)->nullable();
            $table->string('nit', 50);
            $table->string('businessName', 50);
            $table->string('description', 1550);
            $table->string('mission', 1550);
            $table->string('vision', 1555);
            $table->bigInteger('phone')->unique();
            $table->string('email', 80)->unique();
            $table->string('address', 40);
            $table->string('city', 50)->nullable();
            $table->unsignedBigInteger('state_id');
            $table->foreign('state_id')->references('id')->on('states');
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
        Schema::dropIfExists('companies');
    }
};
