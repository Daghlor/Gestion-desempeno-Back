<?php

// ESTE ES EL CONTROLADOR DE AUTH O AUTENTICACION DONDE ESTAN LAS FUNCIONES QUE AUTENTIFICAN LOS DATOS DEL USUARIO

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Company;
use App\Models\Employment;
use App\Models\ObjectivesIndividual;
use App\Models\ObjectivesStrategics;
use App\Models\Roles;
use App\Models\RolesPermissions;
use App\Models\RolesUsers;
use App\Models\State;
use App\Models\UserHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // FUNCION DE LOGIN QUE VERIFICA LOS DATOS REALES CON TOKEN, LOS COMPARA CON LA DB Y DEVUELVE SI EXISTE O NO
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                'msg' => 'Debe ingresar el email o la contraseña',
                'loged' => false,
            ), 400);
        }


        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(array(
                'msg' => 'Credendiales invalidas',
                'loged' => false,
            ), 400);
        }

        if (auth()->user()->state_id != 1) {
            return response()->json(array(
                'msg' => 'Usuario Inactivo',
                'loged' => false,
            ), 400);
        }

        auth()->attempt($credentials);
        $company = [];
        $roles = [];
        $rolesPermission = [];
        $validationsPermission = [];
        $permissions = [];
        $totalPoints = 100;

        UserHistorial::create([
            'unique_id' => Str::uuid()->toString(),
            'token' => $token,
            'user_id' => auth()->user()->id,
        ]);

        $points = ObjectivesIndividual::where('user_id', auth()->user()->id)->get(['id', 'weight']);

        if (auth()->user()->company_id) {
            $company = Company::where('id', auth()->user()->company_id)->first();
        }

        for ($i = 0; $i < count($points); $i++) {
            $totalPoints = $totalPoints - $points[$i]->weight;
        }


        $roles = RolesUsers::where('user_id', auth()->user()->id)
            ->join('roles', 'roles.id', '=', 'roles_users.rol_id')
            ->get(['roles_users.rol_id', 'unique_id', 'description']);
        // var_dump($roles);
        for ($i = 0; $i < count($roles); $i++) {
            array_push($rolesPermission, RolesPermissions::where('rol_id', $roles[$i]->rol_id)
                ->join('permissions', 'permissions.id', '=', 'roles_permissions.permissions_id')
                ->get(['permissions.id', 'permissions.unique_id', 'permissions.description', 'permissions.code']));
        }

        for ($i = 0; $i < count($rolesPermission); $i++) {
            for ($o = 0; $o < count($rolesPermission[$i]); $o++) {
                if (!in_array($rolesPermission[$i][$o]->id, $validationsPermission, true)) {
                    array_push($validationsPermission, $rolesPermission[$i][$o]->id);
                    array_push($permissions, $rolesPermission[$i][$o]);
                }
            }
        }

        return response()->json(array(
            'msg' => 'Iniciando Sesion',
            'token' => $token,
            'data' => [
                'user' => [
                    'id' =>  auth()->user()->id,
                    'unique_id' => auth()->user()->unique_id,
                    'photo' => auth()->user()->photo,
                    'name' => auth()->user()->name,
                    'lastName' => auth()->user()->lastName,
                    'identify' => auth()->user()->identify,
                    'phone' => auth()->user()->phone,
                    'email' => auth()->user()->email,
                    'address' => auth()->user()->address,
                    'dateBirth' => auth()->user()->dateBirth,
                    'verify' => auth()->user()->verify,
                    'employment_id' => auth()->user()->employment_id,
                    'points' => $totalPoints,
                    'employment' => Employment::where('id', auth()->user()->employment_id)->first(['description'])['description'],
                    'state' => State::where('id', auth()->user()->state_id)->first(['description'])['description'],
                    'created_at' => auth()->user()->created_at
                ],
                'company' => $company,
                'roles' => $roles,
                'permissions' => $permissions,
                'auth' => auth()->user()
            ],
            'expired' => env('JWT_TTL'),
            'loged' => true,
        ), 200);
    }

    // FUNCION QUE ENCUENTRA LOS DATOS DENTRO DE VARIAS TABLAS DE LA DB PARA SABER SI EXISTE UN USUARIO EN SUS REGISTROS UTILIZANDO SU ID
    public function findData(Request $request)
    {
        $employments = [];
        $areas = [];
        $strategics = [];
        $companies = [];
        $roles = [];

        $validateSuperAdmin = RolesUsers::where('user_id', auth()->user()->id)->where('rol_id', 3)->count();
        $validateAdmin = RolesUsers::where('user_id', auth()->user()->id)->where('rol_id', 1)->count();

        if ($validateSuperAdmin > 0) {
            $employments = Employment::get(['id', 'description', 'company_id']);
            $areas = Area::get(['id', 'description', 'company_id']);
            $strategics = ObjectivesStrategics::get(['id', 'title', 'company_id']);
            $companies = Company::where('state_id', 1)->get(['id', 'businessName']);
            $roles = Roles::get(['id', 'description']);
        } else {
            if (auth()->user()->company_id) {
                $employments = Employment::where('company_id', auth()->user()->company_id)->get(['id', 'description', 'company_id']);
                $areas = Area::where('company_id', auth()->user()->company_id)->get(['id', 'description', 'company_id']);
                $strategics = ObjectivesStrategics::where('company_id', auth()->user()->company_id)->get(['id', 'title', 'company_id']);
                $companies = Company::where('id', auth()->user()->company_id)->get(['id', 'businessName']);
            }

            if ($validateAdmin > 0) {
                $roles = Roles::get(['id', 'description']);
            } else {
                $roles = Roles::where('id', '!=', 3)->get(['id', 'description']);
            }
        }

        return response()->json(array(
            'companies' => $companies,
            'employments' => $employments,
            'roles' => $roles,
            'areas' => $areas,
            'strategics' => $strategics
        ), 200);
    }

    // FUNCION PARA DESLOGEARSE DEL SISTEMA
    public function logout(Request $request)
    {
        auth()->logout();

        return response()->json(array(
            'msg' => 'Se Cerró la Sesión Correctamente',
            'loged' => true,
        ), 200);
    }
}
