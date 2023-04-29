<?php

use App\Models\Permissions;
use App\Models\Roles;
use App\Models\RolesPermissions;
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
        Permissions::create([
            'id' => 1,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Empresas',
            'code' => 'list_companies'
        ]);
        Permissions::create([
            'id' => 2,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Crear Empresas',
            'code' => 'create_companies'
        ]);
        Permissions::create([
            'id' => 3,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Actualizar Empresas',
            'code' => 'update_companies'
        ]);
        Permissions::create([
            'id' => 4,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Eliminar Empresas',
            'code' => 'delete_companies'
        ]);


        Permissions::create([
            'id' => 5,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Usuarios',
            'code' => 'list_users'
        ]);
        Permissions::create([
            'id' => 6,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Crear Usuarios',
            'code' => 'create_users'
        ]);
        Permissions::create([
            'id' => 7,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Actualizar Usuarios',
            'code' => 'update_users'
        ]);
        Permissions::create([
            'id' => 8,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Eliminar Usuarios',
            'code' => 'delete_users'
        ]);


        Permissions::create([
            'id' => 9,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Cargos',
            'code' => 'list_employments'
        ]);
        Permissions::create([
            'id' => 10,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Crear Cargos',
            'code' => 'create_employments'
        ]);
        Permissions::create([
            'id' => 11,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Actualizar Cargos',
            'code' => 'update_employments'
        ]);
        Permissions::create([
            'id' => 12,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Eliminar Cargos',
            'code' => 'delete_employments'
        ]);


        Permissions::create([
            'id' => 13,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Áreas',
            'code' => 'list_areas'
        ]);
        Permissions::create([
            'id' => 14,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Crear Áreas',
            'code' => 'create_areas'
        ]);
        Permissions::create([
            'id' => 15,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Actualizar Áreas',
            'code' => 'update_areas'
        ]);
        Permissions::create([
            'id' => 16,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Eliminar Áreas',
            'code' => 'delete_areas'
        ]);


        Permissions::create([
            'id' => 17,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Objetivos Estratégicos',
            'code' => 'list_objectives_strategics'
        ]);
        Permissions::create([
            'id' => 18,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Mis Objetivos Estratégicos',
            'code' => 'list_my_objectives_strategics'
        ]);
        Permissions::create([
            'id' => 19,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Crear Objetivos Estratégicos',
            'code' => 'create_objectives_strategics'
        ]);
        Permissions::create([
            'id' => 20,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Eliminar Objetivos Estratégicos',
            'code' => 'delete_objectives_strategics'
        ]);


        Permissions::create([
            'id' => 21,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Objetivos Individuales',
            'code' => 'list_objectives_individuals'
        ]);
        Permissions::create([
            'id' => 22,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Mis Objetivos Individuales',
            'code' => 'list_my_objectives_individuals'
        ]);
        Permissions::create([
            'id' => 23,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Crear Objetivos Individuales',
            'code' => 'create_objectives_individuals'
        ]);

        Permissions::create([
            'id' => 24,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Seguimientos',
            'code' => 'list_tracings'
        ]);

        Permissions::create([
            'id' => 25,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Crear Seguimientos',
            'code' => 'create_tracings'
        ]);

        Permissions::create([
            'id' => 26,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Obtener Toda la Información',
            'code' => 'get_all_data'
        ]);

        Permissions::create([
            'id' => 27,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Roles',
            'code' => 'list_roles'
        ]);

        Permissions::create([
            'id' => 28,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Actualizar Roles',
            'code' => 'update_roles'
        ]);

        Permissions::create([
            'id' => 29,
            'unique_id' => Str::uuid()->toString(),
            'description' => 'Listar Permisos',
            'code' => 'list_permissions'
        ]);

        for ($i=1; $i < 30; $i++) {
            if($i != 18 || $i != 22){
                RolesPermissions::create([
                    'rol_id' => 3,
                    'permissions_id' => $i,
                ]);
            }
        }

        for ($i=5; $i < 26; $i++) { 
            if($i != 21 || $i != 22 || $i != 23){
                RolesPermissions::create([
                    'rol_id' => 1,
                    'permissions_id' => $i,
                ]);
            }
        }

        RolesPermissions::create([
            'rol_id' => 2,
            'permissions_id' => 22,
        ]);

        RolesPermissions::create([
            'rol_id' => 2,
            'permissions_id' => 23,
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
