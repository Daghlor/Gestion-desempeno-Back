<?php

// ESTE ES EL CONTROLADOR DE EMPRESAS DONDE ESTAN LAS FUNCIONES DE TIPO CRUD, ES UNO DE LAS MAS IMPORTANTES

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Area;
use App\Models\ColorsCompany;
use App\Models\Employment;
use App\Models\ObjectivesStrategics;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use ADP\Helpers\EmailHelper;
use ADP\Helpers\PhotoHelper;

class CompanyController extends Controller
{
    // FUNCION PARA CREAR O REGISTRAR UNA EMPRESA
    public function Create(Request $request)
    {
        $validate = Company::where('nit', $request->all()['nit'])->first();

        if (isset($validate) == true) {
            return response()->json(array(
                'data' => 'Ya existe una empresa con ese nit',
                'res' => false
            ), 200);
        }

        $UrlImg = "";
        if (isset($request->all()['logo'])) {
            $UrlImg = PhotoHelper::uploadBase64($request->all()['logo'], 'company_' . $request->all()['nit'] . '_' . $request->all()['businessName'], 'companies');
        }

        $company = Company::create([
            'unique_id' => Str::uuid()->toString(),
            'nit' => $request->all()['nit'],
            'businessName' => $request->all()['businessName'],
            'description' => $request->all()['description'],
            'mission' => $request->all()['mission'],
            'vision' => $request->all()['vision'],
            'phone' => $request->all()['phone'],
            'email' => $request->all()['email'],
            'address' => $request->all()['address'],
            'city' => $request->all()['city'],
            'state_id' => 1
        ]);

        if ($UrlImg != "") {
            Company::where('unique_id', $company->unique_id)->update([
                'logo' => $UrlImg,
            ]);
        }

        for ($i = 0; $i < count($request->all()['colors']); $i++) {
            ColorsCompany::create([
                'unique_id' => Str::uuid()->toString() . '-' . $request->all()['nit'] . '-' . $i,
                'label' => $request->all()['colors'][$i]['label'],
                'rgb' => $request->all()['colors'][$i]['rgb'],
                'hexadecimal' => $request->all()['colors'][$i]['hexadecimal'],
                'principal' => $request->all()['colors'][$i]['principal'],
                'location' => $request->all()['colors'][$i]['location'],
                'company_id' => $company->id
            ]);
        }

        return response()->json(array(
            'res' => true,
            'data' => [
                'company' => $company->unique_id,
                'msg' => 'Empresa Creada Correctamente'
            ]
        ), 200);
    }

    // FUNCION PARA TRAER O BUSCAR UNA EMPRESA
    public function FindAll(Request $request)
    {
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $companies = Company::join('states', 'states.id', '=', 'companies.state_id');
        if (count($search) > 0) {
            if (isset($search['nit'])) {
                $companies = $companies->where('nit', 'like', '%' . $search['nit'] . '%');
            }
            if (isset($search['businessName'])) {
                $companies = $companies->where('businessName', 'like', '%' . $search['businessName'] . '%');
            }
            if (isset($search['phone'])) {
                $companies = $companies->where('phone', 'like', '%' . $search['phone'] . '%');
            }
            if (isset($search['email'])) {
                $companies = $companies->where('email', 'like', '%' . $search['email'] . '%');
            }
            if (isset($search['address'])) {
                $companies = $companies->where('address', 'like', '%' . $search['address'] . '%');
            }
            if (isset($search['city'])) {
                $companies = $companies->where('city', 'like', '%' . $search['city'] . '%');
            }
            if (isset($search['state_id']) && $search['state_id'] != 0) {
                $companies = $companies->where('state_id', $search['state_id']);
            }
        }
        $companies = $companies->limit($paginate)
            ->offset(($page - 1) * $paginate)
            ->orderBy($column, $direction)
            ->get([
                'companies.unique_id', 'companies.nit', 'companies.businessName', 'companies.phone',
                'companies.email', 'companies.address', 'companies.city', 'states.description as state',
            ]);

        $count = Company::join('states', 'states.id', '=', 'companies.state_id');
        if (count($search) > 0) {
            if (isset($search['nit'])) {
                $count = $count->where('nit', 'like', '%' . $search['nit'] . '%');
            }
            if (isset($search['businessName'])) {
                $count = $count->where('businessName', 'like', '%' . $search['businessName'] . '%');
            }
            if (isset($search['phone'])) {
                $count = $count->where('phone', 'like', '%' . $search['phone'] . '%');
            }
            if (isset($search['email'])) {
                $count = $count->where('email', 'like', '%' . $search['email'] . '%');
            }
            if (isset($search['address'])) {
                $count = $count->where('address', 'like', '%' . $search['address'] . '%');
            }
            if (isset($search['city'])) {
                $count = $count->where('city', 'like', '%' . $search['city'] . '%');
            }
            if (isset($search['state_id']) && $search['state_id'] != 0) {
                $count = $count->where('state_id', $search['state_id']);
            }
        }
        $count = $count
            ->get([
                'companies.unique_id'
            ]);

        return response()->json(array(
            'res' => true,
            'data' => [
                'companies' => $companies,
                'total' => count($count)
            ]
        ), 200);
    }

    // FUNCION PARA BUSCAR UNA EMPRESA PUBLICAMENTE VERIFICANDO SU ESTADO
    public function FindAllPublic(Request $request)
    {
        $companies = Company::where('state_id', 1)->get(['id', 'businessName']);

        return response()->json(array(
            'res' => true,
            'data' => $companies
        ), 200);
    }

    // FUNCION PARA TRAER O BUSCAR UNA SOLA EMPRESA POR SU UNIQUE_ID
    public function FindOne(Request $request, $uuid)
    {
        $companies = Company::join('states', 'states.id', '=', 'companies.state_id')
            ->where('unique_id', $uuid)->first([
                'companies.id', 'companies.unique_id', 'companies.logo', 'companies.nit',
                'companies.businessName', 'companies.description', 'companies.mission',
                'companies.vision', 'companies.phone', 'companies.email', 'companies.address',
                'companies.city', 'states.description as state',
            ]);

        $companies->colors = ColorsCompany::where('company_id', $companies->id)->get();
        $companies->users = User::where('users.company_id', $companies->id)
            ->where('users.state_id', 1)
            ->join('states', 'states.id', '=', 'users.state_id')
            ->join('employments', 'employments.id', '=', 'users.employment_id')
            ->get([
                'users.id', 'users.unique_id', 'users.photo', 'users.name', 'users.lastName', 'users.identify', 'users.phone',
                'users.email', 'users.address', 'users.city', 'users.verify', 'users.dateBirth', 'users.employment_id',
                'users.created_at', 'states.description as state', 'employments.description as employment',
            ]);
        $companies->employments = Employment::where('company_id', $companies->id)->get();
        $companies->strategics = ObjectivesStrategics::where('objectives_strategics.company_id', $companies->id)
            ->where('objectives_strategics.state_id', 1)
            ->join('users', 'users.id', '=', 'objectives_strategics.user_id')
            ->join('states', 'states.id', '=', 'objectives_strategics.state_id')
            ->get([
                'objectives_strategics.id', 'objectives_strategics.unique_id', 'objectives_strategics.title',
                'objectives_strategics.mission', 'objectives_strategics.vision', 'objectives_strategics.totalWeight',
                'objectives_strategics.company_id', 'objectives_strategics.user_id', 'objectives_strategics.areas_id',
                'objectives_strategics.state_id', 'states.description as state', DB::raw("CONCAT(users.name,' ', users.lastName) AS nameUser"),
            ]);
        $companies->areas = Area::where('company_id', $companies->id)->get();


        return response()->json(array(
            'res' => true,
            'data' => $companies
        ), 200);
    }

    // FUNCION PARA ACTUALIZAR UNA EMPRESA
    public function Update(Request $request, $uuid)
    {
        $validate = Company::where('nit', $request->all()['nit'])->first();

        if (isset($validate) == true && $validate->unique_id != $uuid) {
            return response()->json(array(
                'data' => 'Ya existe una empresa con ese nit',
                'res' => false
            ), 200);
        }

        $companies = Company::where('unique_id', $uuid)->first(['id', 'unique_id', 'businessName', 'logo']);
        $UrlImg = $companies->logo;

        if (isset($request->all()['logo'])) {
            $UrlImg = PhotoHelper::uploadBase64($request->all()['logo'], 'company_' . $request->all()['nit'] . '_' . $request->all()['businessName'], 'companies');
        }

        Company::where('unique_id', $uuid)
            ->update([
                'nit' => $request->all()['nit'],
                'businessName' => $request->all()['businessName'],
                'description' => $request->all()['description'],
                'mission' => $request->all()['mission'],
                'vision' => $request->all()['vision'],
                'phone' => $request->all()['phone'],
                'email' => $request->all()['email'],
                'address' => $request->all()['address'],
                'city' => $request->all()['city'],
            ]);

        if ($UrlImg != "") {
            Company::where('unique_id', $uuid)->update([
                'logo' => $UrlImg,
            ]);
        }

        $notSaveUsers = 0;
        $notSaveEmployments = 0;
        $notSaveAreas = 0;

        for ($i = 0; $i < count($request->all()['colors']); $i++) {
            /* ColorsCompany::create([
                'unique_id' => Str::uuid()->toString().'-'.$request->all()['nit'].'-'.$i,
                'label' => $request->all()['colors'][$i]['label'],
                'rgb' => $request->all()['colors'][$i]['rgb'],
                'hexadecimal' => $request->all()['colors'][$i]['hexadecimal'],
                'principal' => $request->all()['colors'][$i]['principal'],
                'location' => $request->all()['colors'][$i]['location'],
                'company_id' => $companies->id
            ]);*/
        }


        for ($i = 0; $i < count($request->all()['users']); $i++) {
            $users = $request->all()['users'][$i];

            if ($users['create']) {
                $validate = User::where('identify', $users['identify'])
                    ->orWhere('email', $users['email'])
                    ->orWhere('phone', $users['phone'])
                    ->first();

                if (isset($validate) == false) {
                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_';
                    $codeVerify = random_int(100000, 999999);
                    $password = substr(str_shuffle($permitted_chars), 8, 8);

                    User::create([
                        'unique_id' => Str::uuid()->toString(),
                        'name' => $users['name'],
                        'lastName' => $users['lastName'],
                        'identify' => $users['identify'],
                        'phone' => $users['phone'],
                        'email' => $users['email'],
                        'password' => bcrypt($password),
                        'address' => $users['address'],
                        'city' => $users['city'],
                        'verify' => 0,
                        'codeVerify' => bcrypt($codeVerify),
                        'dateBirth' => $users['dateBirth'],
                        'employment_id' => $users['employment_id'],
                        'company_id' => $users['company_id'],
                        'state_id' => 1,
                    ]);

                    $dataEmail = [
                        'name' => $users['name'] . ' ' . $users['lastName'],
                        'code' => $codeVerify,
                        'pass' => $password
                    ];

                    EmailHelper::sendMail('mails.users.Register', $dataEmail, $users['email'], "Contraseña - Gestion Desempeño");
                    EmailHelper::sendMail('mails.users.Verify', $dataEmail, $users['email'], "Codigo de verificación - Gestion Desempeño");
                } else {
                    $notSaveUsers = $notSaveUsers + 1;
                }
            }

            if ($users['update']) {
                User::where('unique_id', $users['unique_id'])->update([
                    'name' => $users['name'],
                    'lastName' => $users['lastName'],
                    'identify' => $users['identify'],
                    'phone' => $users['phone'],
                    'address' => $users['address'],
                    'city' => $users['city'],
                    'dateBirth' => $users['dateBirth'],
                    'employment_id' => $users['employment_id'],
                    'company_id' => $users['company_id'],
                ]);
            }

            if ($users['delete']) {
                User::where('unique_id', $users['unique_id'])->update([
                    'state_id' => 2,
                ]);
            }
        }


        for ($i = 0; $i < count($request->all()['strategics']); $i++) {
            $strategics = $request->all()['strategics'][$i];

            if ($strategics['create']) {
                ObjectivesStrategics::create([
                    'unique_id' => Str::uuid()->toString(),
                    'title' => $strategics['title'],
                    'mission' => $strategics['mission'],
                    'vision' => $strategics['vision'],
                    'totalWeight' => $strategics['totalWeight'],
                    'company_id' => $strategics['company_id'],
                    'user_id' => auth()->user()->id,
                    'areas_id' => $strategics['areas_id'],
                    'state_id' => 1
                ]);
            }

            if ($strategics['delete']) {
                ObjectivesStrategics::where('unique_id', $strategics['unique_id'])->update([
                    'state_id' => 2
                ]);
            }
        }

        for ($i = 0; $i < count($request->all()['employments']); $i++) {
            $employment = $request->all()['employments'][$i];

            if ($employment['create']) {
                $validate = Employment::where('description', $employment['description'])->first();

                if (isset($validate) == false) {
                    Employment::create([
                        'unique_id' => Str::uuid()->toString(),
                        'description' => $employment['description'],
                        'company_id' => $employment['company_id'],
                    ]);
                } else {
                    $notSaveEmployments = $notSaveEmployments + 1;
                }
            }

            if ($employment['update']) {
                Employment::where('unique_id', $employment['unique_id'])->update([
                    'description' => $employment['description'],
                    'company_id' => $employment['company_id'],
                ]);
            }
        }

        for ($i = 0; $i < count($request->all()['areas']); $i++) {
            $area = $request->all()['areas'][$i];

            if ($area['create']) {
                $validate = Area::where('description', $area['description'])->first();

                if (isset($validate) == false) {
                    Area::create([
                        'unique_id' => Str::uuid()->toString(),
                        'description' => $area['description'],
                        'company_id' => $area['company_id'],
                    ]);
                } else {
                    $notSaveAreas = $notSaveAreas + 1;
                }
            }

            if ($area['update']) {
                Area::where('unique_id', $area['unique_id'])->update([
                    'description' => $area['description'],
                    'company_id' => $area['company_id'],
                ]);
            }
        }

        return response()->json(array(
            'res' => true,
            'data' => 'Empresa Actualizada Correctamente'
        ), 200);
    }

    // FUNCION PARA BORRAR O CAMBIAR DE ESTADO UNA EMPRESA
    public function Delete(Request $request, $uuid)
    {
        $company = Company::where('unique_id', $uuid)
            ->update([
                'state_id' => 2,
            ]);

        return response()->json(array(
            'res' => true,
            'data' => 'Empresa Eliminada Correctamente'
        ), 200);
    }
}
