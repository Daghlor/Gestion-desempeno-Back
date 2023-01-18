<?php

namespace App\Http\Controllers;

use ADP\Helpers\EmailHelper;
use App\Models\RolesUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class UsersController extends Controller
{
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

        
        $user = User::create([
            'unique_id' => Str::uuid()->toString(),
            //'photo' => $request->all()['nombres'],
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

        //EmailHelper::sendMail('mails.users.Register', $dataEmail, $request->all()['email'], "Contraseña - Gestion Desempeño");
        //EmailHelper::sendMail('mails.users.Verify', $dataEmail, $request->all()['email'], "Codigo de verificación - Gestion Desempeño");*/

        return response()->json(array(
            'res'=> true,
            'data' => [
                //'id' => $user->id
                'roles' => $request->all()['roles'],
            ]
        ), 200);
    }
}
