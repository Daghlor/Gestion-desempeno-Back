<?php

// ESTE ES EL CONTROLADOR DE ROLES DONDE ESTAN LAS FUNCIONES DE TIPO CRUD

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\RolesPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    // FUNCION PARA CREAR O REGISTRAR LOS ROLES
    public function Create(Request $request)
    {
        $validate = Roles::where('description', $request->all()['description'])->first();

        if (isset($validate) == true) {
            return response()->json(array(
                'data' => 'Ya existe un rol con la misma descripción',
                'res' => false
            ), 200);
        }

        $rol = Roles::create([
            'unique_id' => Str::uuid()->toString(),
            'description' => $request->all()['description']
        ]);

        for ($i = 0; $i < count($request->all()['permissions']); $i++) {
            RolesPermissions::create([
                'rol_id' => $rol->id,
                'permissions_id' => $request->all()['permissions'][$i]
            ]);
        }

        return response()->json(array(
            'res' => true,
            'data' => [
                'roles' => $rol->unique_id,
                'msg' => 'Información Creada Correctamente'
            ]
        ), 200);
    }

    // FUNCION PARA TRAER O BUSCAR TODOS LOS ROLES
    public function FindAll(Request $request)
    {
        $roles = Roles::orderBy('description', 'asc')->get();

        return response()->json(array(
            'res' => true,
            'data' => $roles,
        ), 200);
    }

    // FUNCIOM PARA BUSCAR UN SOLO ROL POR SU UNIQUE_ID
    public function FindOne(Request $request, $uuid)
    {
        $roles = Roles::where('unique_id', $uuid)->first();

        $roles->permissions = RolesPermissions::where('rol_id', $roles->id)
            ->join('permissions', 'permissions.id', '=', 'roles_permissions.permissions_id')
            ->get(['permissions.unique_id', 'permissions.description', 'permissions.code']);

        return response()->json(array(
            'res' => true,
            'data' => $roles,
        ), 200);
    }

    // FUNCION PARA ACTUALIAZAR UN ROL
    public function Update(Request $request, $uuid)
    {
        $validate = Roles::where('description', $request->all()['description'])->first();

        if (isset($validate) == true && $validate->unique_id != $uuid) {
            return response()->json(array(
                'data' => 'Ya existe un rol con la misma descripción',
                'res' => false
            ), 200);
        }

        $rol = Roles::where('unique_id', $uuid)->first(['id']);
        Roles::where('unique_id', $uuid)->update([
            'description' => $request->all()['description'],
        ]);

        RolesPermissions::where('rol_id', $rol->id)->delete();
        for ($i = 0; $i < count($request->all()['permissions']); $i++) {
            RolesPermissions::create([
                'rol_id' => $rol->id,
                'permissions_id' => $request->all()['permissions'][$i]
            ]);
        }

        return response()->json(array(
            'res' => true,
            'data' => 'Rol Actualizado Correctamente'
        ), 200);
    }
}
