<?php

namespace App\Http\Controllers;

use ADP\Helpers\EmailHelper;
use ADP\Helpers\PhotoHelper;
use App\Models\Company;
use App\Models\RolesUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersController extends Controller
{

    public function verify (Request $request){
        $user = auth()->user();
        $decryp = Hash::check($request->all()['code'], $user->codeVerify);

        if(!$decryp){
            return response()->json(array(
                'res'=> false,
                'data' => 'Codigo Invalido'
            ), 200);
        }

        User::where('id', $user->id)->update([
            'verify' => 1,
            'codeVerify' => null
        ]);

        return response()->json(array(
            'res'=> true,
            'data' =>  'Usuario Verificado Correctamente'
        ), 200);
    }

    public function registerPublic (Request $request){
        $validate = User::where('identify', $request->all()['identify'])
            ->orWhere('email', $request->all()['email'])
            ->orWhere('phone', $request->all()['phone'])
            ->first();

        if(isset($validate) == true){
            return response()->json(array(
                'data' => 'Ya existe un usuario con el mismo telefono, email o identificación',
                'res' => false
            ), 200);
        }

        $codeVerify = random_int(100000, 999999);
        $UrlImg = "";
        if(isset($request->all()['photo'])){
            $validator = Validator::make($request->all(),[
                'photo'  => 'required|mimes:png,jpg,jpeg|max:2048',
            ]);

            if($validator->fails()) {
                return response()->json(array(
                    'data' => 'Formato de la foto es invalido',
                    'res' => false
                ), 200);
            }

            $UrlImg = PhotoHelper::uploadImg($request->file('photo'), 'user_'.$request->all()['identify'], 'users');
        }

        $user = User::create([
            'unique_id' => Str::uuid()->toString(),
            'photo' => $UrlImg,
            'name' => $request->all()['name'],
            'lastName' => $request->all()['lastName'],
            'identify' => $request->all()['identify'],
            'phone' => $request->all()['phone'],
            'email' => $request->all()['email'],
            'password' => bcrypt($request->all()['password']),
            'address' => $request->all()['address'],
            'city' => $request->all()['city'],
            'verify' => 0,
            'codeVerify' => bcrypt($codeVerify),
            'dateBirth' => $request->all()['dateBirth'],
            'employment_id' => 2,
            'company_id' => null,
            'state_id' => 1,
        ]);

        RolesUsers::create([
            'rol_id' => 2,
            'user_id' => $user->id
        ]);

        $dataEmail = [
            'name' => $request->all()['name'].' '.$request->all()['lastName'],
            'code' => $codeVerify,
        ];

        EmailHelper::sendMail('mails.users.Verify', $dataEmail, $request->all()['email'], "Codigo de verificación - Gestion Desempeño");

        return response()->json(array(
            'res'=> true,
            'data' => [
                'unique_id' => $user->unique_id,
                'msg' => 'Usuario Creado Correctamente'
            ]
        ), 200);
    }

    //FUNCION PARA REGISTRAR UN USUARIO DESDE UN ADMINISTRADOR
    public function registerAdmin (Request $request){
        $validate = User::where('identify', $request->all()['identify'])
            ->orWhere('email', $request->all()['email'])
            ->orWhere('phone', $request->all()['phone'])
            ->first();

        if(isset($validate) == true){
            return response()->json(array(
                'data' => 'Ya existe un usuario con el mismo telefono, email o identificación',
                'res' => false
            ), 200);
        }

        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_';
        $codeVerify = random_int(100000, 999999);
        $password = substr(str_shuffle($permitted_chars), 8, 8);

        $UrlImg = "";
        if(isset($request->all()['photo'])){
            $validator = Validator::make($request->all(),[
                'photo'  => 'required|mimes:png,jpg,jpeg|max:2048',
            ]);

            if($validator->fails()) {
                return response()->json(array(
                    'data' => 'Formato de la foto es invalido',
                    'res' => false
                ), 200);
            }

            $UrlImg = PhotoHelper::uploadImg($request->file('photo'), 'user_'.$request->all()['identify'], 'users');
        }

        $user = User::create([
            'unique_id' => Str::uuid()->toString(),
            'photo' => $UrlImg,
            'name' => $request->all()['name'],
            'lastName' => $request->all()['lastName'],
            'identify' => $request->all()['identify'],
            'phone' => $request->all()['phone'],
            'email' => $request->all()['email'],
            'password' => bcrypt($password),
            'address' => $request->all()['address'],
            'city' => $request->all()['city'],
            'verify' => 0,
            'codeVerify' => bcrypt($codeVerify),
            'dateBirth' => $request->all()['dateBirth'],
            'employment_id' => $request->all()['employment_id'],
            'company_id' => $request->all()['company_id'],
            'state_id' => 1,
        ]);

        for ($i=0; $i < count($request->all()['roles']); $i++) {
            RolesUsers::create([
                'rol_id' => $request->all()['roles'][$i],
                'user_id' => $user->id
            ]);
        }

        $dataEmail = [
            'name' => $request->all()['name'].' '.$request->all()['lastName'],
            'code' => $codeVerify,
            'pass' => $password
        ];

        EmailHelper::sendMail('mails.users.Register', $dataEmail, $request->all()['email'], "Contraseña - Gestion Desempeño");
        EmailHelper::sendMail('mails.users.Verify', $dataEmail, $request->all()['email'], "Codigo de verificación - Gestion Desempeño");

        return response()->json(array(
            'res'=> true,
            'data' => [
                'unique_id' => $user->unique_id,
                'msg' => 'Usuario Creado Correctamente'
            ]
        ), 200);
    }

    //OBTIENE TODOS LOS USUARIOS REGISTRADOS
    public function findAll (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];



        $users = User::join('states', 'states.id', '=', 'users.state_id')
        ->join('employments', 'employments.id', '=', 'users.employment_id')
        ->leftjoin('companies', 'companies.id', '=', 'users.company_id');
        if(count($search) > 0){
            if(isset($search['name'])){
                $users = $users->where('users.name', 'like', '%'.$search['name'].'%')
                ->orWhere('users.lastName', 'like', '%'.$search['name'].'%');
            }
            if(isset($search['identify'])){
                $users = $users->where('users.identify', $search['identify']);
            }
            if(isset($search['employment_id'])){
                $users = $users->where('users.employment_id',$search['employment_id']);
            }
            if(isset($search['state_id'])  && $search['state_id'] != 0){
                $users = $users->where('users.state_id', $search['state_id']);
            }
        }
        $users = $users->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            'users.unique_id', 'users.name', 'users.lastName', 'users.identify', 'users.phone',
            'users.email', 'users.address', 'users.city', 'users.verify', 'users.dateBirth',
            'users.created_at', 'states.description as state', 'employments.description as employment',
            'companies.businessName as company'
        ]);

        $count = User::join('states', 'states.id', '=', 'users.state_id');
        if(count($search) > 0){
            if(isset($search['name'])){
                $count = $count->where('name', 'like', '%'.$search['name'].'%')
                ->orWhere('lastName', 'like', '%'.$search['name'].'%');
            }
            if(isset($search['identify'])){
                $count = $count->where('identify', $search['identify']);
            }
            if(isset($search['employment_id'])){
                $count = $count->where('employment_id',$search['employment_id']);
            }
            if(isset($search['state_id']) && $search['state_id'] != 0){
                $count = $count->where('users.state_id', $search['state_id']);
            }
        }
        $count = $count
        ->get([
            'users.unique_id'
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'users' => $users,
                'total' => count($count)
            ]
        ), 200);
    }

    //OBTIENE UN USUARIO MEDIANTE EL UNIQUE_ID
    public function findOne (Request $request, $uuid){
        $user = User::where('users.unique_id', $uuid)
        ->join('states', 'states.id', '=', 'users.state_id')
        ->join('employments', 'employments.id', '=', 'users.employment_id')
        ->leftjoin('companies', 'companies.id', '=', 'users.company_id')
        ->first([
            'users.id', 'users.unique_id', 'users.name', 'users.lastName', 'users.identify', 'users.phone',
            'users.email', 'users.address', 'users.city', 'users.verify', 'users.dateBirth',
            'users.created_at', 'states.description as state', 'employments.description as employment',
            'users.company_id'
        ]);

        $user->company = Company::where('id', $user->company_id)->first();
        $user->roles = RolesUsers::where('user_id', $user->id)->join('roles', 'roles.id', '=', 'roles_users.rol_id')->get(['roles.id', 'unique_id', 'description']);

        return response()->json(array(
            'res'=> true,
            'data' => $user
        ), 200);
    }

    //ACTUALIZA UN USUARIO
    public function update (Request $request, $uuid){
        $validate = User::
            where('identify', $request->all()['identify'])
            ->orWhere('email', $request->all()['email'])
            ->orWhere('phone', $request->all()['phone'])
            ->first();

        if(isset($validate) == true && $validate->unique_id != $uuid){
            return response()->json(array(
                'data' => 'Ya existe un usuario con el mismo telefono, email o identificación',
                'res' => false
            ), 200);
        }

        $user = User::where('unique_id', $uuid)->first(['id', 'unique_id','email']);
        $codeVerify = random_int(100000, 999999);

        $UrlImg = "";
        if(isset($request->all()['photo'])){
            $validator = Validator::make($request->all(),[
                'photo'  => 'required|mimes:png,jpg,jpeg|max:2048',
            ]);

            if($validator->fails()) {
                return response()->json(array(
                    'data' => 'Formato de la foto es invalido',
                    'res' => false
                ), 200);
            }

            $UrlImg = PhotoHelper::uploadImg($request->file('photo'), 'user_'.$request->all()['identify'], 'users');
        }

        User::where('unique_id', $uuid)->update([
            'photo' => $UrlImg,
            'name' => $request->all()['name'],
            'lastName' => $request->all()['lastName'],
            'identify' => $request->all()['identify'],
            'phone' => $request->all()['phone'],
            'address' => $request->all()['address'],
            'city' => $request->all()['city'],
            'dateBirth' => $request->all()['dateBirth'],
            'employment_id' => $request->all()['employment_id'],
            'company_id' => $request->all()['company_id'],
        ]);

        if($request->all()['email'] != $user->email){
            User::where('unique_id', $uuid)->update([
                'email' => $request->all()['email'],
                'verify' => 0,
                'codeVerify' => bcrypt($codeVerify),
            ]);

            $dataEmail = [
                'name' => $request->all()['name'].' '.$request->all()['lastName'],
                'code' => $codeVerify,
            ];

            EmailHelper::sendMail('mails.users.Verify', $dataEmail, $request->all()['email'], "Codigo de verificación - Gestion Desempeño");
        }

        if(count($request->all()['roles']) > 0){
            RolesUsers::where('user_id', $user->id)->delete();

            for ($i=0; $i < count($request->all()['roles']); $i++) {
                RolesUsers::create([
                    'rol_id' => $request->all()['roles'][$i],
                    'user_id' => $user->id
                ]);
            }
        }

        if(isset($request->all()['password'])){
            User::where('unique_id', $uuid)->update([
                'password' => bcrypt($request->all()['password']),
            ]);
        }

        return response()->json(array(
            'res'=> true,
            'data' => 'Usuario Actualizado Correctamente'
        ), 200);
    }

    //ELIMINAR UN USUARIO
    public function delete (Request $request, $uuid){
        User::where('unique_id', $uuid)->update([
            'state_id' => 2,
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => 'Información Eliminada Correctamente'
        ), 200);
    }

}
