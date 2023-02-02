<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use ADP\Helpers\PhotoHelper;
use App\Models\Area;
use App\Models\ColorsCompany;
use App\Models\Employment;
use App\Models\ObjectivesStrategics;
use App\Models\User;

class CompanyController extends Controller
{
    public function Create (Request $request){
        $validate = Company::where('nit', $request->all()['nit'])->first();

        if(isset($validate) == true){
            return response()->json(array(
                'data' => 'Ya existe una empresa con ese nit',
                'res' => false
            ), 200);
        }

        $UrlImg = "";
        if(isset($request->all()['logo'])){
            $validator = Validator::make($request->all(),[ 
                'logo'  => 'required|mimes:png,jpg,jpeg|max:2048',
            ]);
    
            if($validator->fails()) {          
                return response()->json(array(
                    'data' => 'Formato de la foto es invalido',
                    'res' => false
                ), 200);                       
            }  
    
            $UrlImg = PhotoHelper::uploadImg($request->file('logo'), 'company_'.$request->all()['nit'].'_'.$request->all()['businessName'], 'companies');
        }

        $company = Company::create([
            'unique_id' => Str::uuid()->toString(),
            'logo' => $UrlImg,
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

        for ($i=0; $i < count($request->all()['colors']); $i++) { 
            ColorsCompany::create([
                'unique_id' => Str::uuid()->toString().'-'.$request->all()['nit'].'-'.$i,
                'label' => $request->all()['colors'][$i]['label'],
                'rgb' => $request->all()['colors'][$i]['rgb'],
                'hexadecimal' => $request->all()['colors'][$i]['hexadecimal'],
                'principal' => $request->all()['colors'][$i]['principal'],
                'location' => $request->all()['colors'][$i]['location'],
                'company_id' => $company->id
            ]);
        }
       
        return response()->json(array(
            'res'=> true,
            'data' => [
                'company' => $company->unique_id,
                'msg' => 'Empresa Creada Correctamente'
            ]
        ), 200);
    }

    public function FindAll (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $companies = Company::join('states', 'states.id', '=', 'companies.state_id');
        if(count($search) > 0){
            if(isset($search['nit'])){
                $companies = $companies->where('nit', 'like', '%'.$search['nit'].'%');
            }
            if(isset($search['businessName'])){
                $companies = $companies->where('businessName', 'like', '%'.$search['businessName'].'%');
            }
            if(isset($search['phone'])){
                $companies = $companies->where('phone', 'like', '%'.$search['phone'].'%');
            }
            if(isset($search['email'])){
                $companies = $companies->where('email', 'like', '%'.$search['email'].'%');
            }
            if(isset($search['address'])){
                $companies = $companies->where('address', 'like', '%'.$search['address'].'%');
            }
            if(isset($search['city'])){
                $companies = $companies->where('city', 'like', '%'.$search['city'].'%');
            }
            if(isset($search['state_id']) && $search['state_id'] != 0){
                $companies = $companies->where('state_id', $search['state_id']);
            }
        }
        $companies = $companies->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            'companies.unique_id', 'companies.nit', 'companies.businessName', 'companies.phone', 
            'companies.email', 'companies.address', 'companies.city', 'states.description as state',
        ]);

        $count = Company::join('states', 'states.id', '=', 'companies.state_id');
        if(count($search) > 0){
            if(isset($search['nit'])){
                $count = $count->where('nit', 'like', '%'.$search['nit'].'%');
            }
            if(isset($search['businessName'])){
                $count = $count->where('businessName', 'like', '%'.$search['businessName'].'%');
            }
            if(isset($search['phone'])){
                $count = $count->where('phone', 'like', '%'.$search['phone'].'%');
            }
            if(isset($search['email'])){
                $count = $count->where('email', 'like', '%'.$search['email'].'%');
            }
            if(isset($search['address'])){
                $count = $count->where('address', 'like', '%'.$search['address'].'%');
            }
            if(isset($search['city'])){
                $count = $count->where('city', 'like', '%'.$search['city'].'%');
            }
            if(isset($search['state_id']) && $search['state_id'] != 0){
                $count = $count->where('state_id', $search['state_id']);
            }
        }
        $count = $count
        ->get([
            'companies.unique_id'
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'companies' => $companies,
                'total' => count($count)
            ]
        ), 200);
    }

    public function FindAllPublic (Request $request){
        $companies = Company::where('state_id', 1)->get(['id', 'businessName']);

        return response()->json(array(
            'res'=> true,
            'data' => $companies
        ), 200);
    }

    public function FindOne (Request $request, $uuid){
        $companies = Company::join('states', 'states.id', '=', 'companies.state_id')
        ->where('unique_id', $uuid)->first();

        $companies->colors = ColorsCompany::where('company_id', $companies->id)->get();
        $companies->users = User::where('users.company_id', $companies->id)
        ->join('states', 'states.id', '=', 'users.state_id')
        ->join('employments', 'employments.id', '=', 'users.employment_id')
        ->get([
            'users.id', 'users.unique_id', 'users.name', 'users.lastName', 'users.identify', 'users.phone', 
            'users.email', 'users.address', 'users.city', 'users.verify', 'users.dateBirth', 
            'users.created_at', 'states.description as state', 'employments.description as employment', 
        ]);
        $companies->employments = Employment::where('company_id', $companies->id)->get();
        $companies->strategics = ObjectivesStrategics::where('company_id', $companies->id)->get();
        $companies->areas = Area::where('company_id', $companies->id)->get();


        return response()->json(array(
            'res'=> true,
            'data' => $companies
        ), 200);
    }

    public function Update (Request $request, $uuid){
        $validate = Company::where('nit', $request->all()['nit'])->first();

        if(isset($validate) == true && $validate->unique_id != $uuid){
            return response()->json(array(
                'data' => 'Ya existe una empresa con ese nit',
                'res' => false
            ), 200);
        }

        $UrlImg = "";
        if(isset($request->all()['logo'])){
            $validator = Validator::make($request->all(),[ 
                'logo'  => 'required|mimes:png,jpg,jpeg|max:2048',
            ]);
    
            if($validator->fails()) {          
                return response()->json(array(
                    'data' => 'Formato de la foto es invalido',
                    'res' => false
                ), 200);                       
            }  
    
            $UrlImg = PhotoHelper::uploadImg($request->file('logo'), 'company_'.$request->all()['nit'].'_'.$request->all()['businessName'], 'companies');
        }

        $companies = Company::where('unique_id', $uuid)->first(['id', 'unique_id','businessName']);
        $company = Company::where('unique_id', $uuid)
        ->update([
            'logo' => $UrlImg,
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

        ColorsCompany::where('company_id', $companies->id)->delete();
        for ($i=0; $i < count($request->all()['colors']); $i++) {
            ColorsCompany::create([
                'unique_id' => Str::uuid()->toString().'-'.$request->all()['nit'].'-'.$i,
                'label' => $request->all()['colors'][$i]['label'],
                'rgb' => $request->all()['colors'][$i]['rgb'],
                'hexadecimal' => $request->all()['colors'][$i]['hexadecimal'],
                'principal' => $request->all()['colors'][$i]['principal'],
                'location' => $request->all()['colors'][$i]['location'],
                'company_id' => $companies->id
            ]);
        }
       
        return response()->json(array(
            'res'=> true,
            'data' => 'Empresa Actualizada Correctamente'
        ), 200);
    }

    public function Delete (Request $request, $uuid){
        $company = Company::where('unique_id', $uuid)
        ->update([
            'state_id' => 2,
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => 'Empresa Eliminada Correctamente'
        ), 200);
    }
}
