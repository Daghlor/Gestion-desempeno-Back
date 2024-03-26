<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permissions;
use App\Models\Roles;
use App\Models\RolesPermissions;
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
        Permissions::create([
            'id' => 34,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Jerarquia Jefe',
            'code' => 'create_first_hierarchy'
        ]);

        RolesPermissions::create([
            'rol_id' => 3,
            'permissions_id' => 34,
        ]);
    }



};