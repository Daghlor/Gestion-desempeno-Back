<?php

// MIGRACION PARA AGREGAR DATOS A VARIAS TABLAS YA CREADAS
use App\Models\Employment;
use App\Models\Roles;
use App\Models\RolesUsers;
use App\Models\State;
use App\Models\StatesObjectives;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
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
            'description' => 'Pendiente de aprobación',
        ]);
        StatesObjectives::create([
            'id' => 2,
            'description' => 'Aprobado',
        ]);
        StatesObjectives::create([
            'id' => 3,
            'description' => 'Calificado',
        ]);
        StatesObjectives::create([
            'id' => 4,
            'description' => 'Cerrado',
        ]);

        Employment::create([
            'id' => 1,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Administrador',
        ]);

        Employment::create([
            'id' => 2,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Empleado',
        ]);

        Roles::create([
            'id' => 1,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Administrador',
        ]);

        Roles::create([
            'id' => 2,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Empleado',
        ]);

        Roles::create([
            'id' => 3,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Super Administrador',
        ]);

        User::create([
            'id' => 1,
            'unique_id' => Str::uuid()->toString(),
            'photo' => '',
            'name' => 'ADMIN',
            'lastName' => 'GENERAL',
            'identify' => 1111111111,
            'phone' => 111111111111,
            'email' => 'admin@engagement.com',
            'password' => bcrypt('Engagement.2023'),
            'address' => 'NN',
            'city' => 'NN',
            'verify' => 1,
            'codeVerify' => '',
            'dateBirth' => '1999/01/01',
            'employment_id' => 1,
            'state_id' => 1
        ]);

        RolesUsers::create([
            'rol_id' => 3,
            'user_id' => 1
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
