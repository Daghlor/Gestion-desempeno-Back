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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('unique_id', 50)->unique();
            $table->string('photo', 150)->nullable();
            $table->string('name', 25);
            $table->string('lastName', 25);
            $table->bigInteger('identify')->unique();
            $table->bigInteger('phone')->unique();
            $table->string('email', 80)->unique();
            $table->string('password', 150);
            $table->string('address', 40);
            $table->string('city', 50)->nullable();
            $table->integer('verify');
            $table->string('codeVerify', 120)->nullable();
            $table->string('dateBirth', 40)->nullable();
            $table->unsignedBigInteger('employment_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('state_id');
            $table->foreign('employment_id')->references('id')->on('employments');
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('users');
    }
};
