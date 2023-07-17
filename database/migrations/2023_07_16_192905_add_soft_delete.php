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
        Schema::table('objectives_strategics', function (Blueprint $table) {
            $table->softDeletes();          
        });

        Schema::table('objectives_individuals', function (Blueprint $table) {
            $table->softDeletes();          
        });

        Schema::table('tracings', function (Blueprint $table) {
            $table->softDeletes();          
        });

        Schema::table('performance_plans', function (Blueprint $table) {
            $table->softDeletes();          
        });
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
