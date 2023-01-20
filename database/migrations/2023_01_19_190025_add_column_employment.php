<?php

use App\Models\Employment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->string('unique_id', 50)->unique()->after('id');
            $table->unsignedBigInteger('company_id')->nullable()->after('description');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Employment::where('id', 1)->update([
            'unique_id' => Str::uuid()->toString(),
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
