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
        Schema::create('tracings', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('unique_id', 50)->unique();
            $table->string('comment', 550);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('individual_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('individual_id')->references('id')->on('objectives_individuals');
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
        Schema::dropIfExists('tracings');
    }
};
