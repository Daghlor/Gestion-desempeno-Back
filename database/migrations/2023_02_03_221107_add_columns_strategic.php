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
            $table->string('title', 255)->nullable()->after('unique_id');
        });

        Schema::table('objectives_individuals', function (Blueprint $table) {
            $table->string('title', 255)->nullable()->after('unique_id');
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
