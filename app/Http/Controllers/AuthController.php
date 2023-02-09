<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employment;
use App\Models\RolesUsers;
use App\Models\State;
use App\Models\UserHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(array(
                'msg'=> 'Debe ingresar el email o la contraseña',
                'loged' => false,
            ), 400);
        }


        if(! $token = JWTAuth::attempt($credentials)){
            return response()->json(array(
                'msg'=> 'Credendiales invalidas',
                'loged' => false,
            ), 400);
        }

        if(auth()->user()->state_id != 1){
            return response()->json(array(
                'msg'=> 'Usuario Inactivo',
                'loged' => false,
            ), 400);
        }

        auth()->attempt($credentials);
        $company = [];
        $roles = [];
        $permissions = [];


        UserHistorial::create([
            'unique_id' => Str::uuid()->toString(),
            'token' => $token,
            'user_id' => auth()->user()->id,
        ]);
        
        if(auth()->user()->company_id){
            $company = Company::where('id', auth()->user()->company_id)->first();
        }

        $roles = RolesUsers::where('user_id', auth()->user()->id)
        ->join('roles', 'roles.id', '=', 'roles_users.user_id')->get(['roles.id', 'unique_id','description']);

        return response()->json(array(
            'msg'=> 'Iniciando Sesion',
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
                    'employment' => Employment::where('id', auth()->user()->employment_id)->first(['description'])['description'],
                    'state' => State::where('id', auth()->user()->state_id)->first(['description'])['description'],
                    'created_at' => auth()->user()->created_at
                ],
                'company' => $company,
                'roles' => $roles

            ],
            'expired' => env('JWT_TTL'),
            'loged' => true,
        ), 200);

    }

    public function logout(Request $request)
    {
        auth()->logout();
        
        return response()->json(array(
            'msg'=> 'Se Cerró la Sesión Correctamente',
            'loged' => true,
        ), 400);
    }
}
